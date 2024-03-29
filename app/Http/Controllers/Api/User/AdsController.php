<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Ads;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\AppData;

class AdsController extends Controller
{
    //
    use ResponseTrait;
    public function getAds()
    {
        try {
            $myads = Ads::all(['id', 'title', 'image_location as image_url', 'description', 'url', 'type']);

            // Add the base URL to the image_url field
            $myads = $myads->map(function ($ad) {
                $ad->image_url = env('DO_STORAGE_URL') . $ad->image_url;
                return $ad;
            });

            $data = $myads;

            return $this->success("get Ads successful", $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }

    public function getAppData()
    {
        try {
            $app_data = AppData::first();

            return $this->success("get Ads successful", $app_data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }
}
