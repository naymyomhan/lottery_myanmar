<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Traits\ResponseTrait;

class ExchangeRateController extends Controller
{
    use ResponseTrait;

    public function getExchangeRate()
    {
    try {
        $activeExchanges = ExchangeRate::where('is_active', true)->get();

        $data = $activeExchanges;

        return $this->success("Get active exchange rates successful", $data);
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage() ?: "Server error", 500);
    }
}
}