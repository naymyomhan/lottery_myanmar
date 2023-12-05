<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Games;
use App\Traits\ResponseTrait;

class AppController extends Controller
{
    use ResponseTrait;

    public function getApps()
    {
        try {
            $myapps = Games::all(['id', 'title', 'image_location as image_url', 'category', 'url', 'type']);

            // Add the base URL to the image_url field
            $myapps = $myapps->map(function ($ad) {
                $ad->image_url = env('DO_STORAGE_URL') . $ad->image_url;
                return $ad;
            });

            $data = $myapps;

            return $this->success("get Apps successful", $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }
}
