<?php

namespace App\Http\Resources\Nurse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkHourseNurseResource extends JsonResource
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
            'hours' => $this->hours,
            'price' => $this->price,
            'day' => $this->day,
            'additional_hours' => $this->additional_hours,
            'additional_price' => $this->additional_price,
            'time' => $this->time,
            'status' => $this->status,
        ];
    }
}
