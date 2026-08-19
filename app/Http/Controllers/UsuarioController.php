<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('rol')->orderBy('id', 'desc')->get();
        $roles    = Rol::orderBy('nombre')->get();
        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Rol::orderBy('nombre')->get();
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rol_id'   => 'required|exists:roles,id',
            'nombre'   => 'required|string|max:100',
            'apellido' => 'nullable|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'telefono' => 'nullable|string|max:20',
        ]);

        User::create([
            'rol_id'   => $request->rol_id,
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido ?? '',
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono ?? '',
            'estado'   => 'activo',
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        $roles = Rol::orderBy('nombre')->get();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'rol_id'   => 'required|exists:roles,id',
            'nombre'   => 'required|string|max:100',
            'apellido' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $datos = [
            'rol_id'   => $request->rol_id,
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido ?? '',
            'telefono' => $request->telefono ?? '',
        ];

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    // Activar / Desactivar (toggle estado)
    public function toggleEstado(User $usuario)
    {
        $usuario->estado = ($usuario->estado === 'activo') ? 'inactivo' : 'activo';
        $usuario->save();

        $msg = "Usuario {$usuario->estado} correctamente.";
        return redirect()->route('usuarios.index')->with('success', $msg);
    }
}
