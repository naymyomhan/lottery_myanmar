<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FCMService
{
    public static function send($title, $body, $image)
    {
        Http::acceptJson()->withToken(env('FCM_TOKEN'))->post(
            'https://fcm.googleapis.com/fcm/send',
            [
                'to' => "/topics/stockopen",
                'notification' => [
                    "title" => $title,
                    "body" => $body,
                    "image" => $image,
                ],
            ]
        );
    }

    public static function userSend($title, $body, $image, $userFCMToken)
    {
        Http::acceptJson()->withToken(env('FCM_TOKEN'))->post(
            'https://fcm.googleapis.com/fcm/send',
            [
                'to' => $userFCMToken,
                'notification' => [
                    "title" => $title,
                    "body" => $body,
                    "image" => $image,
                ],
            ]
        );
    }
}
