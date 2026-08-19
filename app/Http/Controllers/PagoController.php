<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\DetallePago;
use App\Models\ActividadLaboral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagoController extends Controller
{
    // --------------------------------------------------------
    // INDEX — muestra semanas disponibles + historial
    // --------------------------------------------------------
    public function index()
    {
        // Semanas que tienen actividades confirmadas (lunes a sábado)
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

        // Historial de pagos ya generados
        $historial = Pago::withCount('detallePagos')
            ->orderBy('fecha_generacion', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Semana seleccionada (primera disponible o la del GET)
        $lunesActual  = request('lunes',  $semanas->first()?->lunes);
        $sabadoActual = request('sabado', $semanas->first()?->sabado);

        $resumenSemana         = [];
        $detallesPorTrabajador = [];
        $yaGenerado            = false;
        $totalSemana           = 0;

        if ($lunesActual && $sabadoActual) {
            // Resumen por trabajador en la semana seleccionada
            $resumenSemana = ActividadLaboral::with(['trabajador.cargo', 'valorActividad'])
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
                        'trabajador'      => $trabajador,
                        'actividades'     => $actividades,
                        'num_actividades' => $actividades->count(),
                        'total'           => $total,
                    ];
                })
                ->values();

            $totalSemana = $resumenSemana->sum('total');

            // Verificar si ya se generó pago para esta semana
            $yaGenerado = Pago::where('periodo_inicio', $lunesActual)
                ->where('periodo_fin', $sabadoActual)
                ->exists();
        }

        return view('admin.pagos.index', compact(
            'semanas', 'historial',
            'lunesActual', 'sabadoActual',
            'resumenSemana', 'totalSemana',
            'yaGenerado'
        ));
    }

    // --------------------------------------------------------
    // GENERAR pago semanal
    // --------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'lunes'  => 'required|date',
            'sabado' => 'required|date|after_or_equal:lunes',
        ]);

        $lunes  = $request->lunes;
        $sabado = $request->sabado;

        // Verificar si ya existe
        if (Pago::where('periodo_inicio', $lunes)->where('periodo_fin', $sabado)->exists()) {
            return redirect()->route('pagos.index', compact('lunes', 'sabado'))
                ->with('error', 'Ya existe un pago generado para esta semana.');
        }

        // Obtener actividades confirmadas de la semana
        $actividades = ActividadLaboral::with('valorActividad')
            ->whereBetween('fecha', [$lunes, $sabado])
            ->where('estado_confirmacion', 'confirmado')
            ->get();

        if ($actividades->isEmpty()) {
            return redirect()->route('pagos.index')
                ->with('error', 'No hay actividades confirmadas para esta semana.');
        }

        $total = $actividades->sum(fn($a) =>
            $a->cantidad * ($a->valorActividad->valor_unitario ?? 0) * $a->numero_pasada
        );

        DB::beginTransaction();
        try {
            // Crear cabecera del pago
            $pago = Pago::create([
                'fecha_generacion' => now()->toDateString(),
                'periodo_inicio'   => $lunes,
                'periodo_fin'      => $sabado,
                'total_pago'       => $total,
                'estado'           => 'pendiente',
            ]);

            // Crear detalles por actividad
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

    // --------------------------------------------------------
    // SHOW — detalle de un pago específico
    // --------------------------------------------------------
    public function show(Pago $pago)
    {
        $pago->load([
            'detallePagos.actividadLaboral.trabajador.cargo',
            'detallePagos.actividadLaboral.lote',
            'detallePagos.actividadLaboral.valorActividad.tipoActividad',
        ]);

        // Agrupar detalles por trabajador
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

    // --------------------------------------------------------
    // MARCAR COMO PAGADO
    // --------------------------------------------------------
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

    public function destroy(Pago $pago)
    {
        if ($pago->estado === 'pagado') {
            return redirect()->route('pagos.index')
                ->with('error', 'No se puede eliminar un pago ya pagado.');
        }

        DB::beginTransaction();
        try {
            $pago->detallePagos()->delete();
            $pago->delete();
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
