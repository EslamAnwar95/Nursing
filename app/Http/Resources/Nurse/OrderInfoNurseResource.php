<?php

namespace App\Http\Resources\Nurse;

use App\Http\Resources\Patient\HomePatientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderInfoNurseResource extends JsonResource
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
            'patient' => new HomePatientResource($this->patient),
            'status' => new StatusResource($this->status),
            // 'nurse' => new HomeNurseResource($this->provider),
            "price" => $this->price,
            "nurse_work_hours" => new WorkHourseNurseResource($this->nurse_hours),
            "schedule_at" => $this->schedule_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'type' => class_basename($this->resource),
        ];
    }
}
