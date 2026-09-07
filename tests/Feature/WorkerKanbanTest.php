<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkerKanbanTest extends TestCase
{
    use RefreshDatabase;

    private User $worker;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'trabajador']);
        Role::create(['name' => 'usuario']);

        $this->worker = User::factory()->create();
        $this->worker->assignRole('trabajador');

        $this->client = User::factory()->create();
        $this->client->assignRole('usuario');
    }

    private function paidOrder(): Order
    {
        return Order::factory()->paid()->create(['user_id' => $this->client->id]);
    }

    public function test_worker_index_shows_pending_and_accepted_today(): void
    {
        $this->paidOrder();

        $this->actingAs($this->worker)
            ->get(route('trabajador.orders.index'))
            ->assertOk()
            ->assertSee('Por aceptar', false)
            ->assertSee('Aceptados hoy', false);
    }

    public function test_accept_order_via_json_for_kanban(): void
    {
        $order = $this->paidOrder();

        $response = $this->actingAs($this->worker)
            ->patchJson(route('trabajador.orders.accept', $order->id));

        $response->assertOk()->assertJson(['order_id' => $order->id, 'status' => 'confirmed']);
        $this->assertSame('confirmed', $order->fresh()->status);
    }

    public function test_accept_order_keeps_redirect_without_json(): void
    {
        $order = $this->paidOrder();

        $this->actingAs($this->worker)
            ->patch(route('trabajador.orders.accept', $order->id))
            ->assertRedirect(route('trabajador.orders.index'));
    }

    public function test_reject_order_validates_reason_via_json(): void
    {
        $order = $this->paidOrder();

        $this->actingAs($this->worker)
            ->patchJson(route('trabajador.orders.reject', $order->id), [
                'rejection_reason' => 'corto',
            ])
            ->assertStatus(422);

        $this->actingAs($this->worker)
            ->patchJson(route('trabajador.orders.reject', $order->id), [
                'rejection_reason' => 'Producto sin existencias en almacén central.',
            ])
            ->assertOk()->assertJson(['order_id' => $order->id, 'status' => 'rejected']);

        $fresh = $order->fresh();
        $this->assertSame('rejected', $fresh->status);
        $this->assertSame($this->worker->id, $fresh->rejected_by);
    }

    public function test_search_orders_by_id_and_client(): void
    {
        $order = $this->paidOrder();

        $byId = $this->actingAs($this->worker)
            ->getJson(route('trabajador.orders.search', ['q' => (string) $order->id]));

        $byId->assertOk()->assertJsonPath('data.0.id', $order->id);
        $byId->assertJsonMissingPath('data.0.stripe');

        $byClient = $this->actingAs($this->worker)
            ->getJson(route('trabajador.orders.search', ['q' => substr($this->client->name, 0, 4)]));

        $byClient->assertOk()->assertJsonPath('data.0.id', $order->id);
    }

    public function test_client_cannot_use_worker_endpoints(): void
    {
        $order = $this->paidOrder();

        $this->actingAs($this->client)->get(route('trabajador.orders.index'))->assertForbidden();
        $this->actingAs($this->client)->patchJson(route('trabajador.orders.accept', $order->id))->assertForbidden();
        $this->actingAs($this->client)->getJson(route('trabajador.orders.search'))->assertForbidden();
    }
}
