<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = 'orders';

    protected $fillable = [
        'patient_id',
        'provider_id',
        'provider_type',
        'status_id',
        'scheduled_at',
        'price',
        'notes',
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function provider()
    {
        return $this->morphTo();
    }

    public function transaction()
    {
        return $this->hasOne(OrderTransaction::class);
    }

    public function nurseOrderDetail()
    {
        return $this->hasOne(NurseOrderDetail::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function nurseHour()
    {
        return $this->hasOne(NurseHours::class, 'id', 'nurse_hours_id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'order_id', 'id');
    }
}
