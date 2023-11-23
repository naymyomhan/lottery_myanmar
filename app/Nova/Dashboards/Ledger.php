<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\NewTopUp;
use App\Nova\v\DailySale;
use App\Nova\Metrics\TopUp;
use App\Nova\Metrics\TotalTopUp;
use Laravel\Nova\Dashboard;

class Ledger extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new NewTopUp(),
            // (new \App\Nova\Metrics\TopupAmountByDay)
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public  function uriKey()
    {
        return 'ledger';
    }

    public function label()
    {
        return 'Ledger Dashboard';
    }
}