<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotions;
use App\Models\PromotionTopUps;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserPromotionController extends Controller
{
    //
     use ResponseTrait;

     //
    public function promotionlist(Request $request) {
    try {
        $promotions = Promotions::select('id','name', 'created_at')
            ->get()
            ->map(function ($promotion) {
                return [
                    'id' => $promotion->id,
                    'name' => $promotion->name,
                    'created_at' => Carbon::parse($promotion->created_at)->setTimezone('Asia/Yangon')->toDateTimeString()
                ];
            });

        return $this->success("Get Promotion List successful", $promotions);
    } catch (\Throwable $th) {
        // No need to rollback if there's no database transaction
        return $this->fail($th->getMessage() ?: "Server error", 500);
    }
    }

    // ptopuplist
    public function ptopuplist(Request $request) {
    try {
        $promotiontopups = PromotionTopUps::select('id', 'admin_id', 'user_id', 'promotion_id','amount', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(30) // Limit the results to the last 30 records
            ->get();

        $promotionUser = [];
        foreach ($promotiontopups as $promotiontopup) {
            $user = User::where('id', $promotiontopup->user_id)->first();
            $promotion = Promotions::where('id', $promotiontopup->promotion_id)->first();
            
            $formattedPromoitin = [
                    'id' => $promotiontopup->id,
                    'user_name' => $user->name,
                    'promotion_name' => $promotion->name,
                    'amount' => $promotiontopup->amount,
                    'created_at' => Carbon::parse($promotiontopup->created_at)->setTimezone('Asia/Yangon')->toDateTimeString()
            ];
                
            // Add the formatted user to the array
            $promotionUser[] = $formattedPromoitin;
        }

        // Reverse the array to have the latest users first
        // $pUser = array_reverse($promotionUser);

        return $this->success("Get Promotion TopUp List successful", $promotionUser);
    } catch (\Throwable $th) {
        // No need to rollback if there's no database transaction
        return $this->fail($th->getMessage() ?: "Server error", 500);
    }
    }

    // user_promotion_topup_create
   public function user_promotion_topup_create(Request $request){
    try {
        $validateRequest = Validator::make($request->all(), [
            'user_code' => 'required',
            'promotion_id' => 'required',
            'amount' => 'required|numeric',
        ]);

        // Check validation errors
        if ($validateRequest->fails()) {
            return $this->fail($validateRequest->errors()->first(), 400);
        }
        $requestedAmount = $request->amount;

        $user = User::where('refer_code', $request->user_code)->first();

        // Create the top-up record
        $topupRecord = new PromotionTopUps();
        $topupRecord->admin_id = Auth::id();
        $topupRecord->user_id = $user->id;
        $topupRecord->refer_code = $user->refer_code;
        $topupRecord->promotion_id = $request->promotion_id;
        $topupRecord->amount = $requestedAmount;
        $topupRecord->save();

        return $this->success("User Promotion TopUp successful.", $topupRecord);
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage(), 500);
    }
   }

}