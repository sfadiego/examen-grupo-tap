<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function () {
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);

Route::get('productos/pdf', [ProductoController::class, 'exportarPdf']);
Route::get('productos/excel', [ProductoController::class, 'exportarExcel']);
Route::apiResource('productos', ProductoController::class);

Route::get('usuarios/pdf', [UsuarioController::class, 'exportarPdf']);
Route::get('usuarios/excel', [UsuarioController::class, 'exportarExcel']);
Route::apiResource('usuarios', UsuarioController::class);
Route::post('usuarios/{usuario}/perfiles', [UsuarioController::class, 'asignarPerfiles']);

Route::get('perfiles/pdf', [PerfilController::class, 'exportarPdf']);
Route::get('perfiles/excel', [PerfilController::class, 'exportarExcel']);
Route::apiResource('perfiles', PerfilController::class)->parameters(['perfiles' => 'perfil']);
Route::post('perfiles/{perfil}/secciones', [PerfilController::class, 'asignarSecciones']);

Route::apiResource('secciones', SeccionController::class)->parameters(['secciones' => 'seccion']);
// });
