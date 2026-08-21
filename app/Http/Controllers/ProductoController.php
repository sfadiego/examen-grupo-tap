<?php

namespace App\Http\Controllers;

use App\Exports\ProductosExport;
use App\Http\Requests\Productos\StoreProductoRequest;
use App\Http\Requests\Productos\UpdateProductoRequest;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD de productos, incluyendo exportación a PDF y Excel.
 */
class ProductoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Producto::all());
    }

    public function exportarPdf(): Response
    {
        $productos = Producto::all();

        return Pdf::loadView('pdf.productos', compact('productos'))
            ->download('productos.pdf');
    }

    public function exportarExcel(): Response
    {
        return Excel::download(new ProductosExport(), 'productos.xlsx');
    }

    public function store(StoreProductoRequest $request): JsonResponse
    {
        $producto = Producto::create([
            'codigo' => Producto::generarCodigo(),
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'marca' => $request->marca,
        ]);

        return response()->json($producto, 201);
    }

    public function show(string $producto): JsonResponse
    {
        return response()->json(Producto::findOrFail($producto));
    }

    public function update(UpdateProductoRequest $request, string $producto): JsonResponse
    {
        $model = Producto::findOrFail($producto);
        $model->update($request->only(['nombre', 'precio', 'marca']));

        return response()->json($model);
    }

    public function destroy(string $producto): JsonResponse
    {
        Producto::findOrFail($producto)->delete();

        return response()->json(null, 204);
    }
}
