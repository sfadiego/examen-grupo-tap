<?php

namespace App\Http\Controllers;

use App\Exports\UsuariosExport;
use App\Http\Requests\Usuarios\AsignarPerfilesRequest;
use App\Http\Requests\Usuarios\StoreUsuarioRequest;
use App\Http\Requests\Usuarios\UpdateUsuarioRequest;
use App\Models\Usuario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class UsuarioController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Usuario::all());
    }

    public function exportarPdf(): Response
    {
        $usuarios = Usuario::all();

        return Pdf::loadView('pdf.usuarios', compact('usuarios'))
            ->download('usuarios.pdf');
    }

    public function exportarExcel(): Response
    {
        return Excel::download(new UsuariosExport(), 'usuarios.xlsx');
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $usuario = Usuario::create([
            'codigo' => Usuario::generarCodigo(),
            'usuario' => $request->usuario,
            'nombre' => $request->nombre,
            'foto' => $request->file('foto')
                ->store('fotos', 'public'),
            'telefono' => $request->telefono,
            'password' => Str::random(12),
        ]);

        return response()->json($usuario, 201);
    }

    public function show(string $usuario): JsonResponse
    {
        return response()->json(Usuario::with('perfiles')->findOrFail($usuario));
    }

    public function update(UpdateUsuarioRequest $request, string $usuario): JsonResponse
    {
        $modelo = Usuario::findOrFail($usuario);

        $data = $request->only(['usuario', 'nombre', 'telefono']);

        if ($request->hasFile('foto')) {
            if ($modelo->foto) {
                Storage::disk('public')->delete($modelo->foto);
            }

            $data['foto'] = $request->file('foto')->store('fotos', 'public');
        }

        $modelo->update($data);
        return response()->json($modelo);
    }

    public function destroy(string $usuario): JsonResponse
    {
        $modelo = Usuario::findOrFail($usuario);

        if ($modelo->foto) {
            Storage::disk('public')->delete($modelo->foto);
        }

        $modelo->delete();
        return response()->json(null, 204);
    }

    public function asignarPerfiles(AsignarPerfilesRequest $request, string $usuario): JsonResponse
    {
        $modelo = Usuario::findOrFail($usuario);
        $modelo->perfiles()->sync($request->perfil_ids);

        return response()->json($modelo->load('perfiles'));
    }
}
