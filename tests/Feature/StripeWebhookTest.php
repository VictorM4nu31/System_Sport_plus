<?php

namespace Tests\Feature;

use App\Services\StripePaymentService;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    public function test_webhook_returns_200_when_service_handles_event(): void
    {
        $this->mock(StripePaymentService::class)
            ->shouldReceive('handleWebhook')
            ->once()
            ->andReturnNull();

        $this->postJson(route('stripe.webhook'), ['id' => 'evt_test_ok'])->assertOk();
    }

    public function test_webhook_returns_400_when_service_rejects_event(): void
    {
        $this->mock(StripePaymentService::class)
            ->shouldReceive('handleWebhook')
            ->once()
            ->andThrow(new \Exception('bad signature'));

        $this->postJson(route('stripe.webhook'), ['id' => 'evt_test_bad'])->assertStatus(400);
    }
}
