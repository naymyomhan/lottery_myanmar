<?php

namespace App\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserMainWallet;

class AdminUserController extends Controller
{
    use ResponseTrait;
    //

    public function checkuser(Request $request){
    DB::beginTransaction();
    try {
        $validate = Validator::make($request->all(), [
            'user_code' => 'required',
        ]);

        // Check validation errors
        if ($validate->fails()) {
            return $this->fail($validate->errors()->first(), 400);
        }

        $user = User::where('refer_code', $request->user_code)->first();

        if (!$user) {
            return $this->fail("User not found.", 404);
        }

        $wallet = UserMainWallet::where('user_id',$user->id)->first();

        $userData = [
            'name' => $user->name,
            'phone' => $user->phone,
            'user_code' => $user->refer_code,
            'balance' => $wallet ? $wallet->balance : 0,
        ];
        
        return $this->success("User found", $userData);
    } catch (\Throwable $th) {
        DB::rollback();
        return $this->fail($th->getMessage() ? $th->getMessage() : "Server error", 500);
    }
    }

    public function userlist(Request $request) {
    try {
        $users = User::select('id', 'name', 'refer_code', 'phone', 'created_at')
            ->orderBy('created_at', 'desc') // Order by creation date in descending order
            ->take(30) // Take only the last 30 users
            ->get();

        $agnetUser = [];
        foreach ($users as $user) {
            $wallet = UserMainWallet::where('user_id', $user->id)->first();
            
            if ($wallet) { // Check if the wallet is found
                $formattedUser = [
                    'id' => $user->id,
                    'user_name' => $user->name,
                    'user_phone' => $user->phone,
                    'refer_code' => $user->refer_code,
                    'balance' => $wallet->balance,
                    'created_at' => Carbon::parse($user->created_at)->setTimezone('Asia/Yangon')->toDateTimeString()
                ];
                
                // Add the formatted user to the array
                $agnetUser[] = $formattedUser;
            }
        }

        // Reverse the array to have the latest users first
        // $agnetUser = array_reverse($agnetUser);

        return $this->success("Get User List successful", $agnetUser);
    } catch (\Throwable $th) {
        // No need to rollback if there's no database transaction
        return $this->fail($th->getMessage() ?: "Server error", 500);
    }
    }


}