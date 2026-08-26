<?php

namespace App\Http\Controllers;

use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Exception;

class StripeWebhookController extends Controller
{
    protected StripePaymentService $paymentService;

    public function __construct(StripePaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle incoming Stripe webhook
     *
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        try {
            $this->paymentService->handleWebhook($request);

            return response('Webhook handled successfully', 200);
        } catch (Exception $e) {
            Log::channel('payments')->error('Webhook handling failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response('Webhook handling failed', 400);
        }
    }
}
