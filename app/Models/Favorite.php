<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    
    protected $table = 'favorites';

    protected $fillable = [
        'patient_id',
        'provider_id',
        'provider_type',
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
