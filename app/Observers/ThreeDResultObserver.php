<?php

namespace App\Observers;

use App\Models\ThreDNumberBet;
use App\Models\ThreeDResult;
use App\Models\ThreeDWinner;

class ThreeDResultObserver
{
    /**
     * Handle the ThreeDResult "created" event.
     *
     * @param  \App\Models\ThreeDResult  $threeDResult
     * @return void
     */
    public function created(ThreeDResult $threeDResult)
    
    
    {
        $bets=ThreDNumberBet::where('thre_d_numbers_id',$threeDResult->number_id)->get();
          foreach($bets as $bet){
            ThreeDWinner::create([
                'three_d_ledger_id'=>$threeDResult->three_d_ledger_id,
                'result_id'=>$threeDResult->id,
                'user_id'=>$bet->user_id,
                'bet_id'=>$bet->id,
                'type' => 0,
            ]);
        }

    $betoriginal = ThreDNumberBet::where('thre_d_numbers_id', $threeDResult->number_id)->first();
    $betoriginalNumber = $threeDResult->number;

$numbersToCheck = [
 $betoriginalNumber[1].$betoriginalNumber[0].$betoriginalNumber[2],
 $betoriginalNumber[1].$betoriginalNumber[2].$betoriginalNumber[0],
  $betoriginalNumber[2].$betoriginalNumber[0].$betoriginalNumber[1],
    $betoriginalNumber[2].$betoriginalNumber[1].$betoriginalNumber[0],
   $betoriginalNumber[0].$betoriginalNumber[2].$betoriginalNumber[1],
];

foreach ($numbersToCheck as $number) {
    if($betoriginalNumber != $number){
    $rbet = ThreDNumberBet:: where([
    ['number', '=', $number],
    ['three_d_ledger_id', '=', $threeDResult->three_d_ledger_id]
])->get();
    foreach($rbet as $ubet){
            ThreeDWinner::create([
                'three_d_ledger_id'=>$threeDResult->three_d_ledger_id,
                'result_id'=>$threeDResult->id,
                'user_id'=>$ubet->user_id,
                'bet_id'=>$ubet->id,
                'type' => 1,
        ]); 
    }
}
}
    }

        //reverse the number
        

        

        // get r bets for this number
        // create winners for each bet
    

 

    /**
     * Handle the ThreeDResult "updated" event.
     *
     * @param  \App\Models\ThreeDResult  $threeDResult
     * @return void
     */
    public function updated(ThreeDResult $threeDResult)
    {
        //
    }

    /**
     * Handle the ThreeDResult "deleted" event.
     *
     * @param  \App\Models\ThreeDResult  $threeDResult
     * @return void
     */
    public function deleted(ThreeDResult $threeDResult)
    {
        //
    }

    /**
     * Handle the ThreeDResult "restored" event.
     *
     * @param  \App\Models\ThreeDResult  $threeDResult
     * @return void
     */
    public function restored(ThreeDResult $threeDResult)
    {
        //
    }

    /**
     * Handle the ThreeDResult "force deleted" event.
     *
     * @param  \App\Models\ThreeDResult  $threeDResult
     * @return void
     */
    public function forceDeleted(ThreeDResult $threeDResult)
    {
        //
    }
}