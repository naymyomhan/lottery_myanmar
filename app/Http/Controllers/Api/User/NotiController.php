<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotiController extends Controller
{
    use ResponseTrait;
public function userNotis()
{
    try {
        // Get user notifications
        $notifications = Notification::where('user_id', Auth::user()->id)
            ->get(['id', 'message', 'title', 'image_location as image_url','created_at','type']);

        // Add the base URL to the image_url field
        $notifications = $notifications->map(function ($notification) {
            $notification->image_url = env('DO_STORAGE_URL') . $notification->image_url;
            return $notification;
        });

        // Group notifications by date
        $groupedNotifications = $notifications->groupBy(function ($notification) {
            return $notification->created_at->format('Y-m-d'); // Change the format as needed
        });

        // Prepare the response data in the desired format
        $data = $groupedNotifications->map(function ($notifications, $date) {
            return [
                'date' => $date,
                'notifications' => $notifications,
            ];
        })->values()->all();

        return $this->success("Notifications retrieved successfully", $data);
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage() ?: "Server error", 500);
    }
}



    public function allNotis()
    {

        try {
            $allnotis = Notification::where('user_id', 0)->get(['id', 'message', 'title', 'image_location as image_url']);

            // Add the base URL to the image_url field
            $allnotis = $allnotis->map(function ($noti) {
                $noti->image_url = env('DO_STORAGE_URL') . $noti->image_url;
                return $noti;
            });

            $data = $allnotis;

            return $this->success("get Notification successful", $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }
}