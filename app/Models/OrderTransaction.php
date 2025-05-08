<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderTransaction extends Model
{
    
    protected $table = 'order_transactions';

    protected $fillable = [
        'order_id',
        'provider_id',
        'provider_type',
        'payment_method',
        'payment_status',                          
        'total_price',
        'vat_value',
        'app_fee',       
        'provider_earning',
        'status',
        'paid_at',
        "payment_id",
    ];


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function provider(): MorphTo
    {
        return $this->morphTo();
    }
}
