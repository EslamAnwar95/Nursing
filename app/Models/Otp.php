<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = 'otps';

    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'verified_at',
    ];

    protected $dates = ['expires_at', 'verified_at'];
}
