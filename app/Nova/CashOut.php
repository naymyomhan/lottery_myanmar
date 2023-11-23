<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class CashOut extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\CashOut>
     */
    public static $model = \App\Models\CashOut::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'amount';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id','amount','receive_account_name','receive_account_number'
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
            ID::make()->sortable()->hideFromIndex(),
            BelongsTo::make('user'),
            BelongsTo::make('Admin')->hideWhenCreating(),
            BelongsTo::make('cash_out_method','cash_out_method',CashOutMethod::class),
            Text::make('amount'),
            Text::make('Action', function() {
    if ($this->success == 0) {
        return sprintf(
            '<a style="%s" href="/cash_out/make_as_done/'.$this->id.'">Make as Done</a> | <a style="%s" href="/cash_out/reject/'.$this->id.'">Reject</a>',
            'background-color:#2bbd38; padding: 4px by 20px; border-radius: 25px; text-align: center; color:white',
            'color:#ff0000;font-weight:bold;'
        );
    } elseif ($this->success == 1) {
        return '<span style="color:#2bbd38;font-weight:bold;">အောင်မြင်သည်</span>';
    }  elseif ($this->success == 2) {
        return '<span style="color:#ff0000;font-weight:bold;">ငြင်းပါယ်လိုက်သည်</span>'; 
    }else {
        // Return empty string or any other message if success is not 0 or 1
        return '';
    }
})->asHtml(),
            // Text::make('Action', function() {
            //     if($this->success==false){
            //         return sprintf(
            //             '<a style="%s" href="/cash_out/make_as_done/'.$this->id.'">Make as Done</a>',
            //             'background-color:#2bbd38; padding:4px 20px; border-radius:25px; text-align:center; color:white',
            //         );
            //     }else{
            //         return '<span style="color:#2bbd38;font-weight:bold;">Success</span>';
            //     }
            // })->asHtml(),
            Text::make('receive_account_name'),
            Text::make('receive_account_number'),
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