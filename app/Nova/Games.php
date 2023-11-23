<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Games extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Games>
     */
    public static $model = \App\Models\Games::class;

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
           
            BelongsTo::make('admin')->hideWhenCreating()->hideWhenUpdating(),

            Text::make('Title','title')->displayUsing(function ($value) {
                return mb_substr($value,0,40);
            })->onlyOnIndex(),
            Text::make('Title','title')->hideFromIndex(),
            Text::make('Slug','slug')->hideFromIndex(),

            Image::make('image')
                ->store(
                    function(Request $request,$model){
                        $image=$request->image->store('Games','do');
                        return [
                            'image_location'=>$image,
                            'image_name'=>str_replace("Games/","",$image),
                            'image_path'=>"Post"
                        ];
                    }
                )->hideFromDetail()->hideFromIndex()->creationRules('required'),
            Image::make('Image','image_location')->disk('do')->hideWhenCreating()->hideWhenUpdating(),
            Markdown::make("Description",'description')->hideFromIndex()->alwaysShow(),
            Select::make('Category','category')
    ->options([
        0 => 'Lottery',
        1 => 'Game',
    ])
    ->hideFromIndex()
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
            Select::make('Type', 'type')
    ->options([
        0 => 'In APP',
        1 => 'X PWA',
        2 => 'Y PWA',
    ])
    ->hideFromIndex()
    ->displayUsingLabels()
    ->resolveUsing(function ($value, $resource) {
        $lottery = $resource->lottery;

        if ($lottery == 1) {
            return 1; // Select "X PWA"
        } else if ($lottery == 2) {
            return 2; // Select "Y PWA"
        } else {
            return 0; // Select "In APP"
        }
    }),

            Text::make('Url','url')->hideFromIndex(),
            Text::make("Current User count","current_user_count")->hideWhenCreating()->hideWhenUpdating(),
            Date::make("Upload At","created_at")->hideWhenCreating()->hideWhenUpdating(),
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
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return true;
    }
}