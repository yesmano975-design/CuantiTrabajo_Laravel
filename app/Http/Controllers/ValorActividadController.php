<?php

namespace App\Http\Controllers;

use App\Models\ValorActividad;
use App\Models\TipoActividad;
use Illuminate\Http\Request;

class ValorActividadController extends Controller
{
    public function index()
    {
        $tarifas = ValorActividad::with('tipoActividad')
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        return view('admin.tarifas.index', compact('tarifas'));
    }

    public function create()
    {
        $tipos = TipoActividad::orderBy('nombre')->get();
        return view('admin.tarifas.create', compact('tipos'));
    }

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
            'fecha_fin'         => $request->fecha_fin ?: null,
            'estado'            => 'activo',
        ]);

        return redirect()->route('tarifas.index')
            ->with('success', 'Tarifa registrada correctamente.');
    }

    public function edit(ValorActividad $tarifa)
    {
        $tipos = TipoActividad::orderBy('nombre')->get();
        return view('admin.tarifas.edit', compact('tarifa', 'tipos'));
    }

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
