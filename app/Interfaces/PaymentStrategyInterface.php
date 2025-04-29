<?php

namespace App\Interfaces;

interface PaymentStrategyInterface
{
    public function pay(int $orderId): bool;
}