<?php

namespace App\Payments;

use App\Interfaces\PaymentDataInterface;
use App\Models\Order;

class OrderPaymentData implements PaymentDataInterface
{
    public function __construct(public Order $order) {}

    public function getAmount(): float
    {
        return $this->order->price;
    }

    public function getReferenceId(): string
    {
        return (string) $this->order->id;
    }

    public function getType(): string
    {
        return 'order';
    }
}
