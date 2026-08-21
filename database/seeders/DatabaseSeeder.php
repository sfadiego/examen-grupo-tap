<?php

namespace Database\Seeders;

use App\Enums\PerfilEnum;
use App\Models\Perfil;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SeccionSeeder::class,
            PerfilSeeder::class,
        ]);

        $admin = Usuario::factory()->create([
            'codigo' => 'USR-0001',
            'usuario' => 'admin@grupotap.com',
            'nombre' => 'Administrador',
            'foto' => 'https://placehold.co/200x200',
            'password' => bcrypt('password'),
        ]);

        $admin->perfiles()->sync(
            Perfil::where('nombre', PerfilEnum::Administrador->value)->pluck('id')->all()
        );
    }
}
