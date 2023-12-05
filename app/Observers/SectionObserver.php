<?php

namespace App\Observers;

use App\Models\MmAfterNoonNumber;
use App\Models\MmEveningNumber;
use App\Models\MmMorningNumber;
use App\Models\MmNoonNumber;
use App\Models\Section;
use App\Models\UkAfterNoonNumber;
use App\Models\UkEveningNumber;

class SectionObserver
{
    /**
     * Handle the Section "created" event.
     *
     * @param  \App\Models\Section  $section
     * @return void
     */
    public function created(Section $section)
    {
        $numbers = collect(range(00, 99))->map(function($number) {
            return str_pad($number, 2, '0', STR_PAD_LEFT);
        });
    
        foreach($numbers as $number){
            switch ($section->section_index) {
                case 0:
                    $new_number=new MmMorningNumber();
                    $new_number->ledger_id=$section->ledger_id;
                    $new_number->section_id=$section->id;
                    $new_number->number=$number;
                    $new_number->limit_amount=100000;
                    break;
                case 1:
                    $new_number=new MmNoonNumber();
                    $new_number->ledger_id=$section->ledger_id;
                    $new_number->section_id=$section->id;
                    $new_number->number=$number;
                    $new_number->limit_amount=50000;
                    break;
                case 2:
                    $new_number=new MmAfterNoonNumber();
                    $new_number->ledger_id=$section->ledger_id;
                    $new_number->section_id=$section->id;
                    $new_number->number=$number;
                    $new_number->limit_amount=100000;
                    break;
                case 3:
                    $new_number=new MmEveningNumber();
                    $new_number->ledger_id=$section->ledger_id;
                    $new_number->section_id=$section->id;
                    $new_number->number=$number;
                    $new_number->limit_amount=50000;
                    break;
            }
            $new_number->save();
        }
    }

    /**
     * Handle the Section "updated" event.
     *
     * @param  \App\Models\Section  $section
     * @return void
     */
    public function updated(Section $section)
    {
        //
    }

    /**
     * Handle the Section "deleted" event.
     *
     * @param  \App\Models\Section  $section
     * @return void
     */
    public function deleted(Section $section)
    {
        //
    }

    /**
     * Handle the Section "restored" event.
     *
     * @param  \App\Models\Section  $section
     * @return void
     */
    public function restored(Section $section)
    {
        //
    }

    /**
     * Handle the Section "force deleted" event.
     *
     * @param  \App\Models\Section  $section
     * @return void
     */
    public function forceDeleted(Section $section)
    {
        //
    }
}