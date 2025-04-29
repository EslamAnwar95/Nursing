<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NurseHours extends Model
{
    use SoftDeletes;
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

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
