<?php

namespace App\Traits;

use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;

trait NotificationService
{
    public function sendNotification($token, $title, $message)
    {
        try {
            $message = CloudMessage::fromArray([
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                ],
            ]);

            Firebase::messaging()->send($message);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function sendEvent($token, $event)
    {
        try {
            $message = CloudMessage::fromArray([
                'token' => $token,
                'data' => [
                    'event' => $event,
                ],
            ]);

            Firebase::messaging()->send($message);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
