<?php

namespace App\Observers;

use App\Models\ThreDNumber;
use App\Models\ThreeDLedger;

class ThreeDLedgerObserver
{
    /**
     * Handle the ThreeDLedger "created" event.
     *
     * @param  \App\Models\ThreeDLedger  $threeDLedger
     * @return void
     */
    public function created(ThreeDLedger $threeDLedger)
    {
        $numbers = collect(range(0, 999))->map(function($number) {
        return str_pad($number, 3, '0', STR_PAD_LEFT);
        });

        foreach($numbers as $number){
            $new_number=new ThreDNumber();
            $new_number->three_d_ledger_id=$threeDLedger->id;
            $new_number->number=$number;
            $new_number->limit_amount=20000;
            $new_number->save();
        }
         
    }

    /**
     * Handle the ThreeDLedger "updated" event.
     *
     * @param  \App\Models\ThreeDLedger  $threeDLedger
     * @return void
     */
    public function updated(ThreeDLedger $threeDLedger)
    {
        //
    }

    /**
     * Handle the ThreeDLedger "deleted" event.
     *
     * @param  \App\Models\ThreeDLedger  $threeDLedger
     * @return void
     */
    public function deleted(ThreeDLedger $threeDLedger)
    {
        //
    }

    /**
     * Handle the ThreeDLedger "restored" event.
     *
     * @param  \App\Models\ThreeDLedger  $threeDLedger
     * @return void
     */
    public function restored(ThreeDLedger $threeDLedger)
    {
        //
    }

    /**
     * Handle the ThreeDLedger "force deleted" event.
     *
     * @param  \App\Models\ThreeDLedger  $threeDLedger
     * @return void
     */
    public function forceDeleted(ThreeDLedger $threeDLedger)
    {
        //
    }
}