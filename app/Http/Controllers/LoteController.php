<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index()
    {
        $lotes          = Lote::orderBy('nombre')->get();
        $totalHectareas = $lotes->sum('tamano_hectareas');
        return view('admin.lotes.index', compact('lotes', 'totalHectareas'));
    }

    public function create()
    {
        return view('admin.lotes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'referencia'       => 'required|string|max:100|unique:lotes,referencia',
            'ubicacion'        => 'nullable|string|max:255',
            'tamano_hectareas' => 'required|numeric|min:0.01',
        ]);

        Lote::create([
            'nombre'           => $request->nombre,
            'referencia'       => $request->referencia,
            'ubicacion'        => $request->ubicacion ?? '',
            'tamano_hectareas' => $request->tamano_hectareas,
        ]);

        return redirect()->route('lotes.index')
            ->with('success', 'Lote registrado correctamente.');
    }

    public function edit(Lote $lote)
    {
        return view('admin.lotes.edit', compact('lote'));
    }

    public function update(Request $request, Lote $lote)
    {
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'referencia'       => 'required|string|max:100|unique:lotes,referencia,' . $lote->id,
            'ubicacion'        => 'nullable|string|max:255',
            'tamano_hectareas' => 'required|numeric|min:0.01',
        ]);

        $lote->update([
            'nombre'           => $request->nombre,
            'referencia'       => $request->referencia,
            'ubicacion'        => $request->ubicacion ?? '',
            'tamano_hectareas' => $request->tamano_hectareas,
        ]);

        return redirect()->route('lotes.index')
            ->with('success', 'Lote actualizado correctamente.');
    }

    public function destroy(Lote $lote)
    {
        if ($lote->actividadesLaborales()->count() > 0) {
            return redirect()->route('lotes.index')
                ->with('error', 'No se puede eliminar: el lote tiene actividades laborales registradas.');
        }

        $lote->delete();
        return redirect()->route('lotes.index')
            ->with('success', 'Lote eliminado correctamente.');
    }
}
