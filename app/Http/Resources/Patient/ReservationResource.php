<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "scheduled_at" => $this->scheduled_at,
            "order_status" => optional($this->order_status)->name,
            "status" => $this->status,
            "provider_name" => $this->provider->full_name,
            "provider_id" => $this->provider_id,
            
            "type" => class_basename($this->provider),
            "address" => $this->provider->address,
            "provider_image" => $this->provider->profile_image_url,

        ];
    }
}
