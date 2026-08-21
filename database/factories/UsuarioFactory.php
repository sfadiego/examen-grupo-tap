<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => 'USR-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'usuario' => fake()->unique()->safeEmail(),
            'nombre' => fake()->name(),
            'foto' => fake()->imageUrl(200, 200, 'people'),
            'telefono' => fake()->optional()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'created_at' => now(),
        ];
    }
}
