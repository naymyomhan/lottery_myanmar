<?php

namespace App\Nova;

use App\Models\MmAfterNoonBet;
use App\Models\MmEveningBet;
use App\Models\MmMorningBet;
use App\Models\MmNoonBet;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Winner extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Winner>
     */
    public static $model = \App\Models\Winner::class;

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
            BelongsTo::make('user'),
            Text::make('ထိုးသည့် ပမာဏ',function(){
                switch ($this->section->section_index) {
                    case 0:
                        return MmMorningBet::find($this->bet_id)->amount;
                        break;
                    case 1:
                        return MmNoonBet::find($this->bet_id)->amount;
                        break;
                    case 2:
                        return MmAfterNoonBet::find($this->bet_id)->amount;
                        break;
                    case 3:
                        return MmEveningBet::find($this->bet_id)->amount;
                        break;
                }
            })->asHtml()->sortable(),

            Text::make('လျော်ရမည့်ငွေ',function(){
                switch ($this->section->section_index) {
                    case 0:
                        return MmMorningBet::find($this->bet_id)->amount*$this->section->pay_back_multiply;
                        break;
                    case 1:
                        return MmNoonBet::find($this->bet_id)->amount*$this->section->pay_back_multiply;
                        break;
                    case 2:
                        return MmAfterNoonBet::find($this->bet_id)->amount*$this->section->pay_back_multiply;
                        break;
                    case 3:
                        return MmEveningBet::find($this->bet_id)->amount*$this->section->pay_back_multiply;
                        break;
                }
            })->asHtml()->sortable(),

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
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }
}