<?php

namespace App\Exceptions;

use Exception;

class StockManagementException extends Exception
{
    protected $stockData;

    public function __construct(string $message = "", array $stockData = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->stockData = $stockData;
    }

    public function getStockData(): array
    {
        return $this->stockData;
    }
}
