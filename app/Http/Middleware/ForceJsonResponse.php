<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fuerza el header Accept a application/json en todas las rutas de la API.
 * Sin esto, si un cliente no lo envía (Postman, curl), Laravel no detecta
 * que se espera JSON y redirige los errores de validación a la página de
 * inicio en vez de responder con un 422/404 en JSON.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
