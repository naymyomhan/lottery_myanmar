<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Voucher extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Voucher>
     */
    public static $model = \App\Models\Voucher::class;

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
        'id', 'user_id'
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
            Text::make('Voucher Code', 'vouchers_code'),
            BelongsTo::make('User', 'user', User::class)
                ->displayUsing(function ($user) {
                    return $user->name; // Assuming the user model has a 'name' attribute
                }),
            Text::make('Total amount', 'total_amount'),
            BelongsTo::make('Section', 'section', Section::class)
                ->displayUsing(function ($section) {
                    return $section->section_type_name; // Assuming the section model has a 'section_type_name' attribute
                })
                ->onlyOnDetail(),
            Text::make('Time', function () {
                return $this->section->close_at; // Assuming the section relationship is correctly defined
            }),
            Text::make('Created At', function () {
                return $this->section->created_at; // Assuming the section relationship is correctly defined
            }),
            Text::make('Pay Back Multiply', function () {
                return $this->section->pay_back_multiply; // Assuming the section relationship is correctly defined
            }),
            HasMany::make('Morning Bets', 'mm_morning_bets', MmMorningBet::class)
                ->hideFromDetail(function () {
                    return $this->section->section_index == 0 ? false : true;
                }),
            HasMany::make('Noon Bets', 'mm_noon_bets', MmNoonBet::class)
                ->hideFromDetail(function () {
                    return $this->section->section_index == 2 ? false : true;
                }),
            HasMany::make('Afternoon Bets', 'mm_afternoon_bets', MmAfternoonBet::class)
                ->hideFromDetail(function () {
                    return $this->section->section_index == 2 ? false : true;
                }),
            HasMany::make('Evening Bets', 'mm_evening_bets', MmEveningBet::class)
                ->hideFromDetail(function () {
                    return $this->section->section_index == 3 ? false : true;
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
        return false;
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
