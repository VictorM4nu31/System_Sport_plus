<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DemoOrdersSeeder;
use Database\Seeders\DemoUsersSeeder;
use Database\Seeders\ProfessionalCatalogSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\SportsCategoriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoSeedersTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeders_crean_roles_usuarios_catalogo_y_pedidos(): void
    {
        $this->seed(RolesSeeder::class);
        $this->seed(DemoUsersSeeder::class);
        $this->seed(SportsCategoriesSeeder::class);
        $this->seed(ProfessionalCatalogSeeder::class);
        $this->seed(DemoOrdersSeeder::class);

        foreach (['administrador', 'trabajador', 'usuario'] as $role) {
            $this->assertDatabaseHas('roles', ['name' => $role]);
        }

        foreach ([
            'admin@sportplus.demo' => 'administrador',
            'trabajador@sportplus.demo' => 'trabajador',
            'usuario@sportplus.demo' => 'usuario',
        ] as $email => $role) {
            $user = User::where('email', $email)->firstOrFail();
            $this->assertTrue($user->hasRole($role));
        }

        $this->assertGreaterThan(0, Category::count());
        $this->assertGreaterThan(0, Product::count());

        $customer = User::where('email', 'usuario@sportplus.demo')->firstOrFail();
        $this->assertSame(4, Order::where('user_id', $customer->id)->count());
    }

    public function test_catalogo_no_referencia_imagenes_inexistentes(): void
    {
        $this->seed(RolesSeeder::class);
        $this->seed(DemoUsersSeeder::class);
        $this->seed(SportsCategoriesSeeder::class);
        $this->seed(ProfessionalCatalogSeeder::class);

        foreach (Product::whereNotNull('image')->pluck('image') as $image) {
            $this->assertTrue(
                Storage::disk('public')->exists('products/'.$image),
                "El producto referencia una imagen inexistente: products/{$image}"
            );
        }
    }

    public function test_catalogo_muestra_los_ejemplos_del_seeder(): void
    {
        $this->seed(RolesSeeder::class);
        $this->seed(DemoUsersSeeder::class);
        $this->seed(SportsCategoriesSeeder::class);
        $this->seed(ProfessionalCatalogSeeder::class);

        $esperados = [
            'NIK-PEG40-001' => ['Tenis Nike Air Zoom Pegasus 40', '2899.00', 'Calzado Deportivo', 'catalogo-calzado.jpg'],
            'ADI-AER-PLA-002' => ['Playera Casual Estampado Original', '499.00', 'Ropa Deportiva', 'catalogo-ropa.jpg'],
            'VOI-BAL-5-003' => ['Balón de Fútbol Voit Profesional No. 5', '999.00', 'Equipamiento de Fútbol', 'catalogo-futbol.jpg'],
            'SPA-TF1000-004' => ['Balón de Basketball Spalding TF-1000', '1299.00', 'Equipamiento de Basketball', 'catalogo-basketball.jpg'],
            'AMA-GTS4M-009' => ['Reloj Deportivo Amazfit GTS 4 Mini', '2799.00', 'Accesorios Deportivos', 'catalogo-accesorios.jpg'],
            'CAS-JER-020' => ['Bicicleta de Ruta Peugeot 700c', '13999.00', 'Ciclismo', 'catalogo-ciclismo2.jpg'],
        ];

        foreach ($esperados as $sku => [$nombre, $precio, $categoria, $imagen]) {
            $producto = Product::where('sku', $sku)->first();
            $this->assertNotNull($producto, "Falta el producto demo {$sku}");
            $this->assertSame($nombre, $producto->name);
            $this->assertSame($precio, number_format((float) $producto->price, 2, '.', ''));
            $this->assertSame($categoria, $producto->category->name);
            $this->assertSame($imagen, $producto->image);
            $this->assertTrue(
                Storage::disk('public')->exists('products/'.$imagen),
                "Falta el archivo de imagen: products/{$imagen}"
            );
        }

        $this->assertSame(20, Product::count());
    }

    public function test_demo_seeders_son_idempotentes(): void
    {
        $this->seed(RolesSeeder::class);
        $this->seed(DemoUsersSeeder::class);
        $this->seed(SportsCategoriesSeeder::class);
        $this->seed(ProfessionalCatalogSeeder::class);
        $this->seed(DemoOrdersSeeder::class);

        $counts = [
            User::count(),
            Category::count(),
            Product::count(),
            Order::count(),
        ];

        $this->seed(RolesSeeder::class);
        $this->seed(DemoUsersSeeder::class);
        $this->seed(SportsCategoriesSeeder::class);
        $this->seed(ProfessionalCatalogSeeder::class);
        $this->seed(DemoOrdersSeeder::class);

        $this->assertSame($counts, [
            User::count(),
            Category::count(),
            Product::count(),
            Order::count(),
        ]);
    }
}
