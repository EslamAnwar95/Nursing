<?php

namespace App\Models;

use Carbon\Carbon;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

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
        "description_ar",
        "description_en",
        "work_hours_ar",
        "work_hours_en",
        "experience_years",
        'lat',
        'lng',
        'is_active',
        'is_verified',


    ];

    protected $hidden = [
        'password',
        'remember_token',
        'media',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'lat' => 'double',
        'lng' => 'double',

    ];


    public function isVerified(): bool
    {
        return $this->is_verified == true;
    }

    public function workHours()
    {
        return $this->hasMany(NurseHours::class, 'nurse_id');
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'provider');
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(OrderTransaction::class, 'provider');
    }
    public function getFullNameAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null; // لو مفيش تاريخ ميلاد
        }

        return Carbon::parse($this->date_of_birth)->age;
    }

    public function registerMediaCollections(): void
    {

        $this->addMediaCollection('profile_image')->singleFile();
        $this->addMediaCollection('id_card_front')->singleFile();
        $this->addMediaCollection('id_card_back')->singleFile();
        $this->addMediaCollection('union_card_back')->singleFile();
        $this->addMediaCollection('criminal_record')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('nurse_avatar')
            ->fit(Fit::Contain, 300, 300)
            ->performOnCollections('profile_image')
            ->nonQueued();
    }

    public function getIdCardFrontUrlAttribute()
    {
        return $this->getImageFromCollection('id_card_front');
    }

    public function getIdCardBackUrlAttribute()
    {
        return $this->getImageFromCollection('id_card_back');
    }

    public function getUnionCardBackUrlAttribute()
    {
        return $this->getImageFromCollection('union_card_back');
    }

    public function getCriminalRecordUrlAttribute()
    {
        return $this->getImageFromCollection('criminal_record');
    }

    protected $appends = ['profile_image_url', 'id_card_front_url', 'id_card_back_url', 'union_card_back_url', 'criminal_record_url'];


    public function getProfileImageUrlAttribute(): string
    {
        $file = $this->getMedia('profile_image')->last();

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

    public function getImageFromCollection(string $collection): string
    {
        $file = $this->getMedia($collection)->last();
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

    public function scopeNearby(Builder $query, float $lat, float $lng, float $radius = 10): Builder
    {
        return $query->select('*', DB::raw("
            (6371 * acos(
                cos(radians($lat)) *
                cos(radians(lat)) *
                cos(radians($lng) - radians(lng)) +
                sin(radians($lat)) *
                sin(radians(lat))
            )) AS distance
        "))
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->having('distance', '<', $radius)
            ->orderBy('distance');
    }
}
