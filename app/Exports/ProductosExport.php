<?php

namespace App\Exports;

use App\Models\Producto;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductosExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Producto::all();
    }

    public function headings(): array
    {
        return ['Código', 'Nombre', 'Precio', 'Fecha de creación'];
    }

    public function map($producto): array
    {
        return [
            $producto->codigo,
            $producto->nombre,
            $producto->precio,
            $producto->created_at->format('d/m/Y H:i'),
        ];
    }
}
