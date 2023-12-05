<?php

namespace App\Nova;

use App\Models\PromotionTopUps;
use App\Nova\UserPromoWallet;
use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Magdicom\NovaVisiblePassword\VisiblePassword;
use Laravel\Nova\Fields\Image;
class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id','name','phone'
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

            // Text::make('Agent','agent_id')
            //     ->sortable(),

            Image::make('Image', 'profile_picture_location')->disk('do')->hideWhenCreating()->hideWhenUpdating(),
            
            BelongsTo::make('Agent')->hideWhenUpdating()->hideWhenCreating(),

            Text::make('Name')
                ->sortable(),

            Text::make('User Code','refer_code')->hideWhenCreating()->hideWhenUpdating(),

            Number::make('Phone')
                ->sortable(),

            VisiblePassword::make('Password','password')->showOnUpdating()->hideFromIndex(),
                
            Boolean::make('Banned', 'banned')->resolveUsing(function ($value) {
            return $value ? 'Yes' : 'No';
            })->hideWhenUpdating()->hideWhenCreating(),
            BelongsTo::make('Main Wallet','main_wallet',UserMainWallet::class)->hideWhenUpdating()->hideWhenCreating(),
            BelongsTo::make('Promotion Wallet', 'userPromoWallet', UserPromoWallet::class)->hideWhenUpdating()->hideWhenCreating(),
            BelongsTo::make('Game Wallet','game_wallet',UserGameWallet::class)->hideWhenUpdating()->hideWhenCreating(),

             Text::make('Message', function() {
                return sprintf(
                    '<a style="%s" href="/messenger?to='.$this->id.'">
                        <img style="height: 30px;" src="https://iconarchive.com/download/i75883/martz90/circle/messages.ico" alt="send message">
                    </a>',
                    'background-color:#61D04D;  text-align:center; color:white',
                );
            })->asHtml()->onlyOnIndex(),

            
            // Text::make("Created At","created_at")->hideWhenCreating()->hideWhenUpdating(),
            Stack::make('Created At', [
                Text::make('created_at',function(){
                    return $this->created_at->format('h:i a');
                }),
                Text::make('created_at',function(){
                    return $this->created_at->format('M d, Y');
                }),
            ]),

            HasMany::make("Top Up",'topups',TopUp::class)->hideWhenUpdating()->hideWhenCreating(),
            
            HasMany::make("Promotion Up",'promotionTopup',PromotionTopUp::class)->hideWhenUpdating()->hideWhenCreating(),

            HasMany::make("Cash Out",'cashout',CashOut::class)->hideWhenUpdating()->hideWhenCreating(),
            // AuditableLog::make(),
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
            // 
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
        return [
            new \Cog\Laravel\Nova\Ban\Actions\Ban(),
            new \Cog\Laravel\Nova\Ban\Actions\Unban(),
        ];
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return true;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }
}