<?php

namespace App\Nova;

use App\Nova\Metrics\PromotionByDay;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class PromotionTopUp extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\PromotionTopUp>
     */
    public static $model = \App\Models\PromotionTopUps::class;

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
         'id','refer_code',
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
            Text::make('User Name', function () {
            return $this->user->name; // Assuming the user relationship is correctly defined
            }),
            BelongsTo::make('User Code', 'user', User::class)
            ->displayUsing(function ($user) {
                return $user->refer_code; // Assuming the user model has a 'name' attribute
            }),
            // BelongsTo::make('Promotion Name', 'promotion', Promotions::class),
            BelongsTo::make('Promotion Name', 'promotion', Promotion::class)
            ->displayUsing(function ($promotion) {
                return $promotion->name; // Assuming the user model has a 'name' attribute
            }),
            Text::make('Amount','amount'),
            Stack::make('တောင်းဆိုသောအချိန်', [
                Text::make("Upload At",function(){
                    return sprintf(
                        '<span style="%s">'.$this->created_at->format('Y-M d').'</span>',
                        'color:#fffff; font-size:12px',
                    );
                })->asHtml(),
                Text::make("Upload At",function(){
                    return sprintf(
                        '<span style="%s">'.$this->created_at->format('h:i a').'</span>',
                        'color:#fffff; font-size:12px',
                    );
                })->asHtml(),
               
            ]),
            Stack::make('လက်ခံသောအချိန်', [
                Text::make("Upload At",function(){
                    return sprintf(
                        '<span style="%s">'.$this->updated_at->format('Y-M d').'</span>',
                        'color:#fffff; font-size:12px',
                    );
                })->asHtml(),
                Text::make("Upload At",function(){
                    return sprintf(
                        '<span style="%s">'.$this->updated_at->format('h:i a').'</span>',
                        'color:#fffff; font-size:12px',
                    );
                })->asHtml(),
               
            ]),
            
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
        return [
            new PromotionByDay,
        ];
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