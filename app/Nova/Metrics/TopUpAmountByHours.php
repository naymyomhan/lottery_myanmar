<?php

namespace App\Nova\Metrics;

use App\Models\TopUp;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;

class TopUpAmountByHours extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        // $paidUsers = UserMainWallet::where('balance', '>', 0)->whereHas('topups')->count();
        // $topUps = TopUp::where('success', 1)->get();
        return $this->sumByHours($request, TopUp::class, 'amount')->format([
                    'thousandSeparated' => true,
                    'mantissa' => 2,
                ]);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => __('30 Hours'),
            60 => __('60 Hours'),
            90 => __('90 Hours'),
        ];
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'top-up-amount-by-hours';
    }
}