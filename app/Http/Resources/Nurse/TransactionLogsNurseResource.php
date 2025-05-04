<?php

namespace App\Http\Resources\Nurse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionLogsNurseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            "patient_name" => $this->order->patient->full_name,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'total_price' => (int) $this->total_price ?? 0,
            'service_value' => ((int) $this->vat_value + $this->app_fee) ?? 0,            
            'provider_earning' => (int) $this->provider_earning ?? 0,
            'status' => $this->status,
            'paid_at' => $this->paid_at ?? '',
        ];
    }
}
