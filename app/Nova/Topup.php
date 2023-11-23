<?php

namespace App\Nova;

use Devpartners\AuditableLog\AuditableLog;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class TopUp extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\TopUp>
     */
    public static $model = \App\Models\TopUp::class;

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
        'id','topup_transaction_number','payment_transaction_number',
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
            Number::make('TopUp ID','topup_transaction_number')->textAlign('left')->hideFromIndex(),
            BelongsTo::make("User",'user')->withMeta(['placeholder' => 'Select User']),
            BelongsTo::make('Admin')->hideWhenCreating(),
            // Text::make('phone')->hideFromIndex()->hideFromDetail()->rules('required'),
            Text::make('amount'),
            Number::make('ငွေပေးချမှုနည်းလမ်း','payment_method')->textAlign('left'),
            Number::make('ငွေလက်ခံရရှိသောအကောင့်','payment_account_name')->textAlign('left')->hideFromIndex(),
            Number::make('ငွေလက်ခံရရှိသောအကောင့်နံပါတ်','payment_account_number')->textAlign('left')->hideFromIndex(),
            Text::make('ငွေလွဲပြေစာနံပါတ်','payment_transaction_number'),
            Text::make('ငွေဖြည့်သွင်းမှုအခြေအနေ', function() {
          if ($this->payment_transaction_number == null && $this->success == 0) {
        return sprintf(
            '<span style="%s">ငွေဖြည့်သွင်းရန် စောင့်ဆိုင်းနေသည်</span>  <a style="%s" href="/topup/cancel/'.$this->id.'">ငြင်းပါယ်မည်</a>',
            'color:#f53838','background-color:#ff9800; padding:4px 20px; border-radius:25px; text-align:center; color:white'
        );
    } else {
       if ($this->success == 0) {
    return sprintf(
        '<a style="%s" href="/topup/approve/'.$this->id.'">လက်ခံမည်</a> 
        <a style="%s" href="/topup/reject/'.$this->id.'">ဖျက်သိမ်းမည်</a> 
        <a style="%s" href="/topup/cancel/'.$this->id.'">ငြင်းပါယ်မည်</a>',
        'background-color:#2bbd38; padding:4px 20px; border-radius:25px; text-align:center; color:white',
        'background-color:#f53838; padding:4px 20px; border-radius:25px; text-align:center; color:white',
        'background-color:#ff9800; padding:4px 20px; border-radius:25px; text-align:center; color:white'
    );
    } elseif ($this->success == 1) {
        return '<span style="color:#2bbd38;font-weight:bold;">အောင်မြင်သည်</span>';
    }  elseif ($this->success == 2) {
        return '<span style="color:#ff0000;font-weight:bold;">ငြင်းပါယ်လိုက်သည်</span>'; 
    }else {
        // Return empty string or any other message if success is not 0 or 1
        return '';
    }
    }
})->asHtml(),

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
        return false;
    }
}