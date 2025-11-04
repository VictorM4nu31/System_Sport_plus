<?php

namespace App\Exceptions;

use Exception;

class OrderProcessingException extends Exception
{
    protected $orderData;

    public function __construct(string $message = "", array $orderData = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->orderData = $orderData;
    }

    public function getOrderData(): array
    {
        return $this->orderData;
    }
}
