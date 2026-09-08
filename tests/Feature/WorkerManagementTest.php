<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkerManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'administrador']);
        Role::create(['name' => 'trabajador']);
        Role::create(['name' => 'usuario']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('administrador');
    }

    public function test_admin_can_create_worker_and_role_is_assigned(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.workers.store'), [
            'name' => 'Lucia Operaciones',
            'email' => 'lucia.worker@example.com',
            'password' => 'CamposWorker2026!',
            'password_confirmation' => 'CamposWorker2026!',
        ]);

        $response->assertRedirect(route('admin.workers.index'));

        $worker = User::where('email', 'lucia.worker@example.com')->firstOrFail();

        $this->assertTrue($worker->hasRole('trabajador'));
    }

    public function test_admin_can_create_worker_when_role_is_missing(): void
    {
        Role::where('name', 'trabajador')->delete();

        $response = $this->actingAs($this->admin)->post(route('admin.workers.store'), [
            'name' => 'Lucia Operaciones',
            'email' => 'lucia.worker@example.com',
            'password' => 'CamposWorker2026!',
            'password_confirmation' => 'CamposWorker2026!',
        ]);

        $response->assertRedirect(route('admin.workers.index'));

        $worker = User::where('email', 'lucia.worker@example.com')->firstOrFail();

        $this->assertTrue($worker->hasRole('trabajador'));
    }

    public function test_worker_is_forbidden_from_worker_management(): void
    {
        $worker = User::factory()->create();
        $worker->assignRole('trabajador');

        $this->actingAs($worker)->get(route('admin.workers.index'))->assertForbidden();
        $this->actingAs($worker)->get(route('admin.workers.create'))->assertForbidden();
    }

    public function test_usuario_is_forbidden_from_worker_management(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('usuario');

        $this->actingAs($customer)->get(route('admin.workers.index'))->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_worker_management(): void
    {
        $this->get(route('admin.workers.index'))->assertRedirect(route('login'));
    }
}
