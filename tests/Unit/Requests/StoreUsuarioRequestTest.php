<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Usuarios\StoreUsuarioRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StoreUsuarioRequestTest extends TestCase
{
    private function telefonoRules(): array
    {
        $rules = (new StoreUsuarioRequest())->rules();

        return ['telefono' => $rules['telefono']];
    }

    #[DataProvider('telefonoValidosProvider')]
    public function test_telefono_valido(?string $telefono): void
    {
        $validator = Validator::make(['telefono' => $telefono], $this->telefonoRules());

        $this->assertFalse($validator->fails(), "Se esperaba que '$telefono' fuera válido.");
    }

    #[DataProvider('telefonoInvalidosProvider')]
    public function test_telefono_invalido(string $telefono): void
    {
        $validator = Validator::make(['telefono' => $telefono], $this->telefonoRules());

        $this->assertTrue($validator->fails(), "Se esperaba que '$telefono' fuera inválido.");
    }

    public static function telefonoValidosProvider(): array
    {
        return [
            'nulo (opcional)' => [null],
            'formato E.164 válido' => ['+523141234567'],
            'código de país corto' => ['+15551234567'],
        ];
    }

    public static function telefonoInvalidosProvider(): array
    {
        return [
            'sin el signo +' => ['523141234567'],
            'con espacios' => ['+52 314 123 4567'],
            'muy corto' => ['+5231'],
            'con letras' => ['+52abc1234567'],
        ];
    }
}
