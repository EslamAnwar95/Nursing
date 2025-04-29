<?php

use App\Interfaces\PaymentStrategyInterface;

class PaymentContext
{
    private $strategy;

    public function __construct(PaymentStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function pay(int $orderId): bool
    {
        return $this->strategy->pay($orderId);
    }
}