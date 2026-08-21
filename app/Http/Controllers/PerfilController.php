<?php

namespace App\Http\Controllers;

use App\Exports\PerfilesExport;
use App\Http\Requests\Perfiles\AsignarSeccionesRequest;
use App\Http\Requests\Perfiles\StorePerfilRequest;
use App\Http\Requests\Perfiles\UpdatePerfilRequest;
use App\Models\Perfil;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD de perfiles (roles) y asignación de secciones.
 */
class PerfilController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Perfil::all());
    }

    public function exportarPdf(): Response
    {
        $perfiles = Perfil::all();

        return Pdf::loadView('pdf.perfiles', compact('perfiles'))
            ->download('perfiles.pdf');
    }

    public function exportarExcel(): Response
    {
        return Excel::download(new PerfilesExport(), 'perfiles.xlsx');
    }

    public function store(StorePerfilRequest $request): JsonResponse
    {
        $perfil = Perfil::create([
            'codigo' => Perfil::generarCodigo(),
            'nombre' => $request->nombre,
        ]);

        return response()->json($perfil, 201);
    }

    public function show(string $perfil): JsonResponse
    {
        return response()->json(Perfil::with('secciones')->findOrFail($perfil));
    }

    public function update(UpdatePerfilRequest $request, string $perfil): JsonResponse
    {
        $model = Perfil::findOrFail($perfil);
        $model->update($request->only(['nombre']));

        return response()->json($model);
    }

    public function destroy(string $perfil): JsonResponse
    {
        Perfil::findOrFail($perfil)->delete();

        return response()->json(null, 204);
    }

    public function asignarSecciones(AsignarSeccionesRequest $request, string $perfil): JsonResponse
    {
        $model = Perfil::findOrFail($perfil);
        $model->secciones()->sync($request->seccion_ids);

        return response()->json($model->load('secciones'));
    }
}
