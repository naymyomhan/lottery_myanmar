<?php

namespace App\Observers;

use App\Models\MmAfterNoonBet;
use App\Models\MmAfterNoonNumber;
use App\Models\MmEveningBet;
use App\Models\MmEveningNumber;
use App\Models\MmMorningBet;
use App\Models\MmMorningNumber;
use App\Models\MmNoonBet;
use App\Models\MmNoonNumber;
use App\Models\Result;
use App\Models\TopUp;
use App\Models\Transfer;
use App\Models\UkAfterNoonBet;
use App\Models\UkAfterNoonNumber;
use App\Models\UkEveningBet;
use App\Models\UkEveningNumber;
use App\Models\Winner;
use Illuminate\Support\Facades\Auth;

class ResultObserver
{
    /**
     * Handle the Result "created" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function created(Result $result)
    {   
        switch ($result->section->section_index) {
            case 0:
                $bets=MmMorningBet::where('mm_morning_number_id',$result->number_id)->get();
                break;
            case 1:
                $bets=MmNoonBet::where('mm_noon_number_id',$result->number_id)->get();
                break;
            case 2:
                $bets=MmAfterNoonBet::where('mm_after_noon_number_id',$result->number_id)->get();
                break;
            case 3:
                $bets=MmEveningBet::where('mm_evening_number_id',$result->number_id)->get();
                break;
        }

        foreach($bets as $bet){
            Winner::create([
                'ledger_id'=>$result->ledger_id,
                'section_id'=>$result->section_id,
                'result_id'=>$result->id,
                'user_id'=>$bet->user_id,
                'bet_id'=>$bet->id,
            ]);
        }
        
    }

    /**
     * Handle the Result "updated" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function updated(Result $result)
    {
        //
    }

    /**
     * Handle the Result "deleted" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function deleted(Result $result)
    {
        //
    }

    /**
     * Handle the Result "restored" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function restored(Result $result)
    {
        //
    }

    /**
     * Handle the Result "force deleted" event.
     *
     * @param  \App\Models\Result  $result
     * @return void
     */
    public function forceDeleted(Result $result)
    {
        //
    }
}