<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: CheckRol
 * Alias registrado: 'role' (en bootstrap/app.php)
 *
 * Verifica que el usuario autenticado tenga uno de los roles permitidos
 * antes de dejar pasar la petición. Si el rol no coincide, devuelve
 * un error 403 (Acceso prohibido).
 *
 * Uso en rutas:
 *   Route::middleware('role:administrador')          → solo administradores
 *   Route::middleware('role:administrador,secretaria') → ambos roles
 *
 * El rol se compara contra el campo 'nombre' del modelo Rol relacionado
 * con el usuario autenticado (Auth::user()->rol->nombre).
 */
class CheckRol
{
    /**
     * handle()
     * Recibe los roles permitidos como parámetros variádicos (string ...$roles).
     * Si el usuario no está autenticado o su rol no está en la lista → 403.
     * Si el rol está permitido → continúa con la petición normalmente.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        // Verificar autenticación y que el rol del usuario esté en la lista permitida
        if (! $usuario || ! in_array($usuario->rol->nombre ?? '', $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
