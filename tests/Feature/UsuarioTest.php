<?php

namespace Tests\Feature;

use App\Enums\SeccionEnum;
use App\Models\Bitacora;
use App\Models\Perfil;
use App\Models\Seccion;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    private ?Usuario $usuario = null;
    private ?Perfil $perfil = null;
    private array $secciones = [];
    private array $usuariosCreados = [];

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->secciones[] = Seccion::create([
            'codigo' => Seccion::generarCodigo(),
            'nombre' => SeccionEnum::ConsultaUsuarios->value,
        ]);

        $this->secciones[] = Seccion::create([
            'codigo' => Seccion::generarCodigo(),
            'nombre' => SeccionEnum::AltaUsuario->value,
        ]);

        $this->perfil = Perfil::create([
            'codigo' => Perfil::generarCodigo(),
            'nombre' => 'Perfil de Prueba Usuario',
        ]);

        $this->perfil->secciones()->sync(collect($this->secciones)->pluck('id')->all());

        $this->usuario = Usuario::create([
            'codigo' => Usuario::generarCodigo(),
            'usuario' => 'test-usuario-'.uniqid().'@correo.com',
            'nombre' => 'Usuario Prueba',
            'foto' => 'fotos/test.jpg',
            'password' => 'password123',
        ]);

        $this->usuario->perfiles()->sync([$this->perfil->getKey()]);
        $this->usuariosCreados[] = $this->usuario;
    }

    // RefreshDatabase solo limpia SQLite;
    // tearDown se corre despues de cada test,
    //los datos de Mongo deben borrarse manualmente para no dejar residuos entre tests.
    protected function tearDown(): void
    {
        foreach ($this->usuariosCreados as $usuario) {
            Bitacora::where('modelo_id', new ObjectId($usuario->getKey()))->delete();
            $usuario->delete();
        }

        if ($this->perfil) {
            Bitacora::where('modelo_id', new ObjectId($this->perfil->getKey()))->delete();
            $this->perfil->delete();
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

    public function test_index_lista_usuarios(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->getJson('/api/usuarios');

        $response->assertOk();
        $response->assertJsonFragment(['codigo' => $this->usuario->codigo]);
    }

    public function test_store_crea_usuario_con_foto(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->post('/api/usuarios', [
                'usuario' => 'nueva-persona-'.uniqid().'@correo.com',
                'nombre' => 'Nueva Persona',
                'foto' => UploadedFile::fake()->image('foto.jpg'),
            ], ['Accept' => 'application/json']);

        $response->assertCreated();
        $this->assertNotEmpty($response->json('codigo'));
        $this->assertArrayNotHasKey('password', $response->json());

        Storage::disk('public')->assertExists($response->json('foto'));

        $this->usuariosCreados[] = Usuario::find($response->json('id'));
    }

    public function test_store_falla_sin_foto(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->postJson('/api/usuarios', [
                'usuario' => 'sin-foto@correo.com',
                'nombre' => 'Sin Foto',
            ]);

        $response->assertStatus(422);
    }

    public function test_store_falla_con_correo_duplicado(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->post('/api/usuarios', [
                'usuario' => $this->usuario->usuario,
                'nombre' => 'Duplicado',
                'foto' => UploadedFile::fake()->image('foto.jpg'),
            ], ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }

    public function test_show_devuelve_detalle_con_perfiles(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->getJson('/api/usuarios/'.$this->usuario->getKey());

        $response->assertOk();
        $response->assertJsonPath('perfiles.0.nombre', $this->perfil->nombre);
    }

    public function test_update_actualiza_nombre(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->putJson('/api/usuarios/'.$this->usuario->getKey(), [
                'nombre' => 'Nombre Actualizado',
            ]);

        $response->assertOk();
        $response->assertJsonPath('nombre', 'Nombre Actualizado');
    }

    public function test_destroy_elimina_usuario_y_borra_foto(): void
    {
        $foto = UploadedFile::fake()->image('foto.jpg')->store('fotos', 'public');

        $usuario = Usuario::create([
            'codigo' => Usuario::generarCodigo(),
            'usuario' => 'para-borrar-'.uniqid().'@correo.com',
            'nombre' => 'Para Borrar',
            'foto' => $foto,
            'password' => 'password123',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->deleteJson('/api/usuarios/'.$usuario->getKey());

        $response->assertNoContent();

        $this->assertNull(Usuario::find($usuario->getKey()));
        Storage::disk('public')->assertMissing($foto);

        Bitacora::where('modelo_id', new ObjectId($usuario->getKey()))->delete();
    }

    public function test_asignar_perfiles_correctamente(): void
    {
        $otroPerfil = Perfil::create([
            'codigo' => Perfil::generarCodigo(),
            'nombre' => 'Otro Perfil',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->postJson('/api/usuarios/'.$this->usuario->getKey().'/perfiles', [
                'perfil_ids' => [$otroPerfil->getKey()],
            ]);

        $response->assertOk();
        $response->assertJsonPath('perfiles.0.nombre', 'Otro Perfil');

        Bitacora::where('modelo_id', new ObjectId($otroPerfil->getKey()))->delete();
        $otroPerfil->delete();
    }

    public function test_asignar_perfiles_rechaza_id_inexistente(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->postJson('/api/usuarios/'.$this->usuario->getKey().'/perfiles', [
                'perfil_ids' => ['000000000000000000000000'],
            ]);

        $response->assertStatus(422);
    }
}
