<?php

namespace App\Nova;

use App\Observers\LedgerObserver;
use Carbon\Carbon;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Oneduo\NovaTimeField\Time;

class Ledger extends Resource
{       
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Ledger>
     */
    public static $model = \App\Models\Ledger::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public function title(){
        return $this->target_date->format('M d, Y');
    }

    public static function label() {
        return 'Daily Ledgers';
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
            Date::make('ထီဖွင့်လစ်မည့်ရက်စွဲ','target_date')->hideFromIndex()->hideFromDetail(),
            Text::make('ထီဖွင့်လစ်မည့်ရက်စွဲ','target_date',function(){
                if(date('M d, Y', strtotime($this->target_date))==Carbon::today()->format('M d, Y')){
                    return '<span style="color:#27d117; font-weight:bold;">Today</span>';
                }else{
                    return date('M d, Y', strtotime($this->target_date));
                }
            })->asHtml()->hideWhenCreating()->hideWhenUpdating(),
            Date::make('ထီထိုးခွင့်ပြု့မည့်ရက်စွဲ','start_date')->hideFromIndex()->hideFromDetail(),
            Text::make('ထီထိုးခွင့်ပြု့မည့်ရက်စွဲ','start_date',function(){
                if(date('M d, Y', strtotime($this->start_date))==Carbon::today()->format('M d, Y')){
                    return '<span style="color:#27d117; font-weight:bold;">Today</span>';
                }else{
                    return date('M d, Y', strtotime($this->start_date));
                }
            })->asHtml()->hideWhenCreating()->hideWhenUpdating(),
            // start_date
            Time::make('စာရင်းဖွင့်ချိန်','open_at'),
            Text::make('ထိုးငွေစုစုပေါင်း',function(){
                return '<span style="color:#27d117;font-size:18px;font-weight:bold;">'.number_format($this->mm_morning_numbers->sum('current_amount')+
                $this->mm_noon_numbers->sum('current_amount')+
                $this->mm_after_noon_numbers->sum('current_amount')+
                $this->mm_evening_numbers->sum('current_amount'),0).'</span>
                <span style="color:#27d117;"> Ks</span>';
            })->asHtml(),
            HasMany::make('sections'),
            // AuditableLog::make()
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