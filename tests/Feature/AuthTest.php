<?php

namespace Tests\Feature;

use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MongoDB\BSON\ObjectId;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private ?Usuario $usuarioPrueba = null;

    // RefreshDatabase solo limpia SQLite;
    // tearDown se corre despues de cada test,
    //los datos de Mongo deben borrarse manualmente para no dejar residuos entre tests.
    protected function tearDown(): void
    {
        if ($this->usuarioPrueba) {
            Bitacora::where('modelo_id', new ObjectId($this->usuarioPrueba->getKey()))->delete();
            $this->usuarioPrueba->delete();
        }

        parent::tearDown();
    }

    private function crearUsuarioPrueba(string $password = 'password123'): Usuario
    {
        $this->usuarioPrueba = Usuario::create([
            'codigo' => Usuario::generarCodigo(),
            'usuario' => 'test-auth-'.uniqid().'@correo.com',
            'nombre' => 'Usuario de Prueba',
            'foto' => 'fotos/test.jpg',
            'password' => $password,
        ]);

        return $this->usuarioPrueba;
    }

    public function test_login_exitoso(): void
    {
        $usuario = $this->crearUsuarioPrueba('password123');

        $response = $this->postJson('/api/login', [
            'usuario' => $usuario->usuario,
            'password' => 'password123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['usuario', 'token']);
    }

    public function test_login_con_password_incorrecto(): void
    {
        $usuario = $this->crearUsuarioPrueba('password123');

        $response = $this->postJson('/api/login', [
            'usuario' => $usuario->usuario,
            'password' => 'incorrecta',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_con_usuario_inexistente(): void
    {
        $response = $this->postJson('/api/login', [
            'usuario' => 'no-existe@correo.com',
            'password' => 'cualquiera',
        ]);

        $response->assertStatus(422);
    }

    public function test_me_devuelve_usuario_autenticado(): void
    {
        $usuario = $this->crearUsuarioPrueba('password123');

        $login = $this->postJson('/api/login', [
            'usuario' => $usuario->usuario,
            'password' => 'password123',
        ]);

        $token = $login->json('token');

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/me');

        $response->assertOk();
        $response->assertJsonFragment(['usuario' => $usuario->usuario]);
    }

    public function test_logout_revoca_el_token(): void
    {
        $usuario = $this->crearUsuarioPrueba('password123');

        $login = $this->postJson('/api/login', [
            'usuario' => $usuario->usuario,
            'password' => 'password123',
        ]);

        $token = $login->json('token');

        $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout')
            ->assertOk();

        // Laravel reutiliza la misma app/guard resuelto entre llamadas dentro
        // de un mismo test; forzamos un reinicio para simular una petición
        // realmente nueva (así se comporta en producción, verificado con curl).
        $this->refreshApplication();

        $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/me')
            ->assertStatus(401);
    }
}
