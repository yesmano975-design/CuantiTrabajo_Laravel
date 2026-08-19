<?php

namespace App\Http\Controllers;

use App\Models\TipoActividad;
use Illuminate\Http\Request;

class TipoActividadController extends Controller
{
    public function index()
    {
        $tipos = TipoActividad::withCount('valorActividades')->orderBy('nombre')->get();
        return view('admin.tipo-actividades.index', compact('tipos'));
    }

    public function create()
    {
        return view('admin.tipo-actividades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'descripcion'   => 'nullable|string|max:255',
            'unidad_medida' => 'required|string|max:50',
        ]);

        TipoActividad::create([
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion ?? '',
            'unidad_medida' => $request->unidad_medida,
        ]);

        return redirect()->route('tipo-actividades.index')
            ->with('success', 'Tipo de actividad creado correctamente.');
    }

    public function edit(TipoActividad $tipo_actividad)
    {
        return view('admin.tipo-actividades.edit', ['tipo' => $tipo_actividad]);
    }

    public function update(Request $request, TipoActividad $tipo_actividad)
    {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'descripcion'   => 'nullable|string|max:255',
            'unidad_medida' => 'required|string|max:50',
        ]);

        $tipo_actividad->update([
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion ?? '',
            'unidad_medida' => $request->unidad_medida,
        ]);

        return redirect()->route('tipo-actividades.index')
            ->with('success', 'Tipo de actividad actualizado correctamente.');
    }

    public function destroy(TipoActividad $tipo_actividad)
    {
        if ($tipo_actividad->valorActividades()->count() > 0) {
            return redirect()->route('tipo-actividades.index')
                ->with('error', 'No se puede eliminar: tiene tarifas asociadas.');
        }

        $tipo_actividad->delete();
        return redirect()->route('tipo-actividades.index')
            ->with('success', 'Tipo de actividad eliminado correctamente.');
    }
}
