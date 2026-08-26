<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * UsuarioController
 *
 * Gestiona los usuarios del sistema (cuentas de acceso al panel).
 * Cada usuario tiene un rol (administrador o secretaria) que determina
 * a qué secciones puede acceder. La contraseña siempre se almacena
 * hasheada usando bcrypt.
 *
 * Acceso restringido a usuarios con rol 'administrador'
 * (protegido por middleware 'role:administrador' en las rutas).
 *
 * Rutas asociadas (resource 'usuarios' + ruta extra):
 *   GET    /usuarios                          → index()
 *   GET    /usuarios/create                   → create()
 *   POST   /usuarios                          → store()
 *   GET    /usuarios/{usuario}/edit           → edit()
 *   PUT    /usuarios/{usuario}                → update()
 *   DELETE /usuarios/{usuario}                → destroy()
 *   PATCH  /usuarios/{usuario}/toggle-estado  → toggleEstado()
 */
class UsuarioController extends Controller
{
    /**
     * index()
     * Lista todos los usuarios con su rol relacionado,
     * ordenados de más reciente a más antiguo (por ID desc).
     * También pasa los roles disponibles para filtros en la vista.
     */
    public function index()
    {
        $usuarios = User::with('rol')->orderBy('id', 'desc')->get();
        $roles    = Rol::orderBy('nombre')->get();
        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    /**
     * create()
     * Retorna el formulario de creación con la lista de roles disponibles.
     */
    public function create()
    {
        $roles = Rol::orderBy('nombre')->get();
        return view('admin.usuarios.create', compact('roles'));
    }

    /**
     * store()
     * Valida y crea un nuevo usuario.
     * - El email debe ser único en la tabla de usuarios.
     * - La contraseña requiere confirmación (campo password_confirmation en el form).
     * - La contraseña mínima es de 6 caracteres.
     * - Hash::make() convierte la contraseña en bcrypt antes de guardar.
     * - El estado inicial siempre es 'activo'.
     */
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
            'password' => Hash::make($request->password), // hashear siempre
            'telefono' => $request->telefono ?? '',
            'estado'   => 'activo',
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * edit()
     * Carga el formulario de edición con los datos del usuario y los roles disponibles.
     */
    public function edit(User $usuario)
    {
        $roles = Rol::orderBy('nombre')->get();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * update()
     * Actualiza los datos del usuario.
     * - El campo password es opcional en edición: solo se actualiza
     *   si se envía un valor (request->filled). Esto permite cambiar
     *   datos sin tener que volver a escribir la contraseña.
     * - Si se envía contraseña nueva, también requiere confirmación.
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'rol_id'   => 'required|exists:roles,id',
            'nombre'   => 'required|string|max:100',
            'apellido' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            // Contraseña opcional: solo se valida si se envía
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $datos = [
            'rol_id'   => $request->rol_id,
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido ?? '',
            'telefono' => $request->telefono ?? '',
        ];

        // Solo actualizar contraseña si se proporcionó una nueva
        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }
 
        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * destroy()
     * Elimina el usuario del sistema.
     * No hay restricción adicional; el administrador puede eliminar
     * cualquier cuenta excepto la suya propia (esto se puede añadir si se requiere).
     */
    public function destroy(User $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * toggleEstado()
     * Alterna el estado del usuario entre 'activo' e 'inactivo'.
     * Un usuario inactivo no puede iniciar sesión (la lógica de
     * bloqueo por estado se puede agregar en el LoginController si se requiere).
     *
     * Ruta: PATCH /usuarios/{usuario}/toggle-estado
     */
    public function toggleEstado(User $usuario)
    {
        $usuario->estado = ($usuario->estado === 'activo') ? 'inactivo' : 'activo';
        $usuario->save();

        $msg = "Usuario {$usuario->estado} correctamente.";
        return redirect()->route('usuarios.index')->with('success', $msg);
    }
}
