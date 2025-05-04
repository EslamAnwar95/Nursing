<?php


namespace App\Services;

use App\Models\NotificationLog;
use App\Services\FirebaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class NotificationDispatcherService
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Send and log a notification to a user (Patient, Nurse, etc.)
     */
    public function sendToUser(object $notifiable, string $title, string $body, array $data = []): void
    {
        $tokens = $notifiable->deviceTokens()->pluck('fcm_token')->toArray();
        foreach ($tokens as $token) {
            $this->firebase->sendToDevice($token, $title, $body, $data);
        }

        $this->log($notifiable, $title, $body, $data);
    }

    /**
     * Log the notification in the database
     */
    public function log(object $notifiable, string $title, string $body, array $data = []): void
    {
        NotificationLog::create([
            'notifiable_id'   => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'title'           => $title,
            'body'            => $body,
            'data'            => $data,
            'sent_at'         => Carbon::now(),
        ]);
    }
}
