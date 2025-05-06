<?php

namespace App\Interfaces;

use App\Interfaces\PaymentDataInterface;
interface PaymentStrategyInterface
{
    public function pay(PaymentDataInterface $data): string;
}