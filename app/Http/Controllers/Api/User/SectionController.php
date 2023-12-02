<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\Section;
use App\Models\TwoDCloseDay;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SectionController extends Controller
{
    use ResponseTrait;

    public function get_sections()
    {
        try {
            $today = Carbon::today();
            $current_time = \Carbon\Carbon::now();

            // $currentDate = Carbon::now();

            $isFeature = false;
            $currentDate = Carbon::now('Asia/Yangon');
            $currentDayOfWeek = $currentDate->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

            // Calculate the upcoming Friday (end of the current week)
            $endOfWeek = $currentDate->copy()->endOfWeek(Carbon::FRIDAY);

            // Calculate the upcoming Sunday (start of the next week)
            $startOfNextWeek = $currentDate->copy()->next(Carbon::SUNDAY);

            $isFeatureCurrent = false;
            $holidays = ['san', 'sat'];
            $isHoliday = in_array(strtolower(Carbon::now('Asia/Yangon')->isoFormat('ddd')), $holidays);
            $isCloseDay = TwoDCloseDay::where('date', $currentDate->toDateString())->first();


            if ($isCloseDay) {
                Log::info('Step 5: Today is a close day.');
                $isFeature = true;
                $isFeatureCurrent = true;
                // Today is a close day, so get target_date + 1 day
                $ledger = Ledger::whereDate('target_date', $currentDate->copy()->addDay()->toDateString())->first();
            } else    if ($currentDayOfWeek > Carbon::FRIDAY || $currentDayOfWeek === Carbon::SUNDAY) {
                // The current day is between Friday and Sunday (including Sunday), so retrieve the Ledger data for the next week's Monday
                $nextMonday = $currentDate->copy()->next(Carbon::MONDAY);
                $nextTuesday = $currentDate->copy()->next(Carbon::TUESDAY);

                $close_day = TwoDCloseDay::where('date', $nextMonday->toDateString())->first();

                if ($close_day) {
                    $ledger = Ledger::where(function ($query) use ($nextTuesday) {
                        $query->whereDate('target_date', $nextTuesday);
                    })->first();
                } else {
                    $ledger = Ledger::where(function ($query) use ($nextMonday) {
                        $query->whereDate('target_date', $nextMonday);
                    })->first();
                }



                $isFeature = true;
                $isFeatureCurrent = true;
            } else {
                // The current day is not between Friday and Sunday, so follow your existing logic
                if ($currentDate->hour >= 17) {
                    $ledger = Ledger::whereDate('start_date', $currentDate)->first();
                    $isFeature = true;
                } else {
                    $ledger = Ledger::whereDate('target_date', $currentDate)->first();
                }
            }


            if (!$ledger) {
                return $this->fail('no sections found', 404);
            }

            $current_time = Carbon::now();
            $open_time = Carbon::createFromFormat('H:i:s', $ledger->open_at);

            $current_time->setTimezone('Asia/Yangon');
            $open_time->setTimezone('Asia/Yangon');

            if ($isFeature) {
                if (!$isFeatureCurrent) {
                    if ($open_time->gt($current_time)) {
                        return $this->fail('sections are not open yet', 400);
                    }
                }
            }


            $mm_sections = [];

            $sections = $ledger->sections;
            foreach ($sections as $section) {
                unset($section->created_at);
                unset($section->updated_at);
                unset($section->ledger_id);


                $limit_time = Carbon::createFromFormat('H:i:s', $section->limit_at);
                $limit_time->setTimezone('Asia/Yangon');
                if ($current_time->gt($limit_time)) {

                    if ($isFeature  == true) {
                        if ($isFeatureCurrent == true) {
                            $section->is_limited = false;
                        }
                        $section->is_limited = false;
                    } else {
                        $section->is_limited = true;
                    }
                } else {
                    $section->is_limited = false;
                }

                $close_time = Carbon::createFromFormat('H:i:s', $section->close_at);
                $close_time->setTimezone('Asia/Yangon');
                if ($current_time->gt($close_time)) {
                    if ($isFeature  == true) {
                        $section->is_closed = false;
                    } else {
                        $section->is_closed = true;
                    }
                } else {
                    $section->is_closed = false;
                }
                $section->target_date =  $ledger->target_date;
                if ($section->section_type_id == 0) {
                    array_push($mm_sections, $section);
                }
            }

            $mm_sections = array_reverse($mm_sections);
            $data = [
                // "sections"=>$sections,
                "mm_sectioins" => $mm_sections,
            ];


            return $this->success('get sections successful', $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }

    //     public function get_sections()
    //     {
    //         try {
    //             $today = Carbon::today();
    //             $current_time = \Carbon\Carbon::now();

    //             // $currentDate = Carbon::now();

    //             $isFeature = false;
    //             $currentDate = Carbon::now('Asia/Yangon');
    //             $currentDayOfWeek = $currentDate->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

    //             // Calculate the upcoming Friday (end of the current week)
    //             $endOfWeek = $currentDate->copy()->endOfWeek(Carbon::FRIDAY);

    //            // Calculate the upcoming Sunday (start of the next week)
    //             $startOfNextWeek = $currentDate->copy()->next(Carbon::SUNDAY);

    //             $isFeatureCurrent = false;



    // // // Check if the current day is between Friday and Sunday (i.e., the weekend)
    // if ($currentDayOfWeek > Carbon::FRIDAY || $currentDayOfWeek === Carbon::SUNDAY) {
    //     // The current day is between Friday and Sunday (including Sunday), so retrieve the Ledger data for the next week's Monday
    //         $nextMonday = $currentDate->copy()->next(Carbon::MONDAY);
    //    $ledger = Ledger::where(function ($query) use ($nextMonday) {
    //         $query->whereDate('target_date', $nextMonday);
    //     })->first();
    //     $isFeature = true;
    //     $isFeatureCurrent = true;
    // } else {
    //     // The current day is not between Friday and Sunday, so follow your existing logic
    //     if ($currentDate->hour >= 17) {
    //         $ledger = Ledger::whereDate('start_date', $currentDate)->first();
    //         $isFeature = true;
    //     } else {
    //          $ledger = Ledger::whereDate('target_date', $currentDate)->first();
    //     }
    // }


    //             if(!$ledger){
    //                 return $this->fail('no sections found',404);
    //             }

    //             $current_time = Carbon::now();
    //             $open_time = Carbon::createFromFormat('H:i:s', $ledger->open_at);

    //             $current_time->setTimezone('Asia/Yangon');
    //             $open_time->setTimezone('Asia/Yangon');

    //             if($isFeature){
    //                 if(!$isFeatureCurrent){
    //                      if ($open_time->gt($current_time)) {
    //                 return $this->fail('sections are not open yet',400);
    //                   }
    //                 }

    //             }


    //             $mm_sections=[];

    //             $sections=$ledger->sections;
    //             foreach($sections as $section){
    //                 unset($section->created_at);
    //                 unset($section->updated_at);
    //                 unset($section->ledger_id);


    //                 $limit_time = Carbon::createFromFormat('H:i:s', $section->limit_at);
    //                 $limit_time->setTimezone('Asia/Yangon');
    //                 if ($current_time->gt($limit_time)) {

    //                     if($isFeature  == true){
    //                         if($isFeatureCurrent ==true){
    //                              $section->is_limited=false;
    //                         }
    //                      $section->is_limited=false;
    //                     }else{
    //                         $section->is_limited=true;
    //                     }
    //                 }else{
    //                     $section->is_limited=false;
    //                 }

    //                 $close_time = Carbon::createFromFormat('H:i:s', $section->close_at);
    //                 $close_time->setTimezone('Asia/Yangon');
    //                 if ($current_time->gt($close_time)) {
    //                     if($isFeature  == true){
    //                       $section->is_closed=false;
    //                     }else{
    //                         $section->is_closed=true;
    //                     }

    //                 }else{
    //                     $section->is_closed=false;
    //                 }

    //                 if($section->section_type_id==0){
    //                     array_push($mm_sections,$section);
    //                 }
    //             }
    //           $mm_sections = array_reverse($mm_sections);
    //             $data=[
    //                 // "sections"=>$sections,
    //                 "mm_sectioins"=>$mm_sections,
    //             ];


    //             return $this->success('get sections successful',$data);
    //         } catch (\Throwable $th) {
    //                 return $this->fail($th->getMessage()?$th->getMessage():"server error",500);
    //         }
    //     }

    public function get_numbers($section_id)
    {
        try {
            //TODO::check the section date from parent ladger


            //check if section exists
            $section = Section::find($section_id);


            if (!$section) {
                $this->fail('section not found', 404);
            }

            switch ($section->section_index) {
                case 0:
                    $numbers = $section->mm_morning_numbers;
                    break;
                case 1:
                    $numbers = $section->mm_noon_numbers;
                    break;
                case 2:
                    $numbers = $section->mm_after_noon_numbers;
                    break;
                case 3:
                    $numbers = $section->mm_evening_numbers;
                    break;
            }

            foreach ($numbers as $number) {
                if ($number->current_amount > 0) {
                    $number->percentage = ($number->current_amount / $number->limit_amount) * 100;
                } else {
                    $number->percentage = 0;
                }
                $number->available = $number->limit_amount - $number->current_amount > 0;
                $number->life_amount = $number->limit_amount - $number->current_amount;
                $number->multiply = $section->pay_back_multiply;

                unset($number->created_at);
                unset($number->updated_at);
                unset($number->ledger_id);
            }
            $data = [
                "numbers" => $numbers,
            ];

            return $this->success('get numbers successful', $data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage() ? $th->getMessage() : "server error", 500);
        }
    }
}
