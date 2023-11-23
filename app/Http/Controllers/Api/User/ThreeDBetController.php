<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\ThreDNumber;
use App\Models\ThreDNumberBet;
use App\Models\ThreeDLedger;
use App\Models\ThreeDPayBack;
use App\Models\ThreeDResult;
use App\Models\ThreeDVoucher;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Cog\Laravel\Ban\Traits\Bannable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ThreeDBetController extends Controller
{
     use ResponseTrait;
     use Bannable;
     
    public function get_tdlottery(Request $reques)
    {
        if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                   'message' => 'သင့်အား အသုံးပြုခွင့်ရပ်ဆိုင်းထားပါသည်',
            ],403);
        }
        try {
        $today = Carbon::today();
        $current_time = Carbon::now('Asia/Yangon');
        
        $last_open_ledger = ThreeDLedger::where('open_date', '<=', Carbon::now())
                                         ->orderBy('open_date', 'desc')
                                         ->first();
        $currentDate = Carbon::now()->toDateString();
        
       
        
        // $target_date = Carbon::createFromFormat('Y-m-d', $last_open_ledger->target_date.' '.$last_open_ledger->limit_date, 'Asia/Yangon');
        if(!$last_open_ledger){
                return $this->fail('ထီအရောင်းမရှိပါ',404);
        }  
        
        if ($today->gt($last_open_ledger->limit_date)) {
          return $this->fail('ထီအရောင်းမရှိပါ', 400);
        }

        if($last_open_ledger->limit_date == $today){
            if ($current_time->gt($last_open_ledger->limit_time)) {
                  return $this->fail('ထီထိုးအရောင်းပိတ်ပါပြီ',404);
            }
        }

        $data = [
            'id' => $last_open_ledger->id,
            'target_date' => $last_open_ledger->target_date,
            'pay_back_multiply' => $last_open_ledger->pay_back_multiply,
            'open_at' => $last_open_ledger->open_at,
            'close_at' => $last_open_ledger->target_date,
            'limit_at' => $last_open_ledger->limit_date,
            'limit_time' => $last_open_ledger->limit_time,
            'created_at' => $last_open_ledger->created_at,
            'updated_at' => $last_open_ledger->updated_at,
        ];

        return $this->success('Get 3D lottery successful', $data);

    } catch (\Throwable $th) {
        return $this->fail($th->getMessage() ? $th->getMessage() : "Server error", 500);
    }

}

    public function get_numbers($ledger_id)
    {
        if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                  'message' => 'သင့်အား အသုံးပြုခွင့်ရပ်ဆိုင်းထားပါသည်',
            ],403);
        }
        try {   
            $numbers = ThreDNumber::where('three_d_ledger_id', $ledger_id)->get();
           
            foreach($numbers as $number){
            // print($number);
            if($number->current_amount>0){
                $number->percentage = ($number->current_amount/$number->limit_amount) * 100;
            }else{
                $number->percentage = 0;
            }
            $number->available = $number->limit_amount - $number -> current_amount>0;
            $number->life_amount=$number->limit_amount - $number -> current_amount;
            unset($number->created_at);
            unset($number->updated_at);
            
        }
        
        $data=[
            "numbers"=>$numbers->toArray()
        ];
        return $this->success('get numbers successful',$data);       
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
    }
    }
     
public function bet($ledger_id, Request $request)
{
    if (auth()->user()->isBanned()) {
        return response()->json([
            'success' => false,
            'message' => 'You are banned from accessing this feature.',
        ], 403);
    }

    DB::beginTransaction();

    try {
        $validate = Validator::make($request->all(), [
            'number_ids' => 'required|array',
            'amounts' => 'required|array',
        ]);

        // Check if numbers and amount array count are the same or not
        if (count($request->number_ids) != count($request->amounts)) {
            return $this->fail("All numbers should have a bet amount", 400);
        }

        if ($validate->fails()) {
            if (isset($validate->failed()['numbers'])) {
                return $this->fail("Numbers are required", 400);
            }
            if (isset($validate->failed()['amounts'])) {
                return $this->fail("Amounts are required", 400);
            }
            return $this->fail("Validation error", 400);
        }

        // Check if the user has enough balance
        $balance = Auth::user()->main_wallet->balance;
        $total_bet_amount = array_sum($request->amounts);

        if ($balance < $total_bet_amount) {
            return $this->fail("You don't have enough balance in the main wallet", 400);
        }

        // Check if the section exists
        $ledger = ThreeDLedger::find($ledger_id);
        if (!$ledger) {
            return $this->fail("3d section not found", 404);
        }

        $current_time = Carbon::now()->setTimezone('Asia/Yangon');
        $today = Carbon::today();
        if ($ledger->limit_date == $today) {
             if($ledger->limit_date == $today){
            if ($current_time->gt($ledger->limit_time)) {
                  return $this->fail('ထီထိုးအရောင်းပိတ်ပါပြီ',404);
            }
        }
            // try {
            //     $limit_time = Carbon::createFromFormat('H:i:s', $ledger->limit_time)->setTimezone('Asia/Yangon');
            //     if ($current_time->gte($limit_time)) {
            //         return $this->fail("This section is not available", 400);
            //     }
            // } catch (\Exception $e) {
            //     return $this->fail("Invalid limit time format", 400);
            // }
        }

        $today_ledger = ThreeDLedger::where('limit_date', '=', $today)->first();

        if (!$today_ledger) {
            $today_ledger = ThreeDLedger::where('limit_date', '>', $today)->first();
            if (!$today_ledger) {
                return $this->fail("Invalid section id. No section found", 400);
            }
        }

        if ($ledger_id != $today_ledger->id) {
            $limit_time = Carbon::createFromFormat('H:i:s', $today_ledger->limit_time)->setTimezone('Asia/Yangon');
            $currentTime = Carbon::now();

            if ($limit_time->greaterThan($currentTime)) {
                return $this->fail("Invalid section id. Exceeded limit time", 400);
            }
        }

        $numbers = ThreDNumber::where('three_d_ledger_id', $ledger_id)->get();
        $new_voucher = new ThreeDVoucher();
        $new_voucher->vouchers_code = "ZV" . date('Ymd') . $ledger_id . "E" . Auth::id();
        $new_voucher->user_id = Auth::id();
        $new_voucher->three_d_ledger_id = $ledger_id;
        $new_voucher->total_amount = $total_bet_amount;
        $new_voucher->save();

        // Retrieve the auto-generated voucher ID
        $voucher_id = $new_voucher->getKey();

        // Update the voucher code with the auto-generated ID
        $new_voucher->vouchers_code .= "V" . $voucher_id;

        $new_voucher->save();

        $number_ids = $request->number_ids;

        foreach ($number_ids as $index => $number_id) {
            $number = $numbers->find($number_id);
            if (!$number) {
                return $this->fail("Contain an invalid number id", 400);
            }
            $bet_amount = $request->amounts[$index];
            if ($number->current_amount + $bet_amount > $number->limit_amount) {
                return $this->fail("Contain a limited bet amount of number", 400);
            }

            ThreDNumberBet::create([
                'thre_d_numbers_id' => $number_id,
                'voucher_id' => $new_voucher->id,
                'user_id' => Auth::id(),
                'three_d_ledger_id' => $ledger_id,
                'number' => $number->number,
                'amount' => $bet_amount,
            ]);
            $number->increment('current_amount', $bet_amount);
        }

        Auth::user()->main_wallet->decrement('balance', $total_bet_amount);

        DB::commit();

        $new_voucher->three_d_bets = $new_voucher->three_d_bets();

        $data = [
            'voucher' => $new_voucher,
        ];

        return $this->success('Bet your numbers successful', $data);
    } catch (\Throwable $th) {
        DB::rollBack();
        return $this->fail($th->getMessage() ? $th->getMessage() : "Server error", 500);
    }
}



 public function get_bet_histories()
{
    if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                 'message' => 'You are banned from accessing this feature.',
            ],403);
            }
    try {
        // $vouchers = Voucher::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(20);
        $vouchers = ThreeDVoucher::where('user_id', Auth::id())->orderBy('id', 'DESC')->paginate(20);
        $currentDate = Carbon::now()->toDateString();
        foreach ($vouchers as $voucher) {
            $ledger_id=$voucher->three_d_ledger_id;
            $my_ledger = ThreeDLedger::where('id', $ledger_id)->first();
           

            $currentDate = Carbon::now('Asia/Yangon');
            $parsedDate = Carbon::parse($my_ledger->target_date);

            $voucher->target_date = $parsedDate;
            $voucher->target_time = $my_ledger->open_at;

            
            
           if ($voucher->target_date > $currentDate) {
    $voucher->expired = false; 
// } else if ($parsedDate->isSameDay($currentDate) && $currentDate->format('H:i') < '3:00') {
//     $voucher->expired = false;
} else {
    $voucher->expired = true;
}

            unset($voucher->user_id);
            unset($voucher->three_d_ledger_id);
        }

        return $this->success('Successfully retrieved bet history', $vouchers);
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage() ?: 'Server error', 500);
    }
}


// get_bet_history_detail


    public function get_bet_history_detail($voucher_id)
{
    if (auth()->user()->isBanned()) {
        return response()->json([
            'success' => false,
            'message' => 'You are banned from accessing this feature.',
        ], 403);
    }

    try {
        $voucher = ThreeDVoucher::find($voucher_id);
        if (!$voucher) {
            return $this->fail('Voucher not found', 404);
        }

        if ($voucher->user_id != Auth::id()) {
            return $this->fail('Voucher not found', 404);
        }

        $current_time = Carbon::now();
        $current_time->setTimezone('Asia/Yangon');
        $ledger_id = $voucher->three_d_ledger_id;

        // Get the numbers entered by the user from ThreDNumberBet
       
        $threDNumberBets = ThreDNumberBet::where('voucher_id', $voucher->id)->get();
        if ($threDNumberBets->isEmpty()) {
            return $this->fail('Numbers not found', 404);
        }
         $threeDLedger = ThreeDLedger::find($ledger_id);
        if (!$threeDLedger) {
            return $this->fail('ThreeDLedger not found', 404);
        }
         $pay_back_multiply = $threeDLedger->pay_back_multiply;

        // Build the example bet data
        $bets = [];
        foreach ($threDNumberBets as $threDNumberBet) {
            $betData = [
                'id' => $threDNumberBet->id,
                'number' => $threDNumberBet->number,
                'amount' => $threDNumberBet->amount,
                'created_at' => $threDNumberBet->created_at->toISOString(),
                'updated_at' => $threDNumberBet->updated_at->toISOString(),
                'pay_back_multiply' => $pay_back_multiply,
            ];

            $bets[] = $betData;
        }

        // Get the pay_back_multiply from ThreeDLedger
        $threeDLedger = ThreeDLedger::find($ledger_id);
        if (!$threeDLedger) {
            return $this->fail('ThreeDLedger not found', 404);
        }
        $pay_back_multiply = $threeDLedger->pay_back_multiply; // Assuming the column name is 'pay_back_multiply'
        $targetDate = $threeDLedger->target_date; // Assuming the column name is 'target_date'
        $expired = $current_time->greaterThan($targetDate);

        // Build the example response
        $data = [
            'id' => $voucher->id,
            'total_amount' => $voucher->total_amount,
            'created_at' => $voucher->created_at->toISOString(),
            'updated_at' => $voucher->updated_at->toISOString(),
            'expired' => $expired,
            'bets' => $bets,
        ];

        // return response()->json($response);
        return $this->success('Get 3D history successful', $data);
    } catch (\Throwable $th) {
        return $this->fail($th->getMessage() ? $th->getMessage() : 'Server error', 500);
    }
}







}