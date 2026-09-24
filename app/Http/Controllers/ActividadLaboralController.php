<?php

namespace App\Http\Controllers;

use App\Models\ActividadLaboral;
use App\Models\ValorActividad;
use App\Models\Trabajador;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ActividadLaboralController
 *
 * Gestiona el registro diario de labores agrícolas realizadas por los trabajadores.
 * Cada actividad vincula un trabajador, un lote, una tarifa vigente, una fecha
 * y una cantidad ejecutada. El ciclo de vida de una actividad pasa por tres
 * estados: pendiente → confirmado | rechazado.
 *
 * Rutas asociadas (resource 'actividades' + ruta extra de confirmación):
 *   GET    /actividades              → index()
 *   GET    /actividades/create       → create()
 *   POST   /actividades              → store()
 *   GET    /actividades/{id}/edit    → edit()
 *   PUT    /actividades/{id}         → update()
 *   DELETE /actividades/{id}         → destroy()
 *   PATCH  /actividades/{id}/confirmar → confirmar()
 */
class ActividadLaboralController extends Controller
{
    /**
     * index()
     * Carga y muestra todas las actividades laborales con sus relaciones
     * (tarifa → tipo de actividad, lote, trabajador → cargo), ordenadas
     * de más reciente a más antigua. También calcula los contadores
     * de estado para las tarjetas del encabezado de la vista.
     */
    public function index()
    {
        $actividades = ActividadLaboral::with([
            'valorActividad.tipoActividad',
            'lote',
            'trabajador.cargo',
        ])
        ->orderBy('fecha', 'desc')
        ->orderBy('id', 'desc')
        ->get();

        // Contadores para las tarjetas de resumen del tope de la vista
        $pendientes  = $actividades->where('estado_confirmacion', 'pendiente')->count();
        $confirmadas = $actividades->where('estado_confirmacion', 'confirmado')->count();
        $rechazadas  = $actividades->where('estado_confirmacion', 'rechazado')->count();

        // Datos para el modal de crear actividad
        $tarifas = ValorActividad::with('tipoActividad')
            ->where('estado', 'activo')
            ->where('fecha_inicio', '<=', now())
            ->where(function ($q) {
                $q->whereNull('fecha_fin')
                  ->orWhere('fecha_fin', '>=', now());
            })
            ->orderBy('id')
            ->get();

        $trabajadores = Trabajador::with('cargo')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        $lotes = Lote::orderBy('nombre')->get();

        return view('admin.actividades.index', compact(
            'actividades', 'pendientes', 'confirmadas', 'rechazadas',
            'tarifas', 'trabajadores', 'lotes'
        ));
    }

    /**
     * create()
     * Prepara el formulario de nueva actividad.
     * Solo carga tarifas activas y vigentes a la fecha de hoy para evitar
     * que se registren actividades con tarifas vencidas o inactivas.
     * Solo carga trabajadores con estado 'activo'.
     */
    /**
     * store()
     * Valida y persiste una nueva actividad laboral.
     * - La fecha no puede ser futura (before_or_equal:today).
     * - El campo user_id se toma del usuario autenticado automáticamente.
     * - El estado inicial siempre es 'pendiente' (requiere confirmación posterior).
     */
    public function store(Request $request)
    {
        $request->validate([
            'valor_actividad_id' => 'required|exists:valor_actividades,id',
            'lote_id'            => 'required|exists:lotes,id',
            'trabajador_id'      => 'required|exists:trabajadores,id',
            'fecha'              => 'required|date|before_or_equal:today',
            'cantidad'           => 'required|integer|min:1',
            'numero_pasada'      => 'required|integer|min:1',
            'observacion'        => 'nullable|string|max:500',
        ]);

        ActividadLaboral::create([
            'valor_actividad_id'  => $request->valor_actividad_id,
            'lote_id'             => $request->lote_id,
            'trabajador_id'       => $request->trabajador_id,
            'user_id'             => Auth::id(), // usuario que registra la actividad
            'fecha'               => $request->fecha,
            'cantidad'            => $request->cantidad,
            'numero_pasada'       => $request->numero_pasada,
            'observacion'         => $request->observacion ?? '',
            'estado_confirmacion' => 'pendiente', // siempre inicia como pendiente
        ]);

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad registrada correctamente.');
    }

    /**
     * update()
     * Actualiza los datos de una actividad pendiente tras validación.
     * Aplica la misma restricción de estado que edit().
     */
    public function update(Request $request, ActividadLaboral $actividad)
    {
        if ($actividad->estado_confirmacion !== 'pendiente') {
            return redirect()->route('actividades.index')
                ->with('error', 'Solo se pueden editar actividades en estado pendiente.');
        }

        $request->validate([
            'valor_actividad_id' => 'required|exists:valor_actividades,id',
            'lote_id'            => 'required|exists:lotes,id',
            'trabajador_id'      => 'required|exists:trabajadores,id',
            'fecha'              => 'required|date|before_or_equal:today',
            'cantidad'           => 'required|integer|min:1',
            'numero_pasada'      => 'required|integer|min:1',
            'observacion'        => 'nullable|string|max:500',
        ]);

        $actividad->update([
            'valor_actividad_id' => $request->valor_actividad_id,
            'lote_id'            => $request->lote_id,
            'trabajador_id'      => $request->trabajador_id,
            'fecha'              => $request->fecha,
            'cantidad'           => $request->cantidad,
            'numero_pasada'      => $request->numero_pasada,
            'observacion'        => $request->observacion ?? '',
        ]);

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad actualizada correctamente.');
    }

    /**
     * destroy()
     * Elimina una actividad siempre que NO esté confirmada.
     * Las actividades confirmadas no se pueden borrar porque ya pueden
     * estar incluidas en una liquidación de pago.
     */
    public function destroy(ActividadLaboral $actividad)
    {
        if ($actividad->estado_confirmacion === 'confirmado') {
            return redirect()->route('actividades.index')
                ->with('error', 'No se puede eliminar una actividad confirmada.');
        }

        $actividad->delete();
        return redirect()->route('actividades.index')
            ->with('success', 'Actividad eliminada correctamente.');
    }

    /**
     * confirmar()
     * Cambia el estado de confirmación de una actividad.
     * Acepta los valores: 'confirmado', 'rechazado' o 'pendiente'.
     * Esta acción es la que habilita o bloquea que la actividad
     * sea incluida en la liquidación semanal de pagos.
     *
     * Ruta: PATCH /actividades/{actividad}/confirmar
     */
    public function confirmar(Request $request, ActividadLaboral $actividad)
    {
        $request->validate([
            'estado_confirmacion' => 'required|in:confirmado,rechazado,pendiente',
        ]);

        $actividad->estado_confirmacion = $request->estado_confirmacion;
        $actividad->save();

        $msg = match($request->estado_confirmacion) {
            'confirmado' => 'Actividad confirmada correctamente.',
            'rechazado'  => 'Actividad rechazada.',
            default      => 'Actividad marcada como pendiente.',
        };

        return redirect()->route('actividades.index')->with('success', $msg);
    }

    /**
     * avanceLote()
     * Devuelve en JSON el avance de hectáreas ya registradas para una
     * combinación de lote + tipo de actividad (valor_actividad_id).
     * Usado por el formulario de nueva actividad para mostrar el progreso
     * del lote en tiempo real y advertir si se excede el total de hectáreas.
     *
     * Ruta: GET /actividades/avance-lote?lote_id=X&valor_actividad_id=Y
     * Excluye actividades rechazadas; incluye pendientes y confirmadas.
     */
    public function avanceLote(Request $request)
    {
        $loteId          = $request->integer('lote_id');
        $valorActividadId = $request->integer('valor_actividad_id');
        $excluirId       = $request->integer('excluir_id', 0); // para edición: excluir la actividad actual

        $lote = Lote::find($loteId);

        if (!$lote || !$valorActividadId) {
            return response()->json(['error' => 'Parámetros inválidos'], 422);
        }

        // Suma de cantidades ya registradas para este lote + tipo de actividad
        $query = ActividadLaboral::where('lote_id', $loteId)
            ->where('valor_actividad_id', $valorActividadId)
            ->whereIn('estado_confirmacion', ['pendiente', 'confirmado']);

        if ($excluirId > 0) {
            $query->where('id', '!=', $excluirId);
        }

        $hectareasRegistradas = $query->sum('cantidad');
        $hectareasTotales     = (float) $lote->tamano_hectareas;
        $hectareasRestantes   = max(0, $hectareasTotales - $hectareasRegistradas);
        $porcentaje           = $hectareasTotales > 0
            ? min(100, round(($hectareasRegistradas / $hectareasTotales) * 100, 1))
            : 0;

        return response()->json([
            'lote_nombre'          => $lote->nombre,
            'hectareas_totales'    => $hectareasTotales,
            'hectareas_registradas'=> $hectareasRegistradas,
            'hectareas_restantes'  => $hectareasRestantes,
            'porcentaje'           => $porcentaje,
            'completado'           => $hectareasRegistradas >= $hectareasTotales,
        ]);
    }
}
