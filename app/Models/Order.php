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
        'status',
        'scheduled_at',
        'price',
        'notes',
    ];

    
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function provider()
    {
        return $this->morphTo();
    }
    
}
