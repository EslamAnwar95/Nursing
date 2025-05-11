<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePatientMostCommonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "total_favor" => $this->total,
            "type" => class_basename($this->provider),
            "id" => $this->provider_id,
            "full_name" => $this->provider->full_name,
            "address" => $this->provider->address ?? '',
            "image" => $this->provider->profile_image_url,
            "rate" => $this->provider->average_rating,
            "total_rate" => $this->provider->total_rating,

        ];
        
    }
}
