<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationLog extends Model
{
    protected $table = 'notification_logs';

    
    protected $fillable = [
      'notifiable_id', 'notifiable_type', 'title', 'body', 'data', 'is_read', 'read_at', 'sent_at'
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
    ];


    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTitleAttribute()
    {
        
        return __($this->attributes['title']);
    }
    public function getBodyAttribute()
    {
        return __($this->attributes['body']);
    }
    // public function getDataAttribute()
    // {
    //     return json_decode($this->attributes['data'], true);
    // }
    // public function getTranslation($field, $locale)
    // {
        
    //     $translations = json_decode($this->{$field}, true);
    //     return $translations[$locale] ?? $this->{$field};
    // }
}
