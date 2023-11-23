<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\MmAfterNoonBet;
use App\Models\MmEveningBet;
use App\Models\MmMorningBet;
use App\Models\MmNoonBet;
use App\Models\Section;
use App\Models\ThreDNumberBet;
use App\Models\ThreeDLedger;
use App\Models\ThreeDWinner;
use App\Models\UkAfterNoonBet;
use App\Models\UkEveningBet;
use App\Models\Winner;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
class WinnerController extends Controller
{
    use ResponseTrait;
   public function get_winners()
{
    // $currentMonth = Carbon::now()->format('Y-m');

    // $winners = Winner::where('created_at', 'LIKE', $currentMonth.'%')->get();

    // if ($winners->isEmpty()) {
    //     return $this->fail('No winners found for the current month', 400);
    // }
    
    // Get the current date
    $currentDate = Carbon::now();

// Calculate the start of the current week (Monday)
$startOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY);

// Calculate the end of the current week (Sunday)
$endOfWeek = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);


// echo "Start of Week: " . $startOfWeek . ", End of Week: " . $endOfWeek;

// Retrieve winners for the current week
$winners = Winner::where('created_at', '>=', $startOfWeek)
                 ->where('created_at', '<=', $endOfWeek)
                 ->get();

if ($winners->isEmpty()) {
    return $this->fail('No winners found for the current week (Mon to Sun)', 400);
}

    // Handle the winners data here...

    $formattedWinners = [];

    foreach ($winners as $winner) {
        // Fetch the corresponding section
        $section = Section::find($winner->section_id);
        if (!$section) {
            continue; // Skip to the next winner if section not found
        }

        // Winner name
        $name = $winner->user->name;
        
         $phone = $winner->user->phone;

        $lastThreeDigits = substr($phone, -3);
        $phoneDigits = preg_replace('/^\+?(0|959)?/', '', $phone);


// Replacing the remaining digits with 'x'
$maskedPhone = '09' . str_repeat('x', strlen($phoneDigits) - 3) . $lastThreeDigits;


        // Bet amount
        switch ($section->section_index) {
            case 0:
                $bet = MmMorningBet::find($winner->bet_id);
                break;
            case 1:
                $bet = MmNoonBet::find($winner->bet_id);
                break;
            case 2:
                $bet = MmAfterNoonBet::find($winner->bet_id);
                break;
            case 3:
                $bet = MmEveningBet::find($winner->bet_id);
                break;
        }

        // Bet amount
        $betAmount = $bet->amount;
        // $totalBetAmount = $bet->voucher->total_amount;

        // Win amount (computed value)
        $winAmount = $bet->amount * $section->pay_back_multiply;

        // Create the formatted winner data
        $formattedWinner = [
            'id' => $winner->id,
            'name' => $name,
            'image'=>env('DO_STORAGE_URL').$winner->user->profile_picture_location,
            'phone' => $maskedPhone,
            'bet_amount' => $betAmount,
            'total_bet_amount' => 0,
            'win_amount' => $winAmount,
        ];

        // Add the formatted winner to the array
        $formattedWinners[] = $formattedWinner;
    }

    // Sort winners by win_amount in descending order
    usort($formattedWinners, function ($a, $b) {
        return $b['win_amount'] - $a['win_amount'];
    });

    $topWinners = array_slice($formattedWinners, 0, 100);

    return $this->success('Get winners successful', $topWinners);
}

public function get_tdwinners(){
     $currentMonth = Carbon::now()->format('Y-m');
     $winners = ThreeDWinner::where('created_at', 'LIKE', $currentMonth.'%')->get();
      if ($winners->isEmpty()) {
        return $this->fail('No winners found for the current month', 400);
    }
    $formattedWinners = [];
      foreach ($winners as $winner) {
        // Fetch the corresponding 3d ledger section
        $tdledger = ThreeDLedger::find($winner->three_d_ledger_id);

        if (!$tdledger) {
            continue; // Skip to the next winner if section not found
        }

        // Winner name
        $name = $winner->user->name;

         $phone = $winner->user->phone;

        $lastThreeDigits = substr($phone, -3);
        $phoneDigits = preg_replace('/^\+?(0|959)?/', '', $phone);


// Replacing the remaining digits with 'x'
$maskedPhone = '09' . str_repeat('x', strlen($phoneDigits) - 3) . $lastThreeDigits;

        $bet=  ThreDNumberBet::find($winner->bet_id);

        // Bet amount
        $betAmount = $bet->amount;
        $totalBetAmount = $bet->voucher->total_amount;

        // Win amount (computed value)
        if($winner->type==1){
            $winAmount = $bet->amount * $tdledger->r_pay_back_multiply;
        }else{
            $winAmount = $bet->amount * $tdledger->pay_back_multiply;
        }

        // Create the formatted winner data
        $formattedWinner = [
            'id' => $winner->id,
            'name' => $name,
            'image'=>env('DO_STORAGE_URL').$winner->user->profile_picture_location,
            'phone' => $maskedPhone,
            'bet_amount' => $betAmount,
            'total_bet_amount' => $totalBetAmount,
            'type'=>$winner->type,
            'win_amount' => $winAmount,
        ];

        // Add the formatted winner to the array
        $formattedWinners[] = $formattedWinner;
    }

    // Sort winners by win_amount in descending order
    usort($formattedWinners, function ($a, $b) {
        return $b['win_amount'] - $a['win_amount'];
    });

    return $this->success('Get winners successful', $formattedWinners);
    
}


    public function get_winners_by_section($section_id)
    {
        $section=Section::find($section_id);
        if(!$section){
            return $this->fail('some thing went wrong',400);
        }

        $winners=$section->winers;
        
        foreach($winners as $winner){

            //winer name
            $winner->name=$winner->user->name;
            $winner->image= env('DO_STORAGE_URL').$winner->user->profile_picture_location;

            //bet amount
            switch ($section->section_index) {
                case 0:
                    $bet=MmMorningBet::find($winner->bet_id);
                    break;
                case 1:
                    $bet=MmNoonBet::find($winner->bet_id);
                    break;
                case 2:
                    $bet=MmAfterNoonBet::find($winner->bet_id);
                    break;
                case 3:
                    $bet=MmEveningBet::find($winner->bet_id);
                    break;
            }

            //bet amount
            $winner->bet_amount=$bet->amount;
            $winner->total_bet_amount=$bet->voucher->total_amount;

            //win amount
            $winner->win_amount=$bet->amount * $section->pay_back_multiply;

            //remove 
            unset($winner->user);
            unset($winner->ledger_id);
            unset($winner->section_id);
            unset($winner->result_id);
            unset($winner->user_id);
            unset($winner->bet_id);
            unset($winner->created_at);
            unset($winner->updated_at);
        }

        return $this->success('get winner successful',$winners);
    }
}