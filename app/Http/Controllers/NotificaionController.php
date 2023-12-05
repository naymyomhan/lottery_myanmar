<?php

namespace App\Http\Controllers;

use App\Services\FCMService;
use Illuminate\Http\Request;

class NotificaionController extends Controller
{
    public function server_notification(Request $request)
    {
        // Validate the title and body parameters
        $this->validate($request, [
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        // Send the notification using the FCMService
        FCMService::send($request->title, $request->body, null);

        // Return a success response
        return response()->json([
            'message' => 'Notification sent successfully'
        ], 200);
    }
}