<?php

namespace App\Http\Resources\Patient;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePatientResource extends JsonResource
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
            'full_name' => $this->full_name,
            'address' => $this->address ?? '',
            "phone_number" => $this->phone_number ?? '',
            'lat' => $this->lat ?? '',
            'lng' => $this->lng ?? '',
            // 'rate' => round($this->average_rating,2),
            // 'total_orders' => $this->total_orders,
            // 'total_rating' => $this->total_rating,
            'image' => $this->image,
            'type' => class_basename($this->resource) ,

        ];
    }
}
