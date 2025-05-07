<?php

namespace App\Payments;

use App\Interfaces\PaymentDataInterface;

class SettlementPaymentData implements PaymentDataInterface
{
    public function __construct(
        public float $amount,
        public int $providerId,
        public string $providerType,
    ) {}

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getReferenceId(): string
    {
        return "settlement-{$this->providerId}";
    }

    public function getType(): string
    {
        return 'settlement';
    }

    public function getBillingData(): array
    {
        return [
            'provider_id' => $this->providerId,
            'provider_type' => $this->providerType,
        ];
    }
}
