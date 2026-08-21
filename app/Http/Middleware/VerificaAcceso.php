<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificaAcceso
{
    public function handle(Request $request, Closure $next, string $seccion): Response
    {
        $usuario = $request->user();
        if (! $usuario) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $tieneAcceso = $usuario->load('perfiles.secciones')
            ->perfiles
            ->flatMap(fn($perfil) => $perfil->secciones)
            ->contains('nombre', $seccion);

        if (! $tieneAcceso) {
            return response()->json(['message' => 'No tiene acceso a esta sección.'], 403);
        }

        return $next($request);
    }
}
