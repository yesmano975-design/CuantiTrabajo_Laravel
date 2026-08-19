<?php

namespace App\Http\Controllers;

use App\Models\ActividadLaboral;
use App\Models\ValorActividad;
use App\Models\Trabajador;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActividadLaboralController extends Controller
{
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

        // Totales para las tarjetas
        $pendientes  = $actividades->where('estado_confirmacion', 'pendiente')->count();
        $confirmadas = $actividades->where('estado_confirmacion', 'confirmado')->count();
        $rechazadas  = $actividades->where('estado_confirmacion', 'rechazado')->count();

        return view('admin.actividades.index', compact(
            'actividades', 'pendientes', 'confirmadas', 'rechazadas'
        ));
    }

    public function create()
    {
        // Solo tarifas activas y vigentes hoy
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

        return view('admin.actividades.create', compact('tarifas', 'trabajadores', 'lotes'));
    }

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
            'user_id'             => Auth::id(),
            'fecha'               => $request->fecha,
            'cantidad'            => $request->cantidad,
            'numero_pasada'       => $request->numero_pasada,
            'observacion'         => $request->observacion ?? '',
            'estado_confirmacion' => 'pendiente',
        ]);

        return redirect()->route('actividades.index')
            ->with('success', 'Actividad registrada correctamente.');
    }

    public function edit(ActividadLaboral $actividad)
    {
        if ($actividad->estado_confirmacion !== 'pendiente') {
            return redirect()->route('actividades.index')
                ->with('error', 'Solo se pueden editar actividades en estado pendiente.');
        }

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

        return view('admin.actividades.edit', compact('actividad', 'tarifas', 'trabajadores', 'lotes'));
    }

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
}
