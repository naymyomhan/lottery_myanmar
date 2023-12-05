<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class TutorialController extends Controller
{   
    use ResponseTrait;
    public function get_tutorials()
    {
        try {
            $tutorials=Tutorial::orderBy('id', 'desc')->paginate(10);

            foreach ($tutorials as $tutorial) {
                $tutorial->video=env('DO_STORAGE_URL').$tutorial->video_location;
                $tutorial->image=env('DO_STORAGE_URL').$tutorial->image_location;
                unset($tutorial->image_path);
                unset($tutorial->image_name);
                unset($tutorial->image_location);
                unset($tutorial->video_path);
                unset($tutorial->video_name);
                unset($tutorial->video_location);
            }

            return $this->success('get tutorials successful',$tutorials);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }
}