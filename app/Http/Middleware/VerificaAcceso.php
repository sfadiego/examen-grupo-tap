<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe el acceso a una ruta según las secciones asignadas a los
 * perfiles del usuario autenticado. Se usa como `acceso:NombreDeLaSeccion`
 * en las rutas (ver routes/api.php).
 */
class VerificaAcceso
{
    public function handle(Request $request, Closure $next, string $seccion): Response
    {
        $usuario = $request->user();
        if (! $usuario) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        if (! $usuario->tieneAcceso($seccion)) {
            return response()->json(['message' => 'No tiene acceso a esta sección.'], 403);
        }

        return $next($request);
    }
}
