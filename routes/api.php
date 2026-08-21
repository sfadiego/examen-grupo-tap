<?php

use App\Enums\SeccionEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('acceso:' . SeccionEnum::ConsultaProductos->value)->group(function () {
        Route::get('productos/pdf', [ProductoController::class, 'exportarPdf']);
        Route::get('productos/excel', [ProductoController::class, 'exportarExcel']);
        Route::apiResource('productos', ProductoController::class)->except(['store']);
    });

    Route::middleware('acceso:' . SeccionEnum::AltaProductos->value)->group(function () {
        Route::post('productos', [ProductoController::class, 'store']);
    });

    Route::middleware('acceso:' . SeccionEnum::ConsultaUsuarios->value)->group(function () {
        Route::get('usuarios/pdf', [UsuarioController::class, 'exportarPdf']);
        Route::get('usuarios/excel', [UsuarioController::class, 'exportarExcel']);
        Route::apiResource('usuarios', UsuarioController::class)->except(['store']);
        Route::post('usuarios/{usuario}/perfiles', [UsuarioController::class, 'asignarPerfiles']);
    });

    Route::middleware('acceso:' . SeccionEnum::AltaUsuario->value)->group(function () {
        Route::post('usuarios', [UsuarioController::class, 'store']);
    });

    Route::middleware('acceso:' . SeccionEnum::PerfilesUsuarios->value)->group(function () {
        Route::get('perfiles/pdf', [PerfilController::class, 'exportarPdf']);
        Route::get('perfiles/excel', [PerfilController::class, 'exportarExcel']);
        Route::apiResource('perfiles', PerfilController::class)->parameters(['perfiles' => 'perfil']);
        Route::post('perfiles/{perfil}/secciones', [PerfilController::class, 'asignarSecciones']);
    });

    Route::get('secciones', [SeccionController::class, 'index']);
});
