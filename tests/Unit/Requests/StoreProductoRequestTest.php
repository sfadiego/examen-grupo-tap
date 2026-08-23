<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Productos\StoreProductoRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StoreProductoRequestTest extends TestCase
{
    private function precioRules(): array
    {
        $rules = (new StoreProductoRequest())->rules();

        return ['precio' => $rules['precio']];
    }

    #[DataProvider('precioValidosProvider')]
    public function test_precio_valido(string $precio): void
    {
        $validator = Validator::make(['precio' => $precio], $this->precioRules());

        $this->assertFalse($validator->fails(), "Se esperaba que '$precio' fuera válido.");
    }

    #[DataProvider('precioInvalidosProvider')]
    public function test_precio_invalido(string $precio): void
    {
        $validator = Validator::make(['precio' => $precio], $this->precioRules());

        $this->assertTrue($validator->fails(), "Se esperaba que '$precio' fuera inválido.");
    }

    public static function precioValidosProvider(): array
    {
        return [
            'un dígito' => ['5'],
            'tres dígitos' => ['999'],
            'con dos decimales' => ['499.99'],
            'con un decimal' => ['10.5'],
        ];
    }

    public static function precioInvalidosProvider(): array
    {
        return [
            'cuatro dígitos enteros' => ['1000'],
            'tres decimales' => ['10.999'],
            'negativo' => ['-5'],
            'con letras' => ['abc'],
        ];
    }
}
