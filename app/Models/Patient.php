<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Patient extends Authenticatable implements HasMedia
{

    use HasApiTokens, Notifiable, HasFactory, SoftDeletes, InteractsWithMedia;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'patients';

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'password',
        'date_of_birth',
        'gender',
        'national_id',
        'address',
        'medical_history',
        'emergency_contact_name',
        'emergency_contact_phone',
        'lat',
        'lng',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'lat' => 'float',
        'lng' => 'float',
    
    ];
    public function getFullNameAttribute($value)
    {
        return ucwords(strtolower($value));
    }
    public function setFullNameAttribute($value)
    {
        $this->attributes['full_name'] = ucwords(strtolower($value));
    }
    public function getPhoneNumberAttribute($value)
    {
        return preg_replace('/\D/', '', $value);
    }
    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = preg_replace('/\D/', '', $value);
    }
    public function getEmailAttribute($value)
    {
        return strtolower($value);
    }
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('patient_avatar')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    // public function getImageAttribute()
    // {
    //     $file = $this->getMedia('users')->last();

    //     $default = assets_url('img/default-image.jpeg');

    //     if (! $file) {
    //         return $default;
    //     }

    //     $file->url = $file->getUrl();

    //     $file->localUrl = asset('storage/'.$file->id.'/'.$file->file_name);

    //     $path = storage_path('app/public/'.$file->id.'/'.$file->file_name);

    //     if (file_exists($path)) {
    //         return $file->localUrl;
    //     }

    //     return $default;
    // }

    public function getImageAttribute()
    {
        $file = $this->getMedia('patient_avatar')->last();

        // $default = asset('storage/img/default-image.jpeg');
     
        $default =  env('APP_MEDIA_URL') . "/img/default-image.jpeg";
      
        if (! $file) {
            return $default;
        }


        $path = env('APP_MEDIA_URL') . "/{$file->id}/{$file->file_name}";

        if (UR_exists($path)) {
            return $path;
        }


        return $default;
    }
}
