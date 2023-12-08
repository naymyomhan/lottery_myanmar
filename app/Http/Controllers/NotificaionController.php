<?php

namespace App\Http\Controllers;

use App\Services\FCMService;
use Illuminate\Http\Request;

class NotificaionController extends Controller
{
    public function server_notification(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        FCMService::send($request->title, $request->body, null);

        // Return a success response
        return response()->json([
            'message' => 'Notification sent successfully'
        ], 200);
    }
}
