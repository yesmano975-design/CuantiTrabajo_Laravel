<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware NoCache
 *
 * Agrega cabeceras HTTP que impiden que el navegador almacene en caché
 * las páginas del panel. Esto evita que al cerrar sesión y presionar
 * "atrás" se pueda visualizar una página protegida desde la caché local.
 */
class NoCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        return $response->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => 'Sat, 01 Jan 2000 00:00:00 GMT',
        ]);
    }
}
