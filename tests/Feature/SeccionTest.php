<?php

namespace Tests\Feature;

use App\Models\Bitacora;
use App\Models\Seccion;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MongoDB\BSON\ObjectId;
use Tests\TestCase;

class SeccionTest extends TestCase
{
    use RefreshDatabase;

    private ?Usuario $usuario = null;
    private ?Seccion $seccion = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seccion = Seccion::create([
            'codigo' => Seccion::generarCodigo(),
            'nombre' => 'Seccion de Prueba',
        ]);

        $this->usuario = Usuario::create([
            'codigo' => Usuario::generarCodigo(),
            'usuario' => 'test-seccion-'.uniqid().'@correo.com',
            'nombre' => 'Usuario Prueba Seccion',
            'foto' => 'fotos/test.jpg',
            'password' => 'password123',
        ]);
    }

    // RefreshDatabase solo limpia SQLite;
    // tearDown se corre despues de cada test,
    //los datos de Mongo deben borrarse manualmente para no dejar residuos entre tests.
    protected function tearDown(): void
    {
        if ($this->usuario) {
            Bitacora::where('modelo_id', new ObjectId($this->usuario->getKey()))->delete();
            $this->usuario->delete();
        }

        if ($this->seccion) {
            Bitacora::where('modelo_id', new ObjectId($this->seccion->getKey()))->delete();
            $this->seccion->delete();
        }

        parent::tearDown();
    }

    private function token(): string
    {
        $login = $this->postJson('/api/login', [
            'usuario' => $this->usuario->usuario,
            'password' => 'password123',
        ]);

        return $login->json('token');
    }

    public function test_index_lista_secciones(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->getJson('/api/secciones');

        $response->assertOk();
        $response->assertJsonFragment(['codigo' => $this->seccion->codigo]);
    }

    public function test_index_requiere_autenticacion(): void
    {
        $response = $this->getJson('/api/secciones');

        $response->assertStatus(401);
    }
}
