<?php

namespace App\Observers;

use App\Models\Ledger;
use App\Models\Section;

class LedgerObserver
{
    /**
     * Handle the Ledger "created" event.
     *
     * @param  \App\Models\Ledger  $ledger
     * @return void
     */
    public function created(Ledger $ledger)
    {
        $sections=[
            
           
            [
                'section_type_id'=>0,
                'section_type_name'=> 'Myanmar 2D',
                'limit_at'=>"15:55:00",
                'close_at'=>"16:30:00",
                'pay_back_multiply'=>90,
                'section_index'=>3,
            ],
            [
                'section_type_id'=>0,
                'section_type_name'=> 'Myanmar 2D',
                'limit_at'=>"14:40:00",
                'close_at'=>"14:45:00",
                'pay_back_multiply'=>90,
                'section_index'=>2,
            ], [
                'section_type_id'=>0,
                'section_type_name'=> 'Myanmar 2D',
                'limit_at'=>"11:55:00",
                'close_at'=>"12:01:00",
                'pay_back_multiply'=>80,
                'section_index'=>1,
            ],[
                'section_type_id'=>0,
                'section_type_name'=> 'Myanmar 2D',
                'limit_at'=>"10:40:00",
                'close_at'=>"10:45:00",
                'pay_back_multiply'=>90,
                'section_index'=>0,
            ]
        ];

        foreach($sections as $section){
            Section::create([
                'ledger_id'=>$ledger->id,
                'section_type_id'=>$section['section_type_id'],
                'section_type_name'=>$section['section_type_name'],
                'limit_at'=>$section['limit_at'],
                'close_at'=>$section['close_at'],
                'pay_back_multiply'=>$section['pay_back_multiply'],
                'section_index'=>$section['section_index'],
            ]);
        }
}

    /**
     * Handle the Ledger "updated" event.
     *
     * @param  \App\Models\Ledger  $ledger
     * @return void
     */
    public function updated(Ledger $ledger)
    {
        //
    }

    /**
     * Handle the Ledger "deleted" event.
     *
     * @param  \App\Models\Ledger  $ledger
     * @return void
     */
    public function deleted(Ledger $ledger)
    {
        //
    }

    /**
     * Handle the Ledger "restored" event.
     *
     * @param  \App\Models\Ledger  $ledger
     * @return void
     */
    public function restored(Ledger $ledger)
    {
        //
    }

    /**
     * Handle the Ledger "force deleted" event.
     *
     * @param  \App\Models\Ledger  $ledger
     * @return void
     */
    public function forceDeleted(Ledger $ledger)
    {
        //
    }
}