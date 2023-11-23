<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\DailyNewUser;
use App\Nova\Metrics\NewUserByHours;
use Laravel\Nova\Dashboard;

class UserAnalysis extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            //   (new   DailyNewUser()),
            // (new   NewUserByHours()),
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'user-analysis';
    }
}