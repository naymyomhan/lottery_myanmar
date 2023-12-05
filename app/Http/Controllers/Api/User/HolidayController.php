<?php

namespace App\Http\Controllers\Api\User;

use App\Models\TwoDCloseDay;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;

class HolidayController extends Controller
{
    // get Holiday
    use ResponseTrait;
    public function getHolidays()
    {
        try {
            $myholiday = TwoDCloseDay::all(['id', 'date','name', 'description']);
            $data = $myholiday;
            return $this->success("get Holiday successful", $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }
}