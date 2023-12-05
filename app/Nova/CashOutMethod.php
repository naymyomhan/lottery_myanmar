<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class CashOutMethod extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\CashOutMethod>
     */
    public static $model = \App\Models\CashOutMethod::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'payment_name';

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
            Text::make('payment_name')->creationRules('required'),
            Image::make('Logo','image')
                ->store(
                    function(Request $request,$model){
                        $image=$request->image->store('Empire/Payment','do');
                        return [
                            'image_location'=>$image,
                            'image_name'=>str_replace("Empire/Payment/","",$image),
                            'image_path'=>"Empire/Payment"
                        ];
                    }
                )->hideFromDetail()->hideFromIndex()->creationRules('required'),
            Image::make('Logo','image_location')->disk('do')->hideWhenCreating()->hideWhenUpdating(),
            HasMany::make("CashOut",'cash_outs'),
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