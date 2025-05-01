<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\RegistrationToken;
use Kreait\Firebase\Contract\Messaging;

class FirebaseService
{
    protected Messaging $messaging;

    public function __construct()
    {
        $this->messaging = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createMessaging();
    }

    /**
     * Send a notification to a single device.
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = []): void
    {
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data)
            ->withChangedTarget('token', $fcmToken); // ✅ دي الطريقة الرسمية الحالية
    
        $this->messaging->send($message);
    }

    /**
     * Send a notification to multiple devices.
     */
    public function sendToMultipleDevices(array $fcmTokens, string $title, string $body, array $data = []): void
    {
        $tokens = array_map(fn($token) => RegistrationToken::fromValue($token), $fcmTokens);

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        $this->messaging->sendMulticast($message, $tokens);
    }
}
