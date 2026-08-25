<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\JsonResponse;

/**
 * Consulta del historial de altas/ediciones registrado automáticamente
 * por el trait RegistraBitacora en los modelos que lo usan.
 */
class BitacoraController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Bitacora::orderBy('created_at', 'desc')->get()
        );
    }
}
