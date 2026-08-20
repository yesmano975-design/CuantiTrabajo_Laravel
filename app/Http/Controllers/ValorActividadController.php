<?php

namespace App\Http\Controllers;

use App\Models\ValorActividad;
use App\Models\TipoActividad;
use Illuminate\Http\Request;

/**
 * ValorActividadController
 *
 * Administra las tarifas económicas asignadas a cada tipo de actividad.
 * Una tarifa (ValorActividad) define cuánto se paga por unidad de medida
 * en un rango de fechas específico (fecha_inicio → fecha_fin).
 * Esto permite tener historial de cambios de precios a lo largo del tiempo.
 *
 * Acceso restringido a usuarios con rol 'administrador'.
 *
 * Rutas asociadas (resource 'tarifas'):
 *   GET    /tarifas              → index()
 *   GET    /tarifas/create       → create()
 *   POST   /tarifas              → store()
 *   GET    /tarifas/{tarifa}/edit    → edit()
 *   PUT    /tarifas/{tarifa}         → update()
 *   DELETE /tarifas/{tarifa}         → destroy()
 */
class ValorActividadController extends Controller
{
    /**
     * index()
     * Lista todas las tarifas con su tipo de actividad relacionado,
     * ordenadas por fecha de inicio descendente (más recientes primero).
     */
    public function index()
    {
        $tarifas = ValorActividad::with('tipoActividad')
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        $tipos = TipoActividad::orderBy('nombre')->get();
        return view('admin.tarifas.index', compact('tarifas', 'tipos'));
    }

    /**
     * create()
     * Retorna el formulario de nueva tarifa con los tipos de actividad
     * disponibles para el selector.
     */
    public function create()
    {
        $tipos = TipoActividad::orderBy('nombre')->get();
        return view('admin.tarifas.create', compact('tipos'));
    }

    /**
     * store()
     * Valida y crea una nueva tarifa.
     * - fecha_fin es opcional; si se omite, la tarifa se considera abierta (sin vencimiento).
     * - Si se indica fecha_fin debe ser posterior o igual a fecha_inicio.
     * - El estado se establece automáticamente como 'activo' al crear.
     * - valor_unitario debe ser mayor a 0.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_actividad_id' => 'required|exists:tipo_actividades,id',
            'valor_unitario'    => 'required|numeric|min:0.01',
            'fecha_inicio'      => 'required|date',
            'fecha_fin'         => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        ValorActividad::create([
            'tipo_actividad_id' => $request->tipo_actividad_id,
            'valor_unitario'    => $request->valor_unitario,
            'fecha_inicio'      => $request->fecha_inicio,
            'fecha_fin'         => $request->fecha_fin ?: null, // null = sin vencimiento
            'estado'            => 'activo',
        ]);

        return redirect()->route('tarifas.index')
            ->with('success', 'Tarifa registrada correctamente.');
    }

    /**
     * edit()
     * Carga el formulario de edición con los datos de la tarifa
     * y los tipos de actividad disponibles.
     * Nota: el parámetro de ruta se llama {tarifa} (definido en web.php).
     */
    public function edit(ValorActividad $tarifa)
    {
        $tipos = TipoActividad::orderBy('nombre')->get();
        return view('admin.tarifas.edit', compact('tarifa', 'tipos'));
    }

    /**
     * update()
     * Actualiza la tarifa con los nuevos valores.
     * Agrega la validación del campo 'estado' (activo/inactivo)
     * que solo está disponible en edición, no en creación.
     * Una tarifa inactiva no aparece disponible al registrar actividades.
     */
    public function update(Request $request, ValorActividad $tarifa)
    {
        $request->validate([
            'tipo_actividad_id' => 'required|exists:tipo_actividades,id',
            'valor_unitario'    => 'required|numeric|min:0.01',
            'fecha_inicio'      => 'required|date',
            'fecha_fin'         => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'            => 'required|in:activo,inactivo',
        ]);

        $tarifa->update([
            'tipo_actividad_id' => $request->tipo_actividad_id,
            'valor_unitario'    => $request->valor_unitario,
            'fecha_inicio'      => $request->fecha_inicio,
            'fecha_fin'         => $request->fecha_fin ?: null,
            'estado'            => $request->estado,
        ]);

        return redirect()->route('tarifas.index')
            ->with('success', 'Tarifa actualizada correctamente.');
    }

    /**
     * destroy()
     * Elimina la tarifa solo si no tiene actividades laborales que la referencian.
     * Si hay actividades asociadas, la tarifa no puede borrarse para
     * mantener la coherencia del cálculo de pagos históricos.
     */
    public function destroy(ValorActividad $tarifa)
    {
        if ($tarifa->actividadesLaborales()->count() > 0) {
            return redirect()->route('tarifas.index')
                ->with('error', 'No se puede eliminar: tiene actividades laborales asociadas.');
        }

        $tarifa->delete();
        return redirect()->route('tarifas.index')
            ->with('success', 'Tarifa eliminada correctamente.');
    }
}
