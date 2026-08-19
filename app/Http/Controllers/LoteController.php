<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Http\Request;

/**
 * LoteController
 *
 * Administra los lotes o parcelas de terreno de la finca.
 * Cada lote tiene un nombre, una referencia única, ubicación opcional
 * y un tamaño en hectáreas. Los lotes se asocian a las actividades
 * laborales para saber en qué terreno se realizó cada labor.
 *
 * Rutas asociadas (resource 'lotes'):
 *   GET    /lotes              → index()
 *   GET    /lotes/create       → create()
 *   POST   /lotes              → store()
 *   GET    /lotes/{id}/edit    → edit()
 *   PUT    /lotes/{id}         → update()
 *   DELETE /lotes/{id}         → destroy()
 */
class LoteController extends Controller
{
    /**
     * index()
     * Lista todos los lotes ordenados por nombre y calcula
     * el total de hectáreas sumando el campo tamano_hectareas
     * de todos los registros (usado en la tarjeta de resumen de la vista).
     */
    public function index()
    {
        $lotes          = Lote::orderBy('nombre')->get();
        $totalHectareas = $lotes->sum('tamano_hectareas');
        return view('admin.lotes.index', compact('lotes', 'totalHectareas'));
    }

    /**
     * create()
     * Retorna la vista con el formulario vacío para registrar un nuevo lote.
     */
    public function create()
    {
        return view('admin.lotes.create');
    }

    /**
     * store()
     * Valida y guarda un nuevo lote.
     * - La referencia debe ser única en la tabla (identificador de campo).
     * - El tamaño mínimo aceptado es 0.01 hectáreas.
     */
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

    /**
     * edit()
     * Carga el formulario de edición con los datos actuales del lote.
     */
    public function edit(Lote $lote)
    {
        return view('admin.lotes.edit', compact('lote'));
    }

    /**
     * update()
     * Actualiza los datos del lote.
     * La validación de unicidad de 'referencia' excluye el propio registro
     * para permitir guardar sin cambiar la referencia existente.
     */
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

    /**
     * destroy()
     * Elimina el lote solo si no tiene actividades laborales registradas.
     * Esta restricción protege la integridad referencial: si un lote tiene
     * historial de labores no se puede borrar sin perder esa trazabilidad.
     */
    public function destroy(Lote $lote)
    {
        // Verificar dependencias antes de eliminar
        if ($lote->actividadesLaborales()->count() > 0) {
            return redirect()->route('lotes.index')
                ->with('error', 'No se puede eliminar: el lote tiene actividades laborales registradas.');
        }

        $lote->delete();
        return redirect()->route('lotes.index')
            ->with('success', 'Lote eliminado correctamente.');
    }
}
