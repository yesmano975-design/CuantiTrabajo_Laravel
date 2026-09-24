<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\DetallePago;
use App\Models\ActividadLaboral;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * PagoController
 *
 * Maneja todo el ciclo de liquidación semanal de pagos.
 * El flujo es:
 *   1. El sistema detecta semanas (lunes–sábado) con actividades confirmadas.
 *   2. El administrador selecciona una semana y genera la liquidación (store).
 *   3. Se crea un registro Pago con sus DetallePagos (uno por actividad).
 *   4. El pago puede verse en detalle (show), marcarse como pagado o eliminarse.
 *
 * Rutas asociadas (resource 'pagos' + ruta extra):
 *   GET    /pagos                        → index()
 *   POST   /pagos                        → store()
 *   GET    /pagos/{id}                   → show()
 *   DELETE /pagos/{id}                   → destroy()
 *   PATCH  /pagos/{id}/marcar-pagado     → marcarPagado()
 */
class PagoController extends Controller
{
    /**
     * index()
     * Vista principal de liquidaciones. Hace dos consultas paralelas:
     *
     * 1. $semanas: agrupa las actividades confirmadas por semana (lunes–sábado)
     *    usando SQL para calcular el lunes de cada fecha y obtener el total
     *    acumulado, número de trabajadores y número de actividades por semana.
     *
     * 2. $historial: lista todos los pagos ya generados con la cantidad de
     *    detalles incluidos, ordenados por fecha de generación.
     *
     * Luego filtra la semana actualmente seleccionada (por parámetro GET o la
     * primera disponible) y construye el resumen por trabajador ($resumenSemana)
     * sumando los subtotales de cada actividad.
     *
     * También verifica si ya se generó un pago para la semana seleccionada
     * ($yaGenerado) para deshabilitar el botón de generación.
     */
    public function index()
    {
        // ── Semanas disponibles con actividades confirmadas ──────────────────
        // La función SQL calcula el lunes de cada fecha usando el día de la semana.
        $semanas = ActividadLaboral::selectRaw("
                DATE_SUB(fecha, INTERVAL (DAYOFWEEK(fecha) + 5) % 7 DAY) AS lunes,
                DATE_ADD(DATE_SUB(fecha, INTERVAL (DAYOFWEEK(fecha) + 5) % 7 DAY), INTERVAL 5 DAY) AS sabado,
                COUNT(DISTINCT trabajador_id) AS num_trabajadores,
                COUNT(id) AS num_actividades,
                SUM(cantidad * (SELECT valor_unitario FROM valor_actividades va WHERE va.id = actividad_laborals.valor_actividad_id) * numero_pasada) AS total_semana
            ")
            ->where('estado_confirmacion', 'confirmado')
            ->groupBy('lunes', 'sabado')
            ->orderBy('lunes', 'desc')
            ->get();

        // ── Historial de liquidaciones ya procesadas ─────────────────────────
        $historial = Pago::withCount('detallePagos')
            ->with([
                'detallePagos.actividadLaboral.trabajador.cargo',
                'detallePagos.actividadLaboral.lote',
                'detallePagos.actividadLaboral.valorActividad.tipoActividad',
            ])
            ->orderBy('fecha_generacion', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // ── Semana seleccionada (GET o primera de la lista) ──────────────────
        $lunesActual  = request('lunes',  $semanas->first()?->lunes);
        $sabadoActual = request('sabado', $semanas->first()?->sabado);

        $resumenSemana         = collect();
        $totalSemana           = 0;
        $yaGenerado            = false;
        $pagoExistente         = null;
        $actividadesPendientes = collect();

        if ($lunesActual && $sabadoActual) {

            // Comprobar si ya existe un pago para este rango de fechas
            $pagoExistente = Pago::with([
                    'detallePagos.actividadLaboral.trabajador.cargo',
                    'detallePagos.actividadLaboral.lote',
                    'detallePagos.actividadLaboral.valorActividad.tipoActividad',
                ])
                ->where('periodo_inicio', $lunesActual)
                ->where('periodo_fin', $sabadoActual)
                ->first();

            $yaGenerado = (bool) $pagoExistente;

            if ($yaGenerado) {
                // ── IDs ya incluidos en el pago ──
                $idsYaIncluidos = $pagoExistente->detallePagos->pluck('actividad_laboral_id');

                // ── Actividades del pago existente agrupadas por trabajador ──
                $resumenDelPago = $pagoExistente->detallePagos
                    ->groupBy(fn($d) => $d->actividadLaboral->trabajador_id)
                    ->map(function ($detalles) {
                        $trabajador = $detalles->first()->actividadLaboral->trabajador;
                        return [
                            'trabajador'      => $trabajador,
                            'actividades'     => $detalles->map->actividadLaboral,
                            'num_actividades' => $detalles->count(),
                            'total'           => $detalles->sum('subtotal'),
                            'liquidado'       => true,
                        ];
                    });

                // ── Actividades confirmadas NO incluidas en el pago ──
                $actsPendientes = ActividadLaboral::with(['trabajador.cargo', 'valorActividad.tipoActividad', 'lote'])
                    ->whereBetween('fecha', [$lunesActual, $sabadoActual])
                    ->where('estado_confirmacion', 'confirmado')
                    ->whereNotIn('id', $idsYaIncluidos)
                    ->get()
                    ->groupBy('trabajador_id');

                // ── Guardar pendientes para la vista (sección de agregar) ──
                $actividadesPendientes = $actsPendientes
                    ->map(function ($actividades) {
                        $trabajador = $actividades->first()->trabajador;
                        $total = $actividades->sum(fn($a) =>
                            $a->cantidad * ($a->valorActividad->valor_unitario ?? 0) * $a->numero_pasada
                        );
                        return [
                            'trabajador'  => $trabajador,
                            'actividades' => $actividades,
                            'total'       => $total,
                        ];
                    })
                    ->values();

                // ── Fusionar: trabajadores del pago + trabajadores pendientes ──
                foreach ($actsPendientes as $tId => $actividades) {
                    $trabajador = $actividades->first()->trabajador;
                    $total = $actividades->sum(fn($a) =>
                        $a->cantidad * ($a->valorActividad->valor_unitario ?? 0) * $a->numero_pasada
                    );
                    if ($resumenDelPago->has($tId)) {
                        // Ya existe en el pago: agregar actividades pendientes al ítem
                        $existing = $resumenDelPago[$tId];
                        $existing['actividades_pendientes'] = $actividades;
                        $existing['total_pendiente'] = $total;
                        $resumenDelPago[$tId] = $existing;
                    } else {
                        // Trabajador nuevo sin ninguna actividad liquidada aún
                        $resumenDelPago[$tId] = [
                            'trabajador'             => $trabajador,
                            'actividades'            => collect(),
                            'num_actividades'        => 0,
                            'total'                  => 0,
                            'liquidado'              => false,
                            'actividades_pendientes' => $actividades,
                            'total_pendiente'        => $total,
                        ];
                    }
                }

                $resumenSemana = $resumenDelPago->values();
                $totalSemana   = $pagoExistente->total_pago;

            } else {
                $actividadesPendientes = collect();
                // ── Semana pendiente: calcular desde actividades confirmadas ──
                $resumenSemana = ActividadLaboral::with(['trabajador.cargo', 'valorActividad.tipoActividad', 'lote'])
                    ->whereBetween('fecha', [$lunesActual, $sabadoActual])
                    ->where('estado_confirmacion', 'confirmado')
                    ->get()
                    ->groupBy('trabajador_id')
                    ->map(function ($actividades) {
                        $trabajador = $actividades->first()->trabajador;
                        $total = $actividades->sum(fn($a) =>
                            $a->cantidad * ($a->valorActividad->valor_unitario ?? 0) * $a->numero_pasada
                        );
                        return [
                            'trabajador'             => $trabajador,
                            'actividades'            => $actividades,
                            'num_actividades'        => $actividades->count(),
                            'total'                  => $total,
                            'liquidado'              => false,
                            'actividades_pendientes' => collect(),
                            'total_pendiente'        => 0,
                        ];
                    })
                    ->values();

                $totalSemana = $resumenSemana->sum('total');
            }
        }

        return view('admin.pagos.index', compact(
            'semanas', 'historial',
            'lunesActual', 'sabadoActual',
            'resumenSemana', 'totalSemana',
            'yaGenerado', 'pagoExistente', 'actividadesPendientes'
        ));
    }

    /**
     * store()
     * Genera la liquidación para una semana específica.
     *
     * Proceso:
     *   1. Valida que el rango de fechas sea coherente.
     *   2. Verifica que no exista ya un pago para esa semana.
     *   3. Obtiene todas las actividades confirmadas del rango.
     *   4. Calcula el total general.
     *   5. Dentro de una transacción DB: crea el registro Pago
     *      y un DetallePago por cada actividad.
     *   6. Si algo falla, hace rollback y devuelve error.
     *
     * El uso de DB::beginTransaction protege contra pagos parcialmente
     * generados si ocurre un error a mitad del proceso.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lunes'  => 'required|date',
            'sabado' => 'required|date|after_or_equal:lunes',
        ]);

        $lunes  = $request->lunes;
        $sabado = $request->sabado;

        // Evitar liquidaciones duplicadas para el mismo periodo
        if (Pago::where('periodo_inicio', $lunes)->where('periodo_fin', $sabado)->exists()) {
            return redirect()->route('pagos.index', compact('lunes', 'sabado'))
                ->with('error', 'Ya existe un pago generado para esta semana.');
        }

        // Actividades confirmadas del periodo
        $actividades = ActividadLaboral::with('valorActividad')
            ->whereBetween('fecha', [$lunes, $sabado])
            ->where('estado_confirmacion', 'confirmado')
            ->get();

        if ($actividades->isEmpty()) {
            return redirect()->route('pagos.index')
                ->with('error', 'No hay actividades confirmadas para esta semana.');
        }

        // Total general de la liquidación
        $total = $actividades->sum(fn($a) =>
            $a->cantidad * ($a->valorActividad->valor_unitario ?? 0) * $a->numero_pasada
        );

        // Transacción: si falla cualquier INSERT se revierte todo
        DB::beginTransaction();
        try {
            // Cabecera del pago
            $pago = Pago::create([
                'fecha_generacion' => now()->toDateString(),
                'periodo_inicio'   => $lunes,
                'periodo_fin'      => $sabado,
                'total_pago'       => $total,
                'estado'           => 'pendiente',
            ]);

            // Detalle: un registro por cada actividad incluida
            foreach ($actividades as $act) {
                $valorUnit = $act->valorActividad->valor_unitario ?? 0;
                $subtotal  = $act->cantidad * $valorUnit * $act->numero_pasada;

                DetallePago::create([
                    'pago_id'              => $pago->id,
                    'actividad_laboral_id' => $act->id,
                    'cantidad'             => $act->cantidad,
                    'valor_unitario'       => $valorUnit,
                    'subtotal'             => $subtotal,
                ]);
            }

            DB::commit();

            return redirect()->route('pagos.index')
                ->with('success', "Pago #{$pago->id} generado correctamente. Total: $" . number_format($total, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pagos.index')
                ->with('error', 'Error al generar el pago: ' . $e->getMessage());
        }
    }

    /**
     * agregarActividades()
     * Agrega actividades nuevas (no incluidas aún) a un pago ya existente.
     *
     * El administrador selecciona con checkboxes qué actividades incluir.
     * Se crean nuevos DetallePago y se actualiza el total_pago del encabezado.
     * Solo funciona si el pago está en estado 'pendiente' (no pagado).
     *
     * Ruta: PATCH /pagos/{pago}/agregar-actividades
     */
    public function agregarActividades(Request $request, Pago $pago)
    {
        if ($pago->estado === 'pagado') {
            return back()->with('error', 'No se pueden agregar actividades a un pago ya desembolsado.');
        }

        $request->validate([
            'actividad_ids'   => 'required|array|min:1',
            'actividad_ids.*' => 'integer|exists:actividad_laborals,id',
        ]);

        // Evitar duplicados: filtrar las que ya están en el pago
        $yaIncluidos = $pago->detallePagos->pluck('actividad_laboral_id')->toArray();
        $nuevasIds   = array_diff($request->actividad_ids, $yaIncluidos);

        if (empty($nuevasIds)) {
            return back()->with('error', 'Las actividades seleccionadas ya están incluidas en este pago.');
        }

        $actividades = ActividadLaboral::with('valorActividad')
            ->whereIn('id', $nuevasIds)
            ->where('estado_confirmacion', 'confirmado')
            ->get();

        if ($actividades->isEmpty()) {
            return back()->with('error', 'No se encontraron actividades válidas para agregar.');
        }

        DB::beginTransaction();
        try {
            $totalNuevo = 0;

            foreach ($actividades as $act) {
                $valorUnit = $act->valorActividad->valor_unitario ?? 0;
                $subtotal  = $act->cantidad * $valorUnit * $act->numero_pasada;
                $totalNuevo += $subtotal;

                DetallePago::create([
                    'pago_id'              => $pago->id,
                    'actividad_laboral_id' => $act->id,
                    'cantidad'             => $act->cantidad,
                    'valor_unitario'       => $valorUnit,
                    'subtotal'             => $subtotal,
                ]);
            }

            // Actualizar el total acumulado del pago
            $pago->increment('total_pago', $totalNuevo);

            DB::commit();

            $lunes  = $pago->periodo_inicio->format('Y-m-d');
            $sabado = $pago->periodo_fin->format('Y-m-d');

            return redirect()
                ->route('pagos.index', ['lunes' => $lunes, 'sabado' => $sabado])
                ->with('success', count($nuevasIds) . ' actividad(es) agregada(s) al pago #' . $pago->id . '. Nuevo total: $' . number_format($pago->fresh()->total_pago, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al agregar actividades: ' . $e->getMessage());
        }
    }

    /**
     * show()
     * Muestra el detalle completo de un pago ya generado.
     * Carga todas las relaciones necesarias de una sola vez (eager loading)
     * y luego agrupa los detalles por trabajador para presentar
     * el desglose de lo que le corresponde a cada uno.
     */
    public function show(Pago $pago)
    {
        // Carga anidada: pago → detalles → actividad → trabajador/lote/tarifa/tipo
        $pago->load([
            'detallePagos.actividadLaboral.trabajador.cargo',
            'detallePagos.actividadLaboral.lote',
            'detallePagos.actividadLaboral.valorActividad.tipoActividad',
        ]);

        // Agrupar los renglones del pago por trabajador para la vista de detalle
        $porTrabajador = $pago->detallePagos
            ->groupBy(fn($d) => $d->actividadLaboral->trabajador_id)
            ->map(function ($detalles) {
                $trabajador = $detalles->first()->actividadLaboral->trabajador;
                return [
                    'trabajador' => $trabajador,
                    'detalles'   => $detalles,
                    'subtotal'   => $detalles->sum('subtotal'),
                ];
            })
            ->values();

        return view('admin.pagos.show', compact('pago', 'porTrabajador'));
    }

    /**
     * marcarPagado()
     * Cambia el estado del pago de 'pendiente' a 'pagado'.
     * Actúa como confirmación de que el dinero fue efectivamente entregado.
     * Una vez marcado como pagado no se puede eliminar ni volver a cambiar.
     *
     * Ruta: PATCH /pagos/{pago}/marcar-pagado
     */
    public function marcarPagado(Pago $pago)
    {
        if ($pago->estado === 'pagado') {
            return redirect()->route('pagos.index')
                ->with('error', 'Este pago ya fue marcado como pagado.');
        }

        $pago->estado = 'pagado';
        $pago->save();

        return redirect()->route('pagos.index')
            ->with('success', "Pago #{$pago->id} marcado como pagado.");
    }

    /**
     * destroy()
     * Elimina un pago y todos sus detalles asociados usando una transacción.
     * Solo se permite si el pago está en estado 'pendiente'; los pagos
     * ya desembolsados ('pagado') son permanentes.
     */
    public function destroy(Pago $pago)
    {
        if ($pago->estado === 'pagado') {
            return redirect()->route('pagos.index')
                ->with('error', 'No se puede eliminar un pago ya pagado.');
        }

        // Transacción para borrar detalles y cabecera juntos
        DB::beginTransaction();
        try {
            $pago->detallePagos()->delete(); // primero los hijos
            $pago->delete();                 // luego el padre
            DB::commit();
            return redirect()->route('pagos.index')
                ->with('success', 'Pago eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pagos.index')
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
