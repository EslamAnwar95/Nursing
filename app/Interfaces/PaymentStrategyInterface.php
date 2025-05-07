<?php

namespace App\Interfaces;

use App\Interfaces\PaymentDataInterface;
use App\Payments\PaymentResult;

interface PaymentStrategyInterface
{
    public function pay(PaymentDataInterface $data): PaymentResult;
}