<?php

namespace Tests\Feature;

use App\Enums\SeccionEnum;
use App\Models\Bitacora;
use App\Models\Perfil;
use App\Models\Seccion;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MongoDB\BSON\ObjectId;
use Tests\TestCase;

class PerfilTest extends TestCase
{
    use RefreshDatabase;

    private ?Usuario $usuario = null;
    private ?Perfil $perfil = null;
    private array $secciones = [];
    private array $perfilesCreados = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->secciones[] = Seccion::create([
            'codigo' => Seccion::generarCodigo(),
            'nombre' => SeccionEnum::PerfilesUsuarios->value,
        ]);

        $this->perfil = Perfil::create([
            'codigo' => Perfil::generarCodigo(),
            'nombre' => 'Perfil de Prueba Perfil',
        ]);
        $this->perfilesCreados[] = $this->perfil;

        $this->perfil->secciones()->sync(collect($this->secciones)->pluck('id')->all());

        $this->usuario = Usuario::create([
            'codigo' => Usuario::generarCodigo(),
            'usuario' => 'test-perfil-' . uniqid() . '@correo.com',
            'nombre' => 'Usuario Prueba Perfil',
            'foto' => 'fotos/test.jpg',
            'password' => 'password123',
        ]);

        $this->usuario->perfiles()->sync([$this->perfil->getKey()]);
    }

    // RefreshDatabase solo limpia SQLite;
    // tearDown se corre despues de cada test,
    //los datos de Mongo deben borrarse manualmente para no dejar residuos entre tests.
    protected function tearDown(): void
    {
        foreach ($this->perfilesCreados as $perfil) {
            Bitacora::where('modelo_id', new ObjectId($perfil->getKey()))->delete();
            $perfil->delete();
        }

        if ($this->usuario) {
            Bitacora::where('modelo_id', new ObjectId($this->usuario->getKey()))->delete();
            $this->usuario->delete();
        }

        foreach ($this->secciones as $seccion) {
            Bitacora::where('modelo_id', new ObjectId($seccion->getKey()))->delete();
            $seccion->delete();
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

    private function crearPerfil(array $overrides = []): Perfil
    {
        $perfil = Perfil::create(array_merge([
            'codigo' => Perfil::generarCodigo(),
            'nombre' => 'Perfil de prueba ' . uniqid(),
        ], $overrides));

        $this->perfilesCreados[] = $perfil;

        return $perfil;
    }

    public function test_index_lista_perfiles(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token())
            ->getJson('/api/perfiles');

        $response->assertOk();
        $response->assertJsonFragment(['codigo' => $this->perfil->codigo]);
    }

    public function test_store_crea_perfil(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token())
            ->postJson('/api/perfiles', [
                'nombre' => 'Supervisor',
            ]);

        $response->assertCreated();
        $this->assertNotEmpty($response->json('codigo'));

        $this->perfilesCreados[] = Perfil::find($response->json('id'));
    }

    public function test_store_falla_sin_nombre(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token())
            ->postJson('/api/perfiles', []);

        $response->assertStatus(422);
    }

    public function test_show_devuelve_detalle_con_secciones(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token())
            ->getJson('/api/perfiles/' . $this->perfil->getKey());

        $response->assertOk();
        $response->assertJsonPath('secciones.0.nombre', SeccionEnum::PerfilesUsuarios->value);
    }

    public function test_update_actualiza_nombre(): void
    {
        $perfil = $this->crearPerfil(['nombre' => 'Nombre Original']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token())
            ->putJson('/api/perfiles/' . $perfil->getKey(), [
                'nombre' => 'Nombre Actualizado',
            ]);

        $response->assertOk();
        $response->assertJsonPath('nombre', 'Nombre Actualizado');
    }

    public function test_destroy_elimina_perfil(): void
    {
        $perfil = $this->crearPerfil();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token())
            ->deleteJson('/api/perfiles/' . $perfil->getKey());

        $response->assertNoContent();

        $this->assertNull(Perfil::find($perfil->getKey()));
    }

    public function test_asignar_secciones_correctamente(): void
    {
        $otraSeccion = Seccion::create([
            'codigo' => Seccion::generarCodigo(),
            'nombre' => SeccionEnum::ConsultaProductos->value,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token())
            ->postJson('/api/perfiles/' . $this->perfil->getKey() . '/secciones', [
                'seccion_ids' => [$otraSeccion->getKey()],
            ]);

        $response->assertOk();
        $response->assertJsonPath('secciones.0.nombre', SeccionEnum::ConsultaProductos->value);

        Bitacora::where('modelo_id', new ObjectId($otraSeccion->getKey()))->delete();
        $otraSeccion->delete();
    }

    public function test_asignar_secciones_rechaza_id_inexistente(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token())
            ->postJson('/api/perfiles/' . $this->perfil->getKey() . '/secciones', [
                'seccion_ids' => ['000000000000000000000000'],
            ]);

        $response->assertStatus(422);
    }

    public function test_sin_seccion_perfiles_no_puede_acceder(): void
    {
        $this->perfil->secciones()->sync([]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token())
            ->getJson('/api/perfiles');

        $response->assertStatus(403);
    }
}
