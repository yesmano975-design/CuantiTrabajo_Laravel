<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Lote;
use App\Models\ActividadLaboral;
use App\Models\Pago;

/**
 * DashboardController
 *
 * Proporciona los datos de resumen que se muestran en el panel principal
 * (dashboard) del sistema. Consulta contadores clave de cada módulo
 * para dar una visión rápida del estado operativo de la finca.
 *
 * Ruta asociada:
 *   GET /dashboard → index()
 */
class DashboardController extends Controller
{
    /**
     * index()
     * Recopila cuatro métricas de resumen y las envía a la vista:
     *   - totalTrabajadores    : trabajadores con estado 'activo'
     *   - totalLotes           : total de lotes/terrenos registrados
     *   - actividadesPendientes: actividades que aún no han sido confirmadas ni rechazadas
     *   - pagosPendientes      : liquidaciones generadas pero no marcadas como pagadas
     */
    public function index()
    {
        // Trabajadores activos (excluye inactivos/desvinculados)
        $totalTrabajadores = Trabajador::where('estado', 'activo')->count();

        // Todos los lotes registrados sin filtro de estado
        $totalLotes = Lote::count();

        // Actividades que esperan ser confirmadas o rechazadas
        $actividadesPendientes = ActividadLaboral::where('estado_confirmacion', 'pendiente')->count();

        // Pagos generados que aún no han sido desembolsados
        $pagosPendientes = Pago::where('estado', 'pendiente')->count();

        return view('admin.dashboard', compact(
            'totalTrabajadores',
            'totalLotes',
            'actividadesPendientes',
            'pagosPendientes'
        ));
    }
}
