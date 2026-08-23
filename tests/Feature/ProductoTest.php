<?php

namespace Tests\Feature;

use App\Enums\SeccionEnum;
use App\Models\Bitacora;
use App\Models\Perfil;
use App\Models\Producto;
use App\Models\Seccion;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MongoDB\BSON\ObjectId;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    private ?Usuario $usuario = null;
    private ?Perfil $perfil = null;
    private array $secciones = [];
    private array $productosCreados = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->secciones[] = Seccion::create([
            'codigo' => Seccion::generarCodigo(),
            'nombre' => SeccionEnum::ConsultaProductos->value,
        ]);

        $this->secciones[] = Seccion::create([
            'codigo' => Seccion::generarCodigo(),
            'nombre' => SeccionEnum::AltaProductos->value,
        ]);

        $this->perfil = Perfil::create([
            'codigo' => Perfil::generarCodigo(),
            'nombre' => 'Perfil de Prueba Producto',
        ]);

        $this->perfil->secciones()->sync(collect($this->secciones)->pluck('id')->all());

        $this->usuario = Usuario::create([
            'codigo' => Usuario::generarCodigo(),
            'usuario' => 'test-producto-'.uniqid().'@correo.com',
            'nombre' => 'Usuario Prueba Producto',
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
        foreach ($this->productosCreados as $producto) {
            Bitacora::where('modelo_id', new ObjectId($producto->getKey()))->delete();
            $producto->delete();
        }

        if ($this->usuario) {
            Bitacora::where('modelo_id', new ObjectId($this->usuario->getKey()))->delete();
            $this->usuario->delete();
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

    private function crearProducto(array $overrides = []): Producto
    {
        $producto = Producto::create(array_merge([
            'codigo' => Producto::generarCodigo(),
            'nombre' => 'Producto de prueba '.uniqid(),
            'precio' => 100,
            'marca' => 'Marca X',
        ], $overrides));

        $this->productosCreados[] = $producto;

        return $producto;
    }

    public function test_index_lista_productos(): void
    {
        $producto = $this->crearProducto();

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->getJson('/api/productos');

        $response->assertOk();
        $response->assertJsonFragment(['codigo' => $producto->codigo]);
    }

    public function test_store_crea_producto_correctamente(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->postJson('/api/productos', [
                'nombre' => 'Monitor 24 pulgadas',
                'precio' => 499.99,
                'marca' => 'LG',
            ]);

        $response->assertCreated();
        $response->assertJsonPath('precio', '499.99');
        $this->assertNotEmpty($response->json('codigo'));

        $this->productosCreados[] = Producto::find($response->json('id'));
    }

    public function test_store_falla_con_precio_de_cuatro_digitos(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->postJson('/api/productos', [
                'nombre' => 'Producto Caro',
                'precio' => 1000,
                'marca' => 'Marca Y',
            ]);

        $response->assertStatus(422);
    }

    public function test_show_devuelve_producto(): void
    {
        $producto = $this->crearProducto(['nombre' => 'Producto Show']);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->getJson('/api/productos/'.$producto->getKey());

        $response->assertOk();
        $response->assertJsonFragment(['codigo' => $producto->codigo]);
    }

    public function test_update_actualiza_precio(): void
    {
        $producto = $this->crearProducto(['precio' => 50]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->putJson('/api/productos/'.$producto->getKey(), [
                'precio' => 75,
            ]);

        $response->assertOk();
        $response->assertJsonPath('precio', '75.00');
    }

    public function test_destroy_elimina_producto(): void
    {
        $producto = $this->crearProducto();

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->deleteJson('/api/productos/'.$producto->getKey());

        $response->assertNoContent();

        $this->assertNull(Producto::find($producto->getKey()));
    }

    public function test_sin_seccion_de_alta_no_puede_crear(): void
    {
        // Solo dejamos "Consulta de productos", quitando "Alta de productos".
        $this->perfil->secciones()->sync([$this->secciones[0]->getKey()]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token())
            ->postJson('/api/productos', [
                'nombre' => 'No debería crearse',
                'precio' => 10,
                'marca' => 'X',
            ]);

        $response->assertStatus(403);
    }
}
