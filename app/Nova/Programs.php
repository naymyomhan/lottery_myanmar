<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Jangvel\NovaGutenberg\NovaGutenberg;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
// use Techouse\IntlDateTime\IntlDateTime as DateTime;

class Programs extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Programs>
     */
    public static $model = \App\Models\Programs::class;

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
            Text::make('Title','name')->displayUsing(function ($value) {
                return mb_substr($value,0,40);
            })->onlyOnIndex(),
            Text::make('Title','name')->hideFromIndex(),
            Text::make('Slag Name','slag_name')->hideFromIndex(),
            Text::make('Description','description')->displayUsing(function ($value) {
                return mb_substr($value,0,40);
            })->onlyOnIndex(),
            NovaGutenberg::make(__('Content'), 'description'),
            Number::make("Point","point_value"),
            Text::make('Start Date', 'start_date')->hideWhenCreating()->hideWhenUpdating(),
            Text::make('End Date', 'end_date')->hideWhenCreating()->hideWhenUpdating(),
            DateTime::make('Start Date', 'start_date')->hideFromIndex()->hideFromDetail(),
            DateTime::make('End Date', 'end_date')->hideFromIndex()->hideFromDetail(),
            DateTime::make(__('Updated at'), 'updated_at')
            ->hideWhenCreating(),
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