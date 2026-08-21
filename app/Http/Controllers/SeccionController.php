<?php

namespace App\Http\Controllers;

use App\Http\Requests\Secciones\StoreSeccionRequest;
use App\Http\Requests\Secciones\UpdateSeccionRequest;
use App\Models\Seccion;
use Illuminate\Http\JsonResponse;

class SeccionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Seccion::all());
    }

    public function store(StoreSeccionRequest $request): JsonResponse
    {
        $seccion = Seccion::create([
            'codigo' => Seccion::generarCodigo(),
            'nombre' => $request->nombre,
        ]);

        return response()->json($seccion, 201);
    }

    public function show(string $seccion): JsonResponse
    {
        return response()->json(Seccion::findOrFail($seccion));
    }

    public function update(UpdateSeccionRequest $request, string $seccion): JsonResponse
    {
        $model = Seccion::findOrFail($seccion);
        $model->update($request->only(['nombre']));

        return response()->json($model);
    }

    public function destroy(string $seccion): JsonResponse
    {
        Seccion::findOrFail($seccion)->delete();

        return response()->json(null, 204);
    }
}
