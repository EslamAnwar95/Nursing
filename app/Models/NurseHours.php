<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseHours extends Model
{
    protected $table = 'nurse_hours';

   
    protected $fillable = [
        'nurse_id',
        "hours",
        "price",
        "day",
        "additional_hours",
        "additional_price",
        "time",
        "status",
    ];

    public function nurse()
    {
        return $this->belongsTo(Nurse::class);
    }
}
