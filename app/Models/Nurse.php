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


class Nurse extends  Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable, HasFactory, SoftDeletes, InteractsWithMedia;


    protected $table = 'nurses';

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'password',
        'date_of_birth',
        'gender',
        'national_id',
        'address',
        'union_number',
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
    ];
    public function getFullNameAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('profile_image')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }


    public function registerMediaCollections(): void
    {
    
        // $this->addMediaCollection('profile_image')->singleFile();
        $this->addMediaCollection('id_card_front')->singleFile();
        $this->addMediaCollection('id_card_back')->singleFile();
        $this->addMediaCollection('union_card_back')->singleFile();
        $this->addMediaCollection('criminal_record')->singleFile();
    }

 
    public function getIdCardFrontUrlAttribute()
    {
        return $this->getImageFromCollection('id_card_front');
    }

    // public function getIdCardBackUrlAttribute()
    // {
    //     return $this->getImageFromCollection('id_card_back');
    // }

    // public function getUnionCardBackUrlAttribute()
    // {
    //     return $this->getImageFromCollection('union_card_back');
    // }

    // public function getCriminalRecordUrlAttribute()
    // {
    //     return $this->getImageFromCollection('criminal_record');
    // }

    protected function getImageFromCollection($collection, $conversion = '')
    {
        $file = $this->getMedia($collection)->last();
        $default = asset('/img/default-file.png');

        if (! $file) {
            return $default;
        }

        return $conversion
            ? $file->getUrl($conversion)
            : $file->getUrl();
    }
}
