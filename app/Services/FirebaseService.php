<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;
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
            ->withChangedTarget('token', $fcmToken); 
    
            try {
                $this->messaging->send($message);
            } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {

                DeviceToken::where('fcm_token', $fcmToken)->delete();
                Log::warning('FCM token deleted due to NotFound error', ['token' => $fcmToken]);
            } catch (\Throwable $e) {
                Log::error('FCM send failed', ['error' => $e->getMessage()]);
            }
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
