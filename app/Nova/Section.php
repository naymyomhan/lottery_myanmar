<?php

namespace App\Nova;

use App\Models\Ledger;
use App\Models\MmAfterNoonBet as MmAfterNoonBetModel;
use App\Models\MmEveningBet as MmEveningBetModel;
use App\Models\MmMorningBet as MmMorningBetModel;
use App\Models\MmNoonBet as MmNoonBetModel;
use App\Models\PayBack as PayBackModel;
use App\Models\UkAfterNoonBet as UkAfterNoonBetModel;
use App\Models\UkEveningBet as UkEveningBetModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Oneduo\NovaTimeField\Time;
use Devpartners\AuditableLog\AuditableLog;
use Laravel\Nova\Fields\HasOne;

class Section extends Resource
{   

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 10;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Section>
     */
    public static $model = \App\Models\Section::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'close_at';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)    
    {           
        return [
            Text::make('ထီဖွင့်ရက်',function(){
                if(date('M d, Y', strtotime(Ledger::find($this->ledger_id)->target_date))==Carbon::today()->format('M d, Y')){
                    return '<span style="color:#27d117;font-weight:bold;">Today</span>';
                }else{
                    return date('M d, Y', strtotime(Ledger::find($this->ledger_id)->target_date));
                }
            })->asHtml()->hideWhenCreating()->hideWhenUpdating(),
            Text::make('ထီထိုးရန်ပိတ်ချိန်',function(){
                return Carbon::createFromFormat('H:i:s', $this->limit_at)->format('h:i a');
            })->asHtml()->hideWhenCreating()->hideWhenUpdating(),

            Text::make('ထီဖွင့်ချိန်',function(){
                return Carbon::createFromFormat('H:i:s', $this->close_at)->format('h:i a');
            })->asHtml()->hideWhenCreating()->hideWhenUpdating(),

            Text::make('ထီအမျိးအစား','section_type_name')->hideWhenUpdating(),
            Time::make('ထီထိုးရန်ပိတ်ချိန်','limit_at')->hideFromIndex()->hideFromDetail(),
            Time::make('ထီဖွင့်ချိန်','close_at')->hideFromIndex()->hideFromDetail(),
            Time::make('အလျော်ဆ','pay_back_multiply'),
            Text::make('စုစုပေါင်း ရငွေ',function(){
                switch ($this->section_index) {
                    case 0:
                        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format($this->mm_morning_numbers->sum('current_amount'),0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
                        break;
                    case 1:
                        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format($this->mm_noon_numbers->sum('current_amount'),0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
                        break;
                    case 2:
                        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format($this->mm_after_noon_numbers->sum('current_amount'),0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
                        break;
                    case 3:
                        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format($this->mm_evening_numbers->sum('current_amount'),0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
                        break;
                }
            })->asHtml(),

            Text::make('စုစုပေါင်းပေါက်ဂဏန်းထိုးငွေ',function(){
                if($this->result==null){
                    return sprintf(
                        '<span style="%s">ထီဖွင့်လှစ်ရန် စောင့်ဆိုင်းနေပါသည်</span>',
                        'color:#f53838',
                    ); 
                }

                switch ($this->section_index) {
                    case 0:
                        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format(MmMorningBetModel::where('section_id',$this->id)
                        ->where('mm_morning_number_id',$this->result->number_id)
                        ->sum('amount'),0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
                        break;
                    case 1:
                        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format(MmNoonBetModel::where('section_id',$this->id)
                        ->where('mm_noon_number_id',$this->result->number_id)
                        ->sum('amount'),0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
                        break;
                    case 2:
                        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format(MmAfterNoonBetModel::where('section_id',$this->id)
                        ->where('mm_after_noon_number_id',$this->result->number_id)
                        ->sum('amount'),0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
                        break;
                    case 3:
                        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format(MmEveningBetModel::where('section_id',$this->id)
                        ->where('mm_evening_number_id',$this->result->number_id)
                        ->sum('amount'),0)
                        .'</span><span style="color:#27d110;"> Ks</span>';
                        break;
                }
            })->asHtml()->hideFromIndex(),

            Text::make('စုစုပေါင်းလျော်ပေးရမဲ့ငွေ',function(){
                if($this->result==null){
                    return sprintf(
                        '<span style="%s">ထီဖွင့်လှစ်ရန် စောင့်ဆိုင်းနေပါသည်</span>',
                        'color:#f53838',
                    ); 
                }

                switch ($this->section_index) {
                    case 0:
        $amount = MmMorningBetModel::where('section_id', $this->id)
        ->where('mm_morning_number_id', $this->result->number_id)
        ->sum('amount') * $this->pay_back_multiply;

    if ($amount > $this->mm_morning_numbers->sum('current_amount')) {
        return '<span style="color:#ff0000;font-size:17px;font-weight:bold;">' .
            number_format($amount, 0) . '</span><span style="color:#27d117;"> Ks</span>';
    } else {
        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">' .
            number_format($amount, 0) . '</span><span style="color:#27d117;"> Ks</span>';
    }
    break;
                case 1:
    $amount = MmNoonBetModel::where('section_id', $this->id)
        ->where('mm_noon_number_id', $this->result->number_id)
        ->sum('amount') * $this->pay_back_multiply;

    if ($amount > $this->mm_noon_numbers->sum('current_amount')) {
        return '<span style="color:#ff0000;font-size:17px;font-weight:bold;">' .
            number_format($amount, 0) . '</span><span style="color:#27d117;"> Ks</span>';
    } else {
        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">' .
            number_format($amount, 0) . '</span><span style="color:#27d117;"> Ks</span>';
    }
    break;

case 2:
    $amount = MmAfterNoonBetModel::where('section_id', $this->id)
        ->where('mm_after_noon_number_id', $this->result->number_id)
        ->sum('amount') * $this->pay_back_multiply;

    if ($amount > $this->mm_after_noon_numbers->sum('current_amount')) {
        return '<span style="color:#ff0000;font-size:17px;font-weight:bold;">' .
            number_format($amount, 0) . '</span><span style="color:#27d117;"> Ks</span>';
    } else {
        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">' .
            number_format($amount, 0) . '</span><span style="color:#27d117;"> Ks</span>';
    }
    break;

                    case 3:
    $amount = MmEveningBetModel::where('section_id', $this->id)
        ->where('mm_evening_number_id', $this->result->number_id)
        ->sum('amount') * $this->pay_back_multiply;

    if ($amount > $this->mm_evening_numbers->sum('current_amount')) {
        return '<span style="color:#ff0000;font-size:17px;font-weight:bold;">' .
            number_format($amount, 0) . '</span><span style="color:#27d117;"> Ks</span>';
    } else {
        return '<span style="color:#27d117;font-size:17px;font-weight:bold;">' .
            number_format($amount, 0) . '</span><span style="color:#27d117;"> Ks</span>';
    }
    break;

                }
            })->asHtml(),

            Text::make('အမြတ်/အရှုံး', function () {
                if($this->result==null){
                    return sprintf(
                        '<span style="%s">ထီဖွင့်လှစ်ရန် စောင့်ဆိုင်းနေပါသည်</span>',
                        'color:#f53838',
                    ); 
                }

            switch ($this->section_index) {
                case 0:
                    $revenue = MmMorningBetModel::where('section_id', $this->id)
                        ->where('mm_morning_number_id', $this->result->number_id)
                        ->sum('amount') * $this->pay_back_multiply;

                    $cost = $this->mm_morning_numbers->sum('current_amount');
                    break;

                case 1:
                    $revenue = MmNoonBetModel::where('section_id', $this->id)
                        ->where('mm_noon_number_id', $this->result->number_id)
                        ->sum('amount') * $this->pay_back_multiply;

                    $cost = $this->mm_noon_numbers->sum('current_amount');
                    break;

                case 2:
                    $revenue = MmAfterNoonBetModel::where('section_id', $this->id)
                        ->where('mm_after_noon_number_id', $this->result->number_id)
                        ->sum('amount') * $this->pay_back_multiply;

                    $cost = $this->mm_after_noon_numbers->sum('current_amount');
                    break;

                case 3:
                    $revenue = MmEveningBetModel::where('section_id', $this->id)
                        ->where('mm_evening_number_id', $this->result->number_id)
                        ->sum('amount') * $this->pay_back_multiply;

                    $cost = $this->mm_evening_numbers->sum('current_amount');
                    break;

                default:
                    // Handle any other cases here
                    $revenue = 0;
                    $cost = 0;
                    break;
            }

            $profitLoss = $cost - $revenue;

            $profitLossText = number_format(abs($profitLoss), 0) . ' Ks';
            $profitLossColor = $profitLoss >= 0 ? '#27d117' : '#d11717';

            if ($profitLoss > 0) {
                $profitLossText = 'အမြတ် : ' . $profitLossText;
            } elseif ($profitLoss < 0) {
                $profitLossText = 'အရှုံး : ' . $profitLossText;
            } else {
                $profitLossText = 'No Profit/Loss';
            }

            return '<span style="color:' . $profitLossColor . '; font-size:17px; font-weight:bold;">' . $profitLossText . '</span>';
        })->asHtml()->onlyOnDetail(),
    

            Text::make('လျော်ပေးရန်', function() {
                $pay_back=PayBackModel::where('section_id',$this->id)->first();
                if($pay_back){
                    return sprintf(
                        '<span style="%s">လျော်ပေးပြီးပါပြီ</span>',
                        'color:#27d117',
                    ); 
                }

                if($this->result==null){
                    return sprintf(
                        '<span style="%s">ထီဖွင့်လှစ်ရန် စောင့်ဆိုင်းနေပါသည်</span>',
                        'color:#f53838',
                    ); 
                }else{
                    if($this->success==false){
                        return sprintf(
                            '<a style="%s" href="/winers/pay_back/'.$this->id.'">Winers များကို လျော်ပေးရန်</a>',
                            'background-color:#e64545; padding:6px 20px; border-radius:5px; text-align:center; color:white',
                        );
                    }else{
                        return "Done";
                    }
                }
                
            })->asHtml()->hideFromIndex(),

            HasOne::make('result'),

            HasMany::make('Winers','winers',Winner::class),

            HasMany::make('လျော်ပေးထားသည့်မှတ်တမ်း','pay_backs',PayBack::class),

            HasMany::make('Numbers','mm_morning_numbers',MmMorningNumber::class)->hideFromDetail(function(){
                return $this->section_index==0?false:true;
            }),
            HasMany::make('Bets','mm_morning_bets',MmMorningBet::class)->hideFromDetail(function(){
                return $this->section_index==0?false:true;
            }),


            HasMany::make('Numbers','mm_noon_numbers',MmNoonNumber::class)->hideFromDetail(function(){
                return $this->section_index==1?false:true;
            }),
            HasMany::make('Bets','mm_noon_bets',MmNoonBet::class)->hideFromDetail(function(){
                return $this->section_index==1?false:true;
            }),





            HasMany::make('Numbers','mm_after_noon_numbers',MmAfterNoonNumber::class)->hideFromDetail(function(){
                return $this->section_index==2?false:true;
            }),
            HasMany::make('Bets','mm_after_noon_bets',MmAfterNoonBet::class)->hideFromDetail(function(){
                return $this->section_index==2?false:true;
            }),



            HasMany::make('Numbers','mm_evening_numbers',MmEveningNumber::class)->hideFromDetail(function(){
                return $this->section_index==3?false:true;
            }),  
            HasMany::make('Bets','mm_evening_bets',MmEveningBet::class)->hideFromDetail(function(){
                return $this->section_index==3?false:true;
            }), 

            // AuditableLog::make(),
        ];
    }
    

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            new DownloadExcel,
        ];
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return true;
    }
}