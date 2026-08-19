<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Cargo;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
{
    public function index()
    {
        $trabajadores = Trabajador::with('cargo')->orderBy('nombre')->get();
        $cargos       = Cargo::orderBy('nombre')->get();
        return view('admin.trabajadores.index', compact('trabajadores', 'cargos'));
    }

    public function create()
    {
        $cargos = Cargo::orderBy('nombre')->get();
        return view('admin.trabajadores.create', compact('cargos'));
    }

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
            'estado'    => 'activo',
        ]);

        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador registrado correctamente.');
    }

    public function edit(Trabajador $trabajador)
    {
        $cargos = Cargo::orderBy('nombre')->get();
        return view('admin.trabajadores.edit', compact('trabajador', 'cargos'));
    }

    public function update(Request $request, Trabajador $trabajador)
    {
        $request->validate([
            'cargo_id'  => 'required|exists:cargos,id',
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'nullable|string|max:100',
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
            'estado'    => $request->estado ?? $trabajador->estado,
        ]);

        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador actualizado correctamente.');
    }

    public function destroy(Trabajador $trabajador)
    {
        // Verificar que no tenga actividades asociadas
        if ($trabajador->actividadesLaborales()->count() > 0) {
            return redirect()->route('trabajadores.index')
                ->with('error', 'No se puede eliminar: el trabajador tiene actividades registradas.');
        }

        $trabajador->delete();
        return redirect()->route('trabajadores.index')
            ->with('success', 'Trabajador eliminado correctamente.');
    }

    public function toggleEstado(Trabajador $trabajador)
    {
        $trabajador->estado = ($trabajador->estado === 'activo') ? 'inactivo' : 'activo';
        $trabajador->save();

        return redirect()->route('trabajadores.index')
            ->with('success', "Trabajador {$trabajador->estado} correctamente.");
    }
}
