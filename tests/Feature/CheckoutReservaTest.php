<?php

namespace Tests\Feature;

use App\Contracts\PaymentServiceInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Stripe\PaymentIntent;
use Tests\TestCase;

class CheckoutReservaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'usuario']);

        $this->user = User::factory()->create();
        $this->user->assignRole('usuario');
    }

    private function productWithPrice(float $price, int $stock = 10): Product
    {
        return Product::factory()->withStock($stock)->create(['price' => $price]);
    }

    private function addressFor(User $user): Address
    {
        return Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);
    }

    private function mockPaymentIntent(string $id = 'pi_test_789'): PaymentIntent
    {
        $paymentIntent = new PaymentIntent($id);
        $paymentIntent->client_secret = 'cs_test_secret';

        $this->mock(PaymentServiceInterface::class)
            ->shouldReceive('createPaymentIntent')
            ->once()
            ->andReturn($paymentIntent);

        return $paymentIntent;
    }

    public function test_create_payment_intent_exposes_reservation_expiry(): void
    {
        $product = $this->productWithPrice(500.00, 10);
        $address = $this->addressFor($this->user);
        $this->mockPaymentIntent();

        $response = $this
            ->actingAs($this->user)
            ->withSession([
                'cart' => [$product->id => ['name' => 'Producto', 'price' => 500.00, 'quantity' => 1]],
            ])
            ->postJson(route('usuario.cart.create-payment-intent'), [
                'shipping_address_id' => $address->id,
            ]);

        $response->assertOk();
        $this->assertNotNull($response->json('reserva_expira_en'));
        $this->assertTrue(
            Carbon::parse($response->json('reserva_expira_en'))->greaterThan(now()),
            'La reserva debe vencer en el futuro.'
        );
    }

    public function test_confirm_order_is_idempotent_on_retry(): void
    {
        $product = $this->productWithPrice(500.00, 10);
        $address = $this->addressFor($this->user);
        $this->mockPaymentIntent('pi_test_idem');

        $createResponse = $this
            ->actingAs($this->user)
            ->withSession([
                'cart' => [$product->id => ['name' => 'Producto', 'price' => 500.00, 'quantity' => 1]],
            ])
            ->postJson(route('usuario.cart.create-payment-intent'), [
                'shipping_address_id' => $address->id,
            ]);

        $orderId = $createResponse->json('order_id');

        $succeeded = new PaymentIntent('pi_test_idem');
        $succeeded->status = 'succeeded';

        $this->mock(PaymentServiceInterface::class)
            ->shouldReceive('retrievePaymentIntent')
            ->once()
            ->with('pi_test_idem')
            ->andReturn($succeeded);

        // Primera confirmación: marca pagada y descuenta stock (10 -> 9).
        $this->actingAs($this->user)->postJson(route('usuario.cart.confirm-order'), [
            'order_id' => $orderId,
            'payment_intent_id' => 'pi_test_idem',
        ])->assertOk();

        $this->assertSame(9, $product->fresh()->stock);

        // Reintento (doble clic): éxito sin volver a Stripe ni tocar stock.
        $this->mock(PaymentServiceInterface::class)->shouldNotReceive('retrievePaymentIntent');

        $this->actingAs($this->user)->postJson(route('usuario.cart.confirm-order'), [
            'order_id' => $orderId,
            'payment_intent_id' => 'pi_test_idem',
        ])->assertOk()->assertJson(['order_id' => $orderId]);

        $this->assertSame(9, $product->fresh()->stock);
        $order = Order::findOrFail($orderId);
        $this->assertSame(OrderStatus::PAID->value, $order->status);
        $this->assertSame(PaymentStatus::PAID->value, $order->payment_status);
    }

    public function test_reserva_estado_reports_active_reservation(): void
    {
        $product = $this->productWithPrice(500.00, 10);
        $address = $this->addressFor($this->user);
        $this->mockPaymentIntent();

        $this->actingAs($this->user)
            ->withSession([
                'cart' => [$product->id => ['name' => 'Producto', 'price' => 500.00, 'quantity' => 1]],
            ])
            ->postJson(route('usuario.cart.create-payment-intent'), [
                'shipping_address_id' => $address->id,
            ])->assertOk();

        $response = $this->actingAs($this->user)->getJson(route('usuario.cart.reserva-estado'));

        $response->assertOk()->assertJson(['tiene_reserva' => true]);
        $this->assertGreaterThan(0, $response->json('segundos'));
    }

    public function test_reserva_estado_without_reservation(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('usuario.cart.reserva-estado'));

        $response->assertOk()->assertJson([
            'tiene_reserva' => false,
            'segundos' => 0,
        ]);
    }
}
