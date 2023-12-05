<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Notification extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Notification>
     */
    public static $model = \App\Models\Notification::class;

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
            BelongsTo::make("User",'user')->withMeta(['placeholder' => 'Select User'])->hideWhenCreating(),
            Text::make('Title', 'title')->displayUsing(function ($value) {
                return mb_substr($value, 0, 40);
            })->onlyOnIndex(),
            Text::make('Title', 'title')->hideFromIndex(),
            Text::make("Message", 'message')->displayUsing(function ($value) {
                return mb_substr($value, 0, 40);
            }),
            Image::make('image')
                ->store(
                    function (Request $request, $model) {
                        $image = $request->image->store('Notification', 'do');
                        return [
                            'image_location' => $image,
                            'image_name' => str_replace("Notification/", "", $image),
                            'image_path' => "Notification"
                        ];
                    }
                )->hideFromDetail()->hideFromIndex()->creationRules('required'),
            Image::make('Image', 'image_location')->disk('do')->hideWhenCreating()->hideWhenUpdating(),
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