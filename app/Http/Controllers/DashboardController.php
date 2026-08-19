<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Lote;
use App\Models\ActividadLaboral;
use App\Models\Pago;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTrabajadores    = Trabajador::where('estado', 'activo')->count();
        $totalLotes           = Lote::count();
        $actividadesPendientes = ActividadLaboral::where('estado_confirmacion', 'pendiente')->count();
        $pagosPendientes      = Pago::where('estado', 'pendiente')->count();

        return view('admin.dashboard', compact(
            'totalTrabajadores',
            'totalLotes',
            'actividadesPendientes',
            'pagosPendientes'
        ));
    }
}
