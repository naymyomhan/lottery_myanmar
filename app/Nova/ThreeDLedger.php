<?php

namespace App\Nova;

use App\Nova\ThreeDPayBack;
use App\Nova\ThreeDResult;
use App\Observers\ThreeDLedgerObserver;
use Carbon\Carbon;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Oneduo\NovaTimeField\Time;
use App\Models\ThreDNumberBet as ThreDNumberBetModel;
use App\Models\ThreeDPayBack as ModelsThreeDPayBack;
use App\Models\RThreeDPayBack as RModelsThreeDPayBack;

class ThreeDLedger extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ThreeDLedger>
     */
    public static $model = \App\Models\ThreeDLedger::class;

     /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public function title(){
        return $this->target_date;
    }

    public static function label() {
        return '3D Ledgers';
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
     public static $search = [
        'id','target_date'
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
        Date::make("ထီဖွင့်လစ်မည့်ရက်စွဲ", 'target_date')->hideFromIndex()->hideFromDetail(),
        Text::make("ထီဖွင့်လစ်မည့်ရက်စွဲ", 'target_date', function () {
            $today = Carbon::today()->format('M d, Y');
            if (date('M d, Y', strtotime($this->target_date)) == $today) {
                return '<span style="color:#27d117; font-weight:bold;">Today</span>';
            } else {
                return date('M d, Y', strtotime($this->target_date));
            }
        })->asHtml()->hideWhenCreating()->hideWhenUpdating(),
      
       Date::make("ထိုးကြေးတင်ခွင့်စပြုရက်", 'open_date')->hideFromIndex()->hideFromDetail(),
       Text::make("ထိုးကြေးတင်ခွင့်စပြုရက်", 'open_date', function () {
            $today = Carbon::today()->format('M d, Y');
            if (date('M d, Y', strtotime($this->open_date)) == $today) {
                return '<span style="color:#27d117; font-weight:bold;">Today</span>';
            } else {
                return date('M d, Y', strtotime($this->open_date));
            }
        })->asHtml()->hideWhenCreating()->hideWhenUpdating(),
       Time::make("ထိုးကြေးတင်ခွင့်စပြုချိန်", 'open_at'),
       Date::make("နောက်ဆုံး ထိုးကြေးပြုခွင့်ရက်", 'limit_date')->hideFromIndex()->hideFromDetail(),
       Text::make("နောက်ဆုံး ထိုးကြေးပြုခွင့်ရက်", 'limit_date', function () {
            $today = Carbon::today()->format('M d, Y');
            if (date('M d, Y', strtotime($this->limit_date)) == $today) {
                return '<span style="color:#27d117; font-weight:bold;">Today</span>';
            } else {
                return date('M d, Y', strtotime($this->limit_date));
            }
        })->asHtml()->hideWhenCreating()->hideWhenUpdating(),

          Time::make("နောက်ဆုံး ထိုးကြေးပြုခွင့်အချိန်", 'limit_time'),

          Text::make("ဆ", 'pay_back_multiply'),
          Text::make("တွတ်ဆ", 'r_pay_back_multiply'),

          Text::make('စုစုပေါင်း ထီ ထိုးငွေ',function(){
               return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format($this->numbers->sum('current_amount'),0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
            })->asHtml()->hideWhenCreating()->hideWhenUpdating(),
          Text::make('စုစုပေါင်း ပေါက်ဂဏန်းထိုးငွေ',function(){
                if($this->tdresult==null){
                    return sprintf(
                        '<span style="%s">ထီဖွင့်လစ်ရန်စောင့်ဆိုင်းနေပါသည်</span>',
                        'color:#f53838',
                    ); 
                }
                return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format(ThreDNumberBetModel::where('three_d_ledger_id',$this->id)
                        ->where('thre_d_numbers_id',$this->tdresult->number_id)
                        ->sum('amount'),0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
                    })->asHtml()->hideFromIndex(),
            Text::make('စုစုပေါင်းလျော်ပေးရမဲ့ငွေ',function(){
                if($this->tdresult==null){
                    return sprintf(
                        '<span style="%s">ထီဖွင့်လစ်ရန်စောင့်ဆိုင်းနေပါသည်</span>',
                        'color:#f53838',
                    ); 
                }
                return '<span style="color:#27d117;font-size:17px;font-weight:bold;">'.
                        number_format(ThreDNumberBetModel::where('three_d_ledger_id',$this->id)
                        ->where('thre_d_numbers_id',$this->tdresult->number_id)
                        ->sum('amount')*$this->pay_back_multiply,0)
                        .'</span><span style="color:#27d117;"> Ks</span>';
                    })->asHtml()->hideFromIndex(),

            Text::make('လျော်ပေးရန်', function() {
                $pay_back=ModelsThreeDPayBack::where('three_d_ledger_id',$this->id)->first();
                if($pay_back){
                    return sprintf(
                        '<span style="%s">လျော်ပေးပြီးပါပြီ</span>',
                        'color:#27d117',
                    ); 
                }

                if($this->tdresult==null){
                    return sprintf(
                        '<span style="%s">စောင့်ဆိုင်းနေပါသည်</span>',
                        'color:#f53838',
                    ); 
                }else{
                    if($this->success==false){
                        return sprintf(
                            '<a style="%s" href="/winers/tpay_back/'.$this->id.'">Winers များကို လျော်ပေးရန်</a>',
                            'background-color:#e64545; padding:6px 20px; border-radius:5px; text-align:center; color:white',
                        );
                    }else{
                        return "Done";
                    }
                }
                
            })->asHtml()->hideFromIndex(),

             Text::make('တွတ် လျော်ပေးရန်', function() {
                $pay_back=RModelsThreeDPayBack::where('three_d_ledger_id',$this->id)->first();
                if($pay_back){
                    return sprintf(
                        '<span style="%s">လျော်ပေးပြီးပါပြီ</span>',
                        'color:#27d117',
                    ); 
                }

                if($this->tdresult==null){
                    return sprintf(
                        '<span style="%s">စောင့်ဆိုင်းနေပါသည်</span>',
                        'color:#f53838',
                    ); 
                }else{
                    if($this->success==false){
                        return sprintf(
                            '<a style="%s" href="/winers/rtpay_back/'.$this->id.'">Winers များကို လျော်ပေးရန်</a>',
                            'background-color:#e64545; padding:6px 20px; border-radius:5px; text-align:center; color:white',
                        );
                    }else{
                        return "Done";
                    }
                }
                
            })->asHtml()->hideFromIndex(),

            // AuditableLog::make(),

            HasOne::make('Result','tdresult',ThreeDResult::class)->hideWhenCreating(),

            HasMany::make('Winers','winers',ThreeDWinner::class),

            HasMany::make('လျော်ပေးထားသည့်မှတ်တမ်း','pay_backs',ThreeDPayBack::class),
            
            HasMany::make('တွတ် လျော်ပေးထားသည့်မှတ်တမ်း','rpay_backs',RThreeDPayBack::class),

            HasMany::make('Bets','bets',ThreDNumberBet::class),

            HasMany::make('Numbers','numbers',ThreDNumber::class)->hideFromDetail(function(){
                return $this->section_index==0?false:true;
            }),
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
        return [];
    }

       public static function authorizedToCreate(Request $request)
    {
        return true;
    }

    public function authorizedToDelete(Request $request)
    {
        return true;
    }

    public function authorizedToUpdate(Request $request)
    {
        return true;
    }
}