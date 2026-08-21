<?php

namespace App\Exports;

use App\Models\Usuario;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsuariosExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Usuario::all();
    }

    public function headings(): array
    {
        return ['Código', 'Usuario', 'Nombre', 'Fecha de creación'];
    }

    public function map($usuario): array
    {
        return [
            $usuario->codigo,
            $usuario->usuario,
            $usuario->nombre,
            $usuario->created_at->format('d/m/Y H:i'),
        ];
    }
}
