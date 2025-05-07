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

    public function getBillingData(): array
    {
        return [
            'sender_name' => $this->order->patient->full_name,
            'email' => $this->order->patient->email,
            'phone' => $this->order->patient->phone_number,
            'address' => $this->order->patient->address,
            // "receiver_name" => $this->order->provider->full_name,
            // "receiver_phone" => $this->order->provider->phone_number,
            // "receiver_address" => $this->order->provider->address,

        ];
    }
}
