<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class PaymobWebhookService
{
    protected array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function isValid(): bool
    {
        
        $receivedHmac = $payload['hmac'] ?? null;
        
        if (!$receivedHmac) {
            return false; // No HMAC provided
        }

        $orderedKeys = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success'
        ];

        $concatenated = '';
        foreach ($orderedKeys as $key) {
            $concatenated .= $this->payload[$key] ?? '';
        }

        $calculatedHmac = hash_hmac('sha512', $concatenated, Config::get('services.paymob.hmac_secret'));
        // dd($calculatedHmac,Config::get('services.paymob.hmac_secret'), $receivedHmac, hash_equals($calculatedHmac, $receivedHmac));


        return hash_equals($calculatedHmac, $receivedHmac);
    }

    public function getOrderId(): ?string
    {
        return $this->payload['order'] ?? null;
    }

    public function getSuccessStatus(): bool
    {
        return $this->payload['success'] ?? false;
    }

    public function getAmount(): float
    {
        return ($this->payload['amount_cents'] ?? 0) / 100;
    }

    // Add more getters if needed...
}
