<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Cargo;
use Illuminate\Http\Request;

/**
 * TrabajadorController
 *
 * Gestiona el registro y administración de los trabajadores del campo.
 * Cada trabajador pertenece a un cargo (ej: Fumigador, Tractorista) y
 * puede estar activo o inactivo. Solo los trabajadores activos aparecen
 * disponibles al registrar actividades laborales.
 *
 * Rutas asociadas (resource 'trabajadores' + ruta extra):
 *   GET    /trabajadores                          → index()
 *   GET    /trabajadores/create                   → create()
 *   POST   /trabajadores                          → store()
 *   GET    /trabajadores/{trabajador}/edit        → edit()
 *   PUT    /trabajadores/{trabajador}             → update()
 *   DELETE /trabajadores/{trabajador}             → destroy()
 *   PATCH  /trabajadores/{trabajador}/toggle-estado → toggleEstado()
 */
class TrabajadorController extends Controller
{
    /**
     * index()
     * Carga todos los trabajadores con su cargo relacionado,
     * ordenados alfabéticamente por nombre.
     * También pasa la lista de cargos para los filtros de la vista.
     */
    public function index()
    {
        $trabajadores = Trabajador::with('cargo')->orderBy('nombre')->get();
        $cargos       = Cargo::orderBy('nombre')->get();
        return view('admin.trabajadores.index', compact('trabajadores', 'cargos'));
    }

    /**
     * create()
     * Retorna el formulario de registro con los cargos disponibles
     * para el campo select del formulario.
     */
    public function create()
    {
        $cargos = Cargo::orderBy('nombre')->get();
        return view('admin.trabajadores.create', compact('cargos'));
    }

    /**
     * store()
     * Valida y guarda un nuevo trabajador.
     * - El documento de identidad debe ser único en la tabla.
     * - El estado se establece automáticamente como 'activo' al crear.
     * - El campo 'huella' no se gestiona en este formulario (uso futuro).
     */
    public function store(Request $request)
    {
        $request->validate([
            'cargo_id'  => 'required|exists:cargos,id',
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'nullable|string|max:100',
            'documento' => 'required|string|max:50|unique:trabajadores,documento',
            'correo'    => 'nullable|email|max:150',
            'telefono'  => 'nullable|string|max:20',
        ]);

        Trabajador::create([
            'cargo_id'  => $request->cargo_id,
            'nombre'    => $request->nombre,
            'apellido'  => $request->apellido ?? '',
            'documento' => $request->documento,
            'correo'    => $request->correo ?? '',
            'telefono'  => $request->telefono ?? '',
            'estado'    => 'activo', // siempre activo al registrar
        ]);

        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador registrado correctamente.');
    }

    /**
     * edit()
     * Carga el formulario de edición con los datos actuales del trabajador
     * y la lista de cargos para el select.
     */
    public function edit(Trabajador $trabajador)
    {
        $cargos = Cargo::orderBy('nombre')->get();
        return view('admin.trabajadores.edit', compact('trabajador', 'cargos'));
    }

    /**
     * update()
     * Actualiza los datos del trabajador.
     * La validación de unicidad del documento excluye el registro actual
     * para permitir guardar sin cambiar el documento existente.
     * El estado puede modificarse desde este formulario si se incluye en el POST.
     */
    public function update(Request $request, Trabajador $trabajador)
    {
        $request->validate([
            'cargo_id'  => 'required|exists:cargos,id',
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'nullable|string|max:100',
            // Ignorar el propio registro al verificar unicidad del documento
            'documento' => 'required|string|max:50|unique:trabajadores,documento,' . $trabajador->id,
            'correo'    => 'nullable|email|max:150',
            'telefono'  => 'nullable|string|max:20',
        ]);

        $trabajador->update([
            'cargo_id'  => $request->cargo_id,
            'nombre'    => $request->nombre,
            'apellido'  => $request->apellido ?? '',
            'documento' => $request->documento,
            'correo'    => $request->correo ?? '',
            'telefono'  => $request->telefono ?? '',
            // Mantener el estado actual si no se envía en el request
            'estado'    => $request->estado ?? $trabajador->estado,
        ]);

        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador actualizado correctamente.');
    }

    /**
     * destroy()
     * Elimina el trabajador solo si no tiene actividades laborales registradas.
     * Si tiene historial de actividades, se bloquea para no perder trazabilidad.
     */
    public function destroy(Trabajador $trabajador)
    {
        if ($trabajador->actividadesLaborales()->count() > 0) {
            return redirect()->route('trabajadores.index')
                ->with('error', 'No se puede eliminar: el trabajador tiene actividades registradas.');
        }

        $trabajador->delete();
        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador eliminado correctamente.');
    }

    /**
     * toggleEstado()
     * Alterna el estado del trabajador entre 'activo' e 'inactivo'
     * sin necesidad de entrar al formulario de edición.
     * Un trabajador inactivo no aparece en el selector al registrar actividades.
     *
     * Ruta: PATCH /trabajadores/{trabajador}/toggle-estado
     */
    public function toggleEstado(Trabajador $trabajador)
    {
        $trabajador->estado = ($trabajador->estado === 'activo') ? 'inactivo' : 'activo';
        $trabajador->save();

        return redirect()->route('trabajadores.index')
            ->with('success', "Trabajador {$trabajador->estado} correctamente.");
    }
}
