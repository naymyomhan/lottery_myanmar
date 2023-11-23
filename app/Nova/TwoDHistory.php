<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Oneduo\NovaTimeField\Time;
use Carbon\Carbon;
class TwoDHistory extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\TwoDHistory>
     */
    public static $model = \App\Models\TwoDHistory::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

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
            ID::make()->sortable(),
            Text::make("Number","twod"),
            Text::make("Set","set"),
            Text::make("Value","value"),
            Text::make('Open Time','open_time')->hideWhenCreating(),
            Time::make("Open Time","open_time")->hideFromIndex()->hideFromDetail(),
            Text::make('ထီဖွင့်ရက်','target_date',function(){
                if(date('M d, Y', strtotime($this->created_at))==Carbon::today()->format('M d, Y')){
                    return '<span style="color:#27d117; font-weight:bold;">Today</span>';
                }else{
                    return date('M d, Y', strtotime($this->created_at));
                }
            })->asHtml()->hideWhenCreating()->hideWhenUpdating(),
            
            Date::make("Create At","created_at")->hideFromIndex()->hideFromDetail(),
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
        return [];
    }
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return true;
    }

    public function authorizedToDelete(Request $request)
    {
        return true;
    }
}