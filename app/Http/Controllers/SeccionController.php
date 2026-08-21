<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use Illuminate\Http\JsonResponse;

/**
 *  Consulta del catálogo de secciones (permisos fijos del sistema).
 */
class SeccionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Seccion::all());
    }
}
