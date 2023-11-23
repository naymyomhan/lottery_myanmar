<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\PromotionTopUps;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CashOut;
use App\Models\TopUp;
use Illuminate\Support\Facades\Auth;

class AdminProfileController extends Controller
{
    use ResponseTrait;
    //

    public function profile()
   {
    try {
        $user = Auth::user();

        if (!$user) {
            return $this->fail("User not found.", 404);
        }

        $adminData = [
            'name' => $user->name,
            'email' => $user->email,
        ];


        return $this->success("Admin profile retrieved successfully.", $adminData);
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage() ?: "Server error", 500);
    }
    }
    // anlys

public function anlys(){
    try {
        $today = Carbon::now()->format('Y-m-d');
        $todayUserCount = User::whereDate('created_at', $today)->count();
        $userCount = User::count(); 
        $userTopUpTotal = TopUp::where('success', 1)->sum('amount'); // Sum of user top-up amounts
        $userWithdrawTotal = CashOut::where('success', 1)->sum('amount'); // Sum of user top-up amounts
        $userPromotinTopUp = PromotionTopUps::sum('amount'); // Sum of user top-up amounts
        
        $dataArray = [
            ['key' => 'Today Register', 'value' => $todayUserCount],
            ['key' => 'User Count', 'value' => $userCount],
            ['key' => 'TopUp Total Success', 'value' => $userTopUpTotal],
            ['key' => 'Promotion Topup', 'value' => $userPromotinTopUp],
            ['key' => 'Withdraw Total Success', 'value' => $userWithdrawTotal],
        ];
        
        foreach ($dataArray as &$dataEntry) {
            if ($dataEntry['value'] === null) {
                $dataEntry['value'] = 0;
            }
        }
        
        return $this->success("Analysis retrieved successfully", $dataArray);
    } catch (\Throwable $th) {
        // Assuming `fail` is defined elsewhere.
        return $this->fail($th->getMessage(), 500);
    }
}

}