<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Promotion extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Promotion>
     */
    public static $model = \App\Models\Promotions::class;

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
        'id','name'
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
            BelongsTo::make('Admin')->hideWhenCreating(),
            Text::make('Promotion Name','name'),
            Image::make('Poster','image')
                ->store(
                    function(Request $request,$model){
                        $image=$request->image->store('Lottery/promotion','do');
                        return [
                            'image_location'=>$image,
                            'image_name'=>str_replace("Lottery/promotion/","",$image),
                            'image_path'=>"Lottery/Payment"
                        ];
                    }
                )->hideFromDetail()->hideFromIndex()->creationRules('required'),
            Image::make('Poster','image_location')->disk('do')->hideWhenCreating()->hideWhenUpdating(),

            HasMany::make("Promotion Up",'promotionTopup',PromotionTopUp::class)->hideWhenUpdating()->hideWhenCreating(),
            
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
}