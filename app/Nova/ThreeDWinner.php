<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ThreeDWinner extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ThreeDWinner>
     */
    public static $model = \App\Models\ThreeDWinner::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    public static $perPageViaRelationship = 10;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'type'
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
            BelongsTo::make('Number','thre_d_numbers',ThreDNumber::class),
            BelongsTo::make('user'),
            Text::make('Amount','bet_id', function () {
    $bet = \App\Models\ThreDNumberBet::where('id', $this->bet_id)->first();
    return $bet ? $bet->amount : null;
})->asHtml()->hideWhenCreating()->hideWhenUpdating(),
             Select::make('Type','type')
    ->options([
        0 => 'ဒဲ့',
        1 => 'တွတ်',
    ])
    ->displayUsingLabels()
    ->resolveUsing(function ($value, $resource) {
        // Assuming you have a field named "lottery" to determine the type
        $lottery = $resource->lottery;

        // Set the category based on the lottery field
        if ($lottery == 1) {
            return 1; // Select "Game"
        } else {
            return 0; // Select "Lottery"
        }
    }),
            // // AuditableLog::make(),
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
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }
}