<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use Illuminate\Http\JsonResponse;

class SeccionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Seccion::all());
    }
}
