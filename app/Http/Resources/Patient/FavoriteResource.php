<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
            "provider_id" => $this->provider_id,
            'type' => class_basename($this->provider_type),
            'name' => $this->provider->full_name ?? "",
            'phone' => $this->provider->phone_number ?? "",
            "address" => $this->provider->address ?? "",
            'image' => $this->provider->profile_image_url ?? "",
        ];
    }
}
