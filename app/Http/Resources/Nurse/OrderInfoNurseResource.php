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
        // dd($this->order_status);
        return [
            'id' => $this->id,
            'patient' => new HomePatientResource($this->patient),
            'status' => new StatusResource($this->order_status),
            // 'nurse' => new HomeNurseResource($this->provider),
            "price" => $this->price,
            "nurse_work_hours" => new WorkHourseNurseResource($this->nurse_hours),
            "schedule_at" => $this->schedule_at ?? '',
            "address" => $this->nurseOrderDetail->address ?? '',
            "city" => $this->nurseOrderDetail->city ?? '',
            "area" => $this->nurseOrderDetail->area ?? '',
            "street" => $this->nurseOrderDetail->street ?? '',
            "building" => $this->nurseOrderDetail->building ?? '',
            "floor" => $this->nurseOrderDetail->floor ?? '',
            "apartment" => $this->nurseOrderDetail->apartment ?? '',
            "patient_condition" => $this->nurseOrderDetail->patient_condition ?? '',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'type' => class_basename($this->resource),
        ];
    }
}
