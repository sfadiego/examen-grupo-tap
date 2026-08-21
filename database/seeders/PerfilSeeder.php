<?php

namespace Database\Seeders;

use App\Enums\PerfilEnum;
use App\Enums\SeccionEnum;
use App\Models\Perfil;
use App\Models\Seccion;
use Illuminate\Database\Seeder;

class PerfilSeeder extends Seeder
{
    public function run(): void
    {
        // Crear perfil de administrador y asignar todas las secciones
        $administrador = Perfil::create([
            'codigo' => Perfil::generarCodigo(),
            'nombre' => PerfilEnum::Administrador->value,
        ]);

        // Asignar todas las secciones al perfil de administrador
        $administrador->secciones()->sync(
            Seccion::pluck('id')->all()
        );

        // Crear perfil de consulta y asignar secciones específicas
        $consulta = Perfil::create([
            'codigo' => Perfil::generarCodigo(),
            'nombre' => PerfilEnum::Consulta->value,
        ]);

        // Asignar secciones específicas al perfil de consulta
        $consulta->secciones()->sync(
            Seccion::whereIn('nombre', [
                SeccionEnum::ConsultaProductos->value,
                SeccionEnum::ConsultaUsuarios->value,
            ])->pluck('id')->all()
        );
    }
}
