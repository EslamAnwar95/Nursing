<?php

namespace App\Http\Resources\Nurse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NurseInfoResource extends JsonResource
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
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'union_card_number' => $this->union_card_number,
            'national_id' => $this->national_id,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'rate' => $this->rate,
            'medical_history' => $this->medical_history,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,

            'is_active' => $this->is_active,
            'profile_image_url' => $this->profile_image_url,
            'id_card_front_url' => $this->id_card_front_url,
            'id_card_back_url' => $this->id_card_back_url,
            'union_card_back_url' => $this->union_card_back_url,
            'criminal_record_url' => $this->criminal_record_url,
            
            'type' => class_basename($this->resource) ,

            // 'is_verified' => $this->is_verified,
            // 'is_approved' => $this->is_approved,
            // 'is_blocked' => $this->is_blocked,
            // 'is_online' => $this->is_online,
            // 'is_available' => $this->is_available,
        
        ];
    }
}
