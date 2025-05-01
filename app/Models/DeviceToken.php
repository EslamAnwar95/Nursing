<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{

    protected $table = 'device_tokens';

    protected $fillable = [
        'fcm_token',
        'device_type',
    ];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function provider()
    {
        return $this->morphTo();
    }

    
}
