<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class MmAfterNoonBet extends Resource
{
    public static $perPageViaRelationship = 20;
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\MmAfterNoonBet>
     */
    public static $model = \App\Models\MmAfterNoonBet::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'number';

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
            BelongsTo::make('Number','mm_after_noon_number',MmAfterNoonNumber::class)->sortable(),
            
              Text::make('User Code', function () {
            return $this->user->refer_code; // Assuming the user relationship is correctly defined
        }),
             BelongsTo::make('User', 'user', User::class)
            ->displayUsing(function ($user) {
                return $user->name; // Assuming the user model has a 'name' attribute
            }),

        Text::make('User Phone', function () {
            return $this->user->phone; // Assuming the user relationship is correctly defined
        }),
       

            Text::make('Amount','amount')->sortable(),
            Stack::make('Bet At', [
                Text::make('created_at',function(){
                    return $this->created_at->format('h:i a');
                }),
                Text::make('created_at',function(){
                    return $this->created_at->format('M d, Y');
                }),
            ]),
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