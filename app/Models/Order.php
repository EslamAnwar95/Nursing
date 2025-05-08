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
        "payment_status", // 'pending', 'paid', 'failed', 'refunded'
        "status", // 'pending', 'confirmed', 'in_progress', 'completed', 'cancelled'
        'notes',
        'paymob_order_id',
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

    public function order_status()
    {
        return $this->belongsTo(Status::class , 'status_id', 'id');
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
