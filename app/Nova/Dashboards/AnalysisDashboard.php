<?php

namespace App\Nova\Dashboards;

use App\Models\CashOut;
use App\Models\CashOutMethod;
use App\Models\PromotionTopUps;
use App\Models\TopUp;
use App\Models\User;
use App\Models\UserMainWallet;
use Carbon\Carbon;
use Coroowicaksono\ChartJsIntegration\BarChart;
use Coroowicaksono\ChartJsIntegration\PieChart;
use Coroowicaksono\ChartJsIntegration\StackedChart;
use Laravel\Nova\Dashboard;

class AnalysisDashboard extends Dashboard
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

             $totalTopUp = TopUp::sum('amount');
             $successTopUp = TopUp::where('success', '=', 1)->sum('amount');
             $failTopUp = TopUp::where('success', '=', 2)->sum('amount');
             $onGoingTopUp = TopUp::where('success', '=', 0)->sum('amount');

             $totalCashout =CashOut::sum('amount');
             $successCashout = CashOut::where('success', '=', 1)->sum('amount');
             $failCashout = CashOut::where('success', '=', 2)->sum('amount');
             $onGoingCashout = CashOut::where('success', '=', 0)->sum('amount');

             $currentDate = Carbon::now();

// Get the start and end dates of the current month
$startDate = $currentDate->copy()->startOfMonth();
$endDate = $currentDate->copy()->endOfMonth();

// Calculate the sum of 'amount' for the specified days
$totals = [];
$totalsfails = [];
$totalsCashout = [];
$totalsCashoutFails = [];
$days = [];
$dailyNewUsers = [];

$currentDate = $startDate;
while ($currentDate <= $endDate && $currentDate <= $currentDate) {
    $total = TopUp::where('success', '=', 1)->whereDate('created_at', $currentDate)->sum('amount');
    $totalfail = TopUp::where('success', '=', 2)->whereDate('created_at', $currentDate)->sum('amount');
    $totalCashout = CashOut::where('success', '=', 1)->whereDate('created_at', $currentDate)->sum('amount');
    $totalCashoutFail = CashOut::where('success', '=', 2)->whereDate('created_at', $currentDate)->sum('amount');

    // Daily count of new users
    $dailyNewUser = User::whereDate('created_at', $currentDate)->count();
    
    $totals[] = $total;
    $dailyNewUsers[] = $dailyNewUser;
    $totalsfails[] = $totalfail;
    $totalsCashout[] = $totalCashout;
    $totalsCashoutFails[] = $totalCashoutFail;
    $days[] = $currentDate->day;
    $currentDate->addDay();
}


      
        return [
                (new BarChart())
    ->title('User')
    ->animations([
        'enabled' => true,
        'easing' => 'easeinout',
    ])
    ->series(array(
     [
        'barPercentage' => 0.5,
        'label' => 'Total User',
        'backgroundColor' => '#F87900',
        'data' => $dailyNewUsers,
     ],
    [
        'barPercentage' => 0.5,
        'label' => 'Normal User',
        'backgroundColor' =>  '#90ed7d',
        'data' => $dailyNewUsers,
    ],
    [
        'barPercentage' => 0.5,
        'label' => 'TopUp User',
        'backgroundColor' => "#b088d8",
        'data' => $dailyNewUsers,
    ]
    ),
    )
    ->options([
        'xaxis' => [
            'categories' => $days
        ],
         'btnReload' => true
    ])
    ->width('2/3'),
    
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

    (new BarChart())
    ->title('TopUp')
    ->animations([
        'enabled' => true,
        'easing' => 'easeinout',
    ])
    ->series(array([
        'barPercentage' => 0.5,
        'label' => 'TopUp  Success',
        'backgroundColor' =>  '#90ed7d',
        'data' => $totals,
    ],
    [
        'barPercentage' => 0.5,
        'label' => 'TopUp Fail',
        'backgroundColor' => '#F87900',
        'data' => $totalsfails,
    ]
    ))
    ->options([
        'xaxis' => [
            'categories' => $days
        ],
         'btnReload' => true
    ])
    ->width('2/3'),
      (new PieChart())
    ->title("Total TopUp - " . $totalTopUp)
    ->series([
        [
            'name' => 'TopUp',
            'data' => [
                $successTopUp,
                $failTopUp,
                $onGoingTopUp,
            ],
            'backgroundColor' => ["#54E03A", "#E55E48", "#FFFFFF"],
        ]
    ])
    ->options([
        'xaxis' => [
            'categories' => ['Success', 'Fail', 'OnGoing']
        ],
         'btnReload' => true
    ])
    ->width('1/3'),

    (new BarChart())
    ->title('Withdraw')
    ->animations([
        'enabled' => true,
        'easing' => 'easeinout',
    ])
    ->series(
        array([
        'barPercentage' => 0.5,
        'label' => 'Withdraw Success',
        'backgroundColor' =>  '#90ed7d',
        'data' => $totalsCashout,
    ],
    [
        'barPercentage' => 0.5,
        'label' => 'Withdraw Fails',
        'backgroundColor' => '#F87900',
        'data' => $totalsCashoutFails,
    ]
    ))
    ->options([
        'xaxis' => [
            'categories' => $days
        ],
         'btnReload' => true
    ])
    ->width('2/3'),
     (new PieChart())
    ->title("Total Withdraw - " . $totalCashout)
    ->series([
        [
            'name' => 'Withdraw',
            'data' => [
                $successCashout,
                $failCashout,
                $onGoingCashout,
            ],
            'backgroundColor' => ["#54E03A", "#E55E48", "#FFFFFF"],
        ]
    ])
    ->options([
        'xaxis' => [
            'categories' => ['Success', 'Fail', 'OnGoing']
        ],
        'btnReload' => true
    ])
    ->width('1/3'),
   


    // (new StackedChart())
    // ->title('TopUp')
    // ->model('\App\Models\TopUp') // Use Your Model Here
    // ->series(array(
    //     [
    //     'label' => 'TopUp Amount',
    //     'filter' => [
    //         'key' => 'amount', 
    //         'value' => '1'
    //     ],
    //     'borderColor' => '#F87900', // Add This to change the border color
    // ],))
    // ->options([
    //     'showTotal' => false ,
    //     'queryFilter' => array([    // add array of filter with this format
    //         'key' => 'success',
    //         'operator' => '=',
    //         'value' => '1'
    //     ],[
    //         'key' => 'updated_at',
    //         'operator' => 'IS NOT NULL',
    //     ])
    // ])
    // ->width('2/3'),

    
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'analysis-dashboard';
    }
}