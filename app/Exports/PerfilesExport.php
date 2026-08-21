<?php

namespace App\Exports;

use App\Models\Perfil;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PerfilesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return Perfil::all();
    }

    public function headings(): array
    {
        return ['Código', 'Nombre', 'Fecha de creación'];
    }

    public function map($perfil): array
    {
        return [
            $perfil->codigo,
            $perfil->nombre,
            $perfil->created_at->format('d/m/Y H:i'),
        ];
    }
}
