<?php

namespace App\Http\Controllers;

use App\Models\TipoActividad;
use Illuminate\Http\Request;

/**
 * TipoActividadController
 *
 * Gestiona el catálogo de tipos de actividad (ej: Fumigación, Poda, Abonado).
 * Un tipo de actividad define el nombre y la unidad de medida (horas, días,
 * hectáreas) con la que se cuantifica esa labor. A cada tipo se le pueden
 * asociar una o varias tarifas (ValorActividad) con distintas vigencias.
 *
 * Acceso restringido a usuarios con rol 'administrador'.
 *
 * Rutas asociadas (resource 'tipo-actividades'):
 *   GET    /tipo-actividades              → index()
 *   GET    /tipo-actividades/create       → create()
 *   POST   /tipo-actividades              → store()
 *   GET    /tipo-actividades/{id}/edit    → edit()
 *   PUT    /tipo-actividades/{id}         → update()
 *   DELETE /tipo-actividades/{id}         → destroy()
 */
class TipoActividadController extends Controller
{
    /**
     * index()
     * Lista todos los tipos de actividad ordenados por nombre.
     * Incluye el conteo de tarifas asociadas (withCount) para
     * mostrar en la tabla si el tipo ya tiene tarifas configuradas.
     */
    public function index()
    {
        $tipos = TipoActividad::withCount('valorActividades')->orderBy('nombre')->get();
        return view('admin.tipo-actividades.index', compact('tipos'));
    }

    /**
     * create()
     * Retorna el formulario vacío para registrar un nuevo tipo de actividad.
     */
    public function create()
    {
        return view('admin.tipo-actividades.create');
    }

    /**
     * store()
     * Valida y persiste un nuevo tipo de actividad.
     * - unidad_medida acepta: 'horas', 'dias', 'hectareas' (select en la vista).
     */
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

    /**
     * edit()
     * Carga el formulario de edición con los datos del tipo seleccionado.
     * Nota: el modelo se inyecta como $tipo_actividad por el nombre del
     * parámetro de ruta definido en web.php.
     */
    public function edit(TipoActividad $tipo_actividad)
    {
        return view('admin.tipo-actividades.edit', ['tipo' => $tipo_actividad]);
    }

    /**
     * update()
     * Actualiza el tipo de actividad con los nuevos valores validados.
     */
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

    /**
     * destroy()
     * Elimina el tipo de actividad solo si no tiene tarifas asociadas.
     * Si tiene tarifas (ValorActividad) vinculadas, la eliminación se bloquea
     * para evitar dejar tarifas huérfanas sin tipo.
     */
    public function destroy(TipoActividad $tipo_actividad)
    {
        // Protección de integridad: no eliminar si tiene tarifas hijas
        if ($tipo_actividad->valorActividades()->count() > 0) {
            return redirect()->route('tipo-actividades.index')
                ->with('error', 'No se puede eliminar: tiene tarifas asociadas.');
        }

        $tipo_actividad->delete();
        return redirect()->route('tipo-actividades.index')
            ->with('success', 'Tipo de actividad eliminado correctamente.');
    }
}
