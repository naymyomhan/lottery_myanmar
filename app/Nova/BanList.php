<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class BanList extends Resource
{
     /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\BanList>
     */
    public static $model = \App\Models\BanList::class;

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
             BelongsTo::make("User",'user'),
             BelongsTo::make("Admin",'admin'),
             Text::make('Comment','comment')->displayUsing(function ($value) {
                return mb_substr($value,0,40);
            })->onlyOnIndex(),
             Text::make('Banned At','created_at')->hideWhenCreating()->hideWhenUpdating(),
             Text::make('Expired At','expired_at')->hideWhenCreating()->hideWhenUpdating(),
            Boolean::make('Banned')
            ->sortable()
            ->hideWhenCreating()
            ->hideWhenUpdating(),
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
        return [
            new DownloadExcel,
            new \Cog\Laravel\Nova\Ban\Actions\Ban(),
            new \Cog\Laravel\Nova\Ban\Actions\Unban(),
        ];
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