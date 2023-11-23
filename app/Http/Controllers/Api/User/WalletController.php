<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\UserPromoWallet;
use App\Traits\ResponseTrait;
use Aws\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Cog\Laravel\Ban\Traits\Bannable;
use App\Models\CashOut;
use App\Models\TopUp;
use Carbon\Carbon;

class WalletController extends Controller
{   
    use ResponseTrait;
    use Bannable;

    public function get_user_wallet()
    {  
        
        try {
            $user=Auth::user();
            
            if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                 'message' => 'You are banned from accessing this feature.',
            ],403);
            }

            //check if uncompleted transaction exists
            $old_topup=Auth::user()->topups->last();
            if($old_topup){
                if($old_topup->success==0){
                    $pending=true;
                }else{
                    $pending=false;
                }
                //if uncompleted transaction exists check if unconfirmed transaction exists
                if($old_topup->success==0 && ($old_topup->payment_transaction_number==null || $old_topup->payment_transaction_number=="")){
                    $prepared=true;
                }else{
                    $prepared=false;
                }
            }else{
                $pending=false;
                $prepared=false;
            }

             $promoWallet = $user->userPromoWallet;

            // return $promoWallet;
            if (!$promoWallet) {
                UserPromoWallet::create([
                    "user_id" => $user->id,
                    "balance" => 0,
                ]);
            }
            // env('DO_STORAGE_URL') . $ad->image_url;

            $wallet=[
                'id'=>$user->id,
                'name'=>$user->name,
                'user_code'=>$user->refer_code,
                'phone'=>$user->refer_code,
                'image'=>env('DO_STORAGE_URL').$user->profile_picture_location,
                'main_wallet'=>$user->main_wallet->balance,
                'game_wallet'=>$user->game_wallet->balance,
                'promo_wallet' => $user->userPromoWallet->balance,
                'has_prepare'=>$prepared,
                'has_pending'=>$pending,
            ];

            $data=[
                "wallet"=>$wallet,
            ];

            return $this->success("get user wallet successful",$data);

        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function transfer_to_game_wallet(Request $request)
    {   
        
        if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                 'message' => 'You are banned from accessing this feature.',
            ],403);
        }
        
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(),
            [
                'amount'=>'required',
            ]);
            
            if($validate->fails()){
                if(isset($validate->failed()['amount'])){
                    return $this->fail("amount is required",400);
                }
                return $this->fail("validation error",400);
            }

            $transfer_amount= $request->amount;

            $main_wallet=Auth::user()->main_wallet;
            if($main_wallet->balance<$transfer_amount){
                return $this->fail("you don't have enough balance in main wallet",400);
            }

            $game_wallet=Auth::user()->game_wallet;

            $new_transfer=Transfer::create([
                'user_id'=>Auth::id(),
                'to_main'=>false,
                'to_game'=>true,
                'amount'=>$transfer_amount,
            ]);
            
            if($new_transfer){
                $main_wallet->decrement('balance',$transfer_amount);
                $game_wallet->increment('balance',$transfer_amount);
            }

            DB::commit();

            $data=[
                "transfer"=>$new_transfer,
            ];
            return $this->success('transfer to game wallet successful',$data);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }



    public function transfer_to_main_wallet(Request $request)
    {   
        
        if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                 'message' => 'You are banned from accessing this feature.',
            ],403);
        }
        
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(),
            [
                'amount'=>'required',
            ]);
            
            if($validate->fails()){
                if(isset($validate->failed()['amount'])){
                    return $this->fail("amount is required",400);
                }
                return $this->fail("validation error",400);
            }

            $transfer_amount= $request->amount;

            $game_wallet=Auth::user()->game_wallet;
            if($game_wallet->balance<$transfer_amount){
                return $this->fail("you don't have enough balance in game wallet",400);
            }

            $main_wallet=Auth::user()->main_wallet;

            $new_transfer=Transfer::create([
                'user_id'=>Auth::id(),
                'to_main'=>true,
                'to_game'=>false,
                'amount'=>$transfer_amount,
            ]);
            
            if($new_transfer){
                $game_wallet->decrement('balance',$transfer_amount);
                $main_wallet->increment('balance',$transfer_amount);
            }

            DB::commit();

            $data=[
                "transfer"=>$new_transfer,
            ];

            return $this->success('transfer to main wallet successful',$data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }


    public function transfer_history()
    {
        
        if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'You are banned from accessing this feature.',
            ],403);
        }
        
        try {
            $transfer_histories=Transfer::where('user_id',Auth::id())->paginate(15);
            return $this->success('get transfer histories successful',$transfer_histories);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

   public function getWalletHistroy()
{
    if (auth()->user()->isBanned()) {
        return response()->json([
            'success' => false,
            'message' => 'သင့်အား အသုံးပြုခွင့်ရပ်ဆိုင်းထားပါသည်',
        ], 403);
    }

    try {
        // Retrieve authenticated user
        $user = Auth::user();

        // Paginate the mobile_topups query
        $mobile_topups = TopUp::where('user_id', $user->id)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        // Paginate the mobile_cash_outs query
        $mobile_cash_outs = CashOut::where('user_id', $user->id)
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $history = [];

        foreach ($mobile_topups as &$mobile_topup) {
            $mobile_topup['type'] = 1;
            $history[] = $mobile_topup;
        }

        foreach ($mobile_cash_outs as &$mobile_cash_out) {
            $mobile_cash_out['type'] = 2;
            $history[] = $mobile_cash_out;
        }

        usort($history, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Calculate the combined total count
        $combinedTotal = $mobile_topups->total() + $mobile_cash_outs->total();

        $response = [
            'currentPage' => $mobile_cash_outs->currentPage(),
            'data' => $history,
            'from' => $mobile_cash_outs->firstItem(),
            'lastPage' => $mobile_cash_outs->lastPage(),
            'perPage' => $mobile_cash_outs->perPage(),
            'to' => $mobile_cash_outs->lastItem(),
            'total' => $combinedTotal,
        ];

        return $this->success("get wallet history successful", $response);
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
    }
}
}