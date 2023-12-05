<?php

namespace App\Nova\Dashboards;

use App\Models\PromotionTopUps;
use App\Models\User;
use App\Models\UserMainWallet;
use App\Nova\Metrics\BetAmountByHours;
use App\Nova\Metrics\CashOutAmountByHours;
use App\Nova\Metrics\NewUserByHours;
use App\Nova\Metrics\TopUpAmountByHours;
use App\Nova\PromotionTopUp;
use Carbon\Carbon;
use Coroowicaksono\ChartJsIntegration\PieChart;
use Laravel\Nova\Dashboards\Main as Dashboard;
use Stepanenko3\NovaCards\Cards\SystemResourcesCard;
use App\Nova\Metrics\NewTopUp;
use App\Nova\Metrics\DailyNewUser;
class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */

     
    public function cards()
    {
          $totalUsers = User::count();
$paidUsers = UserMainWallet::where('balance', '>', 0)->whereHas('topups')->count();
$promoUsers = User::whereIn('id', PromotionTopUps::pluck('user_id')->toArray())->count();
$freeUsers = $totalUsers - ($paidUsers + $promoUsers);
        return [
           //
       

(new PieChart())
    ->title("Total User - " . $totalUsers)
    ->series([
        [
            'name' => 'Users',
            'data' => [
                $freeUsers,
                $paidUsers,
                $promoUsers,
            ],
            'backgroundColor' => ["#54E03A", "#E55E48", "#FFFFFF"],
        ]
    ])
    ->options([
        'xaxis' => [
            'categories' => ['Free Users', 'Paid Users', 'Promotion Users']
        ],
    ])
    ->width('1/3'),
        //    (new   DailyNewUser()),
        //     (new   NewUserByHours()),
        //   (new   NewTopUp()),
        //    new TopUpAmountByHours(),
        //    (new \App\Nova\Metrics\TopupAmountByDay),
        //    new CashOutAmountByHours(),
        //     (new \App\Nova\Metrics\DailyTwoDBetVoucherAmount),
       
        //    new BetAmountByHours()
          
           //    
        ];
    }
}