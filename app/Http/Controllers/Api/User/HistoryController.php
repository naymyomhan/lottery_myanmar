<?php

namespace App\Http\Controllers\Api\User;

use App\Models\ThreeDHistory;
use App\Models\TwoDHistory;
use DateTime;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoryController extends Controller
{
      use ResponseTrait;
   public function getTwDHistory()
{
    try {
        $tdhistory = TwoDHistory::all(['set', 'value', 'open_time', 'twod', 'created_at']);
        
        $data = $tdhistory->map(function ($item, $key) {
            $item['created_at'] = Carbon::parse($item['created_at'])->toDateString();
            return $item;
        })->groupBy(function ($item) {
            return Carbon::parse($item['created_at'])->format('Y-m-d');
        })->map(function ($item, $key) {
            return [
                'date' => $key,
                'result' => $item->toArray()
            ];
        })->sortByDesc('date')->values();

        return $this->success("Get 2D History successful", $data);
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage() ? $th->getMessage() : "Server error", 500);
    }
}




public function getTeDHistory()
{
    try {
       $tdhistory = ThreeDHistory::orderBy('date', 'desc')->get(['number', 'date']);

        return $this->success("get 3D History successful", $tdhistory);
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
    }
}




}