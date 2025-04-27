<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseOrderDetail extends Model
{
    protected $table = 'nurse_order_details';

    protected $fillable = [
        'order_id',
        'nurse_hours_id',
        'patient_condition',
        'visit_date',
        'visit_time',
   
        'other_nurse_notes',
        'address',
        'city',
        'area',
        'street',
        'building',
        'floor',
        'apartment',
       
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function nurse()
    {
        return $this->belongsTo(Nurse::class);
    }

    public function nurseHours()
    {
        return $this->belongsTo(NurseHours::class);
    }
}
