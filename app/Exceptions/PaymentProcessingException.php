<?php

namespace App\Exceptions;

use Exception;

class PaymentProcessingException extends Exception
{
    protected $paymentData;

    public function __construct(string $message = "", array $paymentData = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->paymentData = $paymentData;
    }

    public function getPaymentData(): array
    {
        return $this->paymentData;
    }
}
