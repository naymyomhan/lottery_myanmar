<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\MmAfterNoonBet;
use App\Models\MmEveningBet;
use App\Models\MmMorningBet;
use App\Models\MmNoonBet;
use App\Models\Section;
use App\Models\Voucher;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Cog\Laravel\Ban\Traits\Bannable;

use function PHPUnit\Framework\isFalse;

class BetController extends Controller
{   
    use ResponseTrait;
    use Bannable;

    public function bet($section_id,Request $request)
    {    
        
        if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                 'message' => 'သင့်အား အသုံးပြုခွင့်ရပ်ဆိုင်းထားပါသည်',
            ],403);
        }
            
        DB::beginTransaction();
        try {
            $validate = Validator::make($request->all(),
            [
                'number_ids' => 'required|array',
                'amounts'=>'required|array',
            ]);
            
            //check if numbers and amount array count are same or not
            if(count($request->number_ids)!=count($request->amounts)){
                return $this->fail("all number should have bet amount",400);
            }

            if($validate->fails()){
                if(isset($validate->failed()['numbers'])){
                    return $this->fail("numbers are required",400);
                }
                if(isset($validate->failed()['amounts'])){
                    return $this->fail("amounts are is required",400);
                }
                return $this->fail("validation error",400);
            }

            //check if user have enough balance
            $balance = Auth::user()->main_wallet->balance;
            $total_bet_amount=array_sum($request->amounts);

            if($balance<$total_bet_amount){
                return $this->fail("သင့်တွင်လက်ကျန်ငွေအလုံလောက်မရှိပါ",400);
            }
            
            //check if section is exists
            $section=Section::find($section_id);
            if(!$section){
                return $this->fail("section not found",404);
            }
            
            //check if section is belong to today ledger or not
            $ledger_id=$section->ledger_id;
            $today = Carbon::today();
            $today->setTimezone('Asia/Yangon');
            
            $today_ledger = Ledger::where('id', $ledger_id)->first();
            $isFeature = true;

            // Assuming $today_ledger->start_date is stored as a date string in the database
            $start_date = Carbon::parse($today_ledger->target_date);
            $start_date->setTimezone('Asia/Yangon');

            if ($start_date->isSameDay($today)) {
             $isFeature = false;
            }
            if($isFeature == false){
                 if(!$today_ledger){
                return $this->fail("invalid section id",400);
            }
            if($ledger_id!=$today_ledger->id){
                return $this->fail("invalid section id",400);
            }
            
            $current_time = Carbon::now();
            $current_time->setTimezone('Asia/Yangon');
            $limit_time = Carbon::createFromFormat('H:i:s', $section->limit_at);
            $limit_time->setTimezone('Asia/Yangon');
            if ($current_time->gt($limit_time)) {
                return $this->fail("this section is no available",400);
            }
            }
          
            $numbers=$section->mm_morning_numbers;

            switch ($section->section_index) {
                case 0:
                    $numbers=$section->mm_morning_numbers;
                    break;
                case 1:
                    $numbers=$section->mm_noon_numbers;
                    break;
                case 2:
                    $numbers=$section->mm_after_noon_numbers;
                    break;
                case 3:
                    $numbers=$section->mm_evening_numbers;
                    break;
            }

            $new_voucher = new Voucher();
$new_voucher->vouchers_code = "ZV" . date('Ymd'). $section->id . "S" . Auth::id();
$new_voucher->user_id = Auth::id();
$new_voucher->section_id = $section->id;
$new_voucher->total_amount = $total_bet_amount;
$new_voucher->save();


// Retrieve the auto-generated voucher ID
$voucher_id = $new_voucher->getKey();

// Update the voucher code with the auto-generated ID
$new_voucher->vouchers_code .="V" . $voucher_id;

$new_voucher->save();

            $number_ids=$request->number_ids;
            foreach($number_ids as $index=>$number_id){

                //check if number is belong to section
                $number=$numbers->find($number_id);
                if(!$number){
                    return $this->fail("မှားယွင်းနေပါသည်",400);
                }
    
                //check if limit amount of bet is full
                $bet_amount=$request->amounts[$index];
                if($number->current_amount+$bet_amount > $number->limit_amount){
                    return $this->fail("limit ကျော်နေပါသည်",400);
                }

                //switch section
                switch ($section->section_index) {
                    case 0:
                        MmMorningBet::create([
                            'mm_morning_number_id'=>$number_id,
                            'voucher_id'=>$new_voucher->id,
                            'user_id'=>Auth::id(),
                            'section_id'=>$section->id,
                            'number'=>$number->number,
                            'amount'=>$bet_amount,
                        ]);
                        break;
                    case 1:
                        MmNoonBet::create([
                            'mm_noon_number_id'=>$number_id,
                            'voucher_id'=>$new_voucher->id,
                            'user_id'=>Auth::id(),
                            'section_id'=>$section->id,
                            'number'=>$number->number,
                            'amount'=>$bet_amount,
                        ]);
                        break;
                    case 2:
                        MmAfterNoonBet::create([
                            'mm_after_noon_number_id'=>$number_id,
                            'voucher_id'=>$new_voucher->id,
                            'user_id'=>Auth::id(),
                            'section_id'=>$section->id,
                            'number'=>$number->number,
                            'amount'=>$bet_amount,
                        ]);
                        break;
                    case 3:
                        MmEveningBet::create([
                            'mm_evening_number_id'=>$number_id,
                            'voucher_id'=>$new_voucher->id,
                            'user_id'=>Auth::id(),
                            'section_id'=>$section->id,
                            'number'=>$number->number,
                            'amount'=>$bet_amount,
                        ]);
                        break;
                }
                 $number->increment('current_amount',$bet_amount);
                 
            }
            Auth::user()->main_wallet->decrement('balance',$total_bet_amount);
            DB::commit();
            //new voucher bets switch
            switch ($section->section_index) {
                case 0:
                    $new_voucher->bets=$new_voucher->mm_morning_bets;
                    unset($new_voucher->mm_morning_bets);
                    break;
                case 1:
                    $new_voucher->bets=$new_voucher->mm_noon_bets;
                    unset($new_voucher->mm_noon_bets);
                    break;
                case 2:
                    $new_voucher->bets=$new_voucher->mm_after_noon_bets;
                    unset($new_voucher->mm_after_noon_bets);
                    break;
                case 3:
                    $new_voucher->bets=$new_voucher->mm_evening_bets;
                    unset($new_voucher->mm_evening_bets);
                    break;
            }
            $new_voucher->section;

            $data=[
                'voucher'=>$new_voucher,
            ];

            return $this->success('bet your numbers successful',$data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function get_bet_histories()   
    {date_default_timezone_set('Asia/Yangon');
        
            try {

            if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                 'message' => 'You are banned from accessing this feature.',
            ],403);
            }
            $vouchers=Voucher::where('user_id',Auth::id())->orderBy('id','DESC')->paginate(20);

            $current_time = Carbon::now();
            $current_time->setTimezone('Asia/Yangon');

            foreach($vouchers as $voucher){
                    $carbon = Carbon::createFromFormat('H:i:s', $voucher->section->close_at);
                    $formattedTime = $carbon->format('h:i:s A');
                    $voucher->section_time=$formattedTime;
                
                    $ledger_id=$voucher->section->ledger_id;
                    $today = Carbon::today();
                    $my_ledger = Ledger::where('id', $ledger_id)->first();

                // Set the target_date of the $voucher from the fetched Ledger
                    $voucher->target_date = $my_ledger->target_date;
               
                // Get the current date
                    $currentDate = Carbon::now('Asia/Yangon')->toDateString();
                    $formattedTargetDate = date('Y-m-d', strtotime($my_ledger->target_date));
                    if ($formattedTargetDate > $currentDate) {
                    // $close_time = Carbon::createFromFormat('H:i:s', $voucher->section->close_at, 'Asia/Yangon');
                    // $current_time = Carbon::now('Asia/Yangon');
                    $voucher->expired = false;
                    } elseif ($formattedTargetDate < $currentDate) {
                    $voucher->expired = true;
                    } else {
                    $close_time = Carbon::createFromFormat('H:i:s', $voucher->section->close_at, 'Asia/Yangon');
                    $current_time = Carbon::now('Asia/Yangon');
                    if ($current_time->gt($close_time)) {
                    $voucher->expired = true;
                    } else {
                    $voucher->expired = false;
                    }
                    }
                unset($voucher->section);
                unset($voucher->user_id);
                unset($voucher->section_id);
            }

            return $this->success('get bet history successfully',$vouchers);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }

    public function get_bet_history_detail($voucher_id)
    {
        if (auth()->user()->isBanned()) {
            return response()->json([
                'success' => false,
                 'message' => 'You are banned from accessing this feature.',
            ],403);
            }
        try {
            $voucher=Voucher::find($voucher_id);
            if(!$voucher){
                return $this->fail('voucher not found',404);
            }

            if($voucher->user_id!=Auth::id()){
                return $this->fail('voucher not found',404);
            }

            $current_time = Carbon::now();
            $current_time->setTimezone('Asia/Yangon');
            $carbon = Carbon::createFromFormat('H:i:s', $voucher->section->close_at);
            $formattedTime = $carbon->format('h:i:s A');
            $voucher->section_time=$formattedTime;

            //check if voucher is expired other day
            $ledger_id=$voucher->section->ledger_id;
            $today = Carbon::today();
            $today_ledger = Ledger::whereDate('target_date', $today)->first();
            $checkstart_ledger = Ledger::where('start_date',  $today)->first();
              $voucher->expired=false;
            if($today_ledger != null){
                
                 if($ledger_id ==$today_ledger->id){
                    $close_time = Carbon::createFromFormat('H:i:s', $voucher->section->close_at);
                    $close_time->setTimezone('Asia/Yangon');
                    if ($current_time->gt($close_time)) {
                        $voucher->expired=true;
                    }else{
                        $voucher->expired=false;
                    }
                 }
                }else if ($checkstart_ledger != null){
                    if ($checkstart_ledger->id == $ledger_id) {
                     $voucher->expired=false;  
                    }else{
                     $voucher->expired=true;  
                    }
                    
                }else {
                     $voucher->expired=true;  
                } 
            

            //voucher bets switch
            switch ($voucher->section->section_index) {
                case 0:
                    $voucher->bets=$voucher->mm_morning_bets;
                    unset($voucher->mm_morning_bets);
                    break;
                case 1:
                    $voucher->bets=$voucher->mm_noon_bets;
                    unset($voucher->mm_noon_bets);
                    break;
                case 2:
                    $voucher->bets=$voucher->mm_after_noon_bets;
                    unset($voucher->mm_after_noon_bets);
                    break;
                case 3:
                    $voucher->bets=$voucher->mm_evening_bets;
                    unset($voucher->mm_evening_bets);
                    break;
            }

            foreach($voucher->bets as $bet){   
                switch ($voucher->section->section_index) {
                    case 0:
                        unset($bet->mm_morning_number_id);
                        break;
                    case 1:
                        unset($bet->mm_noon_number_id);
                        break;
                    case 2:
                        unset($bet->mm_after_noon_number_id);
                        break;
                    case 3:
                        unset($bet->mm_evening_number_id);
                        break;
                }
                $bet->pay_back_multiply=$voucher->section->pay_back_multiply;
                unset($bet->voucher_id);
                unset($bet->section_id);
                unset($bet->user_id);
            }

            unset($voucher->section);
            unset($voucher->user_id);
            unset($voucher->section_id);

            

            return $voucher;
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
        }
    }
}