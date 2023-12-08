<?php

namespace App\Http\Controllers;

use App\Services\FCMService;
use App\Traits\NotificationService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use NotiService;

class NotificationController extends Controller
{
    use ResponseTrait;
    use NotificationService;

    public function testNotification(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $token = "fDMyO4aJRpuyqW7LeEEXPn:APA91bHMHYIMXSdtxf6ou47yNEIM1gFmNtQRtig5SDhFh1OSYgWqQOtsLueDtpO19nkSnOFWqiT2aZniURaZwjd4H5xmGlaLshveK537qLlqsSIKXTaSgqECkRgNbXHjOYkmOdpmllWe";

        // $sendNoti = $this->sendNotification($token, "Title", "this is message");
        $sendNoti = $this->sendEvent($token, "TOPUP");

        if ($sendNoti) {
            return $this->success("Success");
        } else {
            return $this->fail("Fail");
        }
    }
}
