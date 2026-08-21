<?php

namespace Database\Seeders;

use App\Enums\SeccionEnum;
use App\Models\Seccion;
use Illuminate\Database\Seeder;

class SeccionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SeccionEnum::cases() as $seccion) {
            Seccion::create([
                'codigo' => Seccion::generarCodigo(),
                'nombre' => $seccion->value,
            ]);
        }
    }
}
