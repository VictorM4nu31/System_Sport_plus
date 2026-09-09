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
