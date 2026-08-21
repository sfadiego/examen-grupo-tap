<?php

use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\VerificaAcceso;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Midleware ForceJsonResponse verifica que la cabecera Accept: application/json esté presente en todas las rutas de la API.
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);

        // Midleware VerificaAcceso verifica que el usuario autenticado tenga acceso a la sección indicada en la ruta.
        $middleware->alias([
            'acceso' => VerificaAcceso::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Validamos que la ruta comience con api/ para no afectar el manejo de errores de las rutas web.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'El recurso solicitado no existe.',
                ], 404);
            }
        });
    })->create();
