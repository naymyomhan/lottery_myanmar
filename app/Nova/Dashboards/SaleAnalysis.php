<?php

namespace App\Nova\Dashboards;

use App\Models\MmAfterNoonBet;
use App\Models\MmEveningBet;
use App\Models\MmMorningBet;
use App\Models\MmNoonBet;
use App\Models\ThreeDVoucher;
use App\Models\TopUp;
use App\Models\Voucher;
use Carbon\Carbon;
use Coroowicaksono\ChartJsIntegration\BarChart;
use Coroowicaksono\ChartJsIntegration\StackedChart;
use Laravel\Nova\Dashboard;

class SaleAnalysis extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        
         $currentDate = Carbon::now();

        // Get the start and end dates of the current month
        $startDate = $currentDate->copy()->startOfMonth();
        $endDate = $currentDate->copy()->endOfMonth();
        $currentDate = $startDate;

        // total bet amount from voucher 
        $totalbetAmountTwoD = [];
        $totalbetAmountThreeD = [];

        $morningsbetAmountTwoD = [];
        $noonsbetAmountTwoD = [];
        $afterNoonsbetAmountTwoD = [];
        $eveningsbetAmountTwoD = [];
        while ($currentDate <= $endDate && $currentDate <= $currentDate) {
            $totalBetTwod = Voucher::whereDate('created_at', $currentDate)->sum('total_amount');
            $totalBetThreed = ThreeDVoucher::whereDate('created_at', $currentDate)->sum('total_amount');

            // nuber bet
            $totalbetMoringTwod = MmMorningBet::whereDate('created_at', $currentDate)->sum('amount');
            $totalbetNoonTwod = MmNoonBet::whereDate('created_at', $currentDate)->sum('amount');
            $totalbetAfterNoonTwod = MmAfterNoonBet::whereDate('created_at', $currentDate)->sum('amount');
            $totalbetEveningBetTwod = MmEveningBet::whereDate('created_at', $currentDate)->sum('amount');
            $totalbetAmountTwoD[] = $totalBetTwod;
            $totalbetAmountThreeD[] = $totalBetThreed;
            $morningsbetAmountTwoD[] = $totalbetMoringTwod;
            $noonsbetAmountTwoD[] = $totalbetNoonTwod;
            $afterNoonsbetAmountTwoD[] = $totalbetAfterNoonTwod;
            $eveningsbetAmountTwoD[] = $totalbetEveningBetTwod;
            $days[] = $currentDate->day;
            $currentDate->addDay();
        }

        $morninghourlyTotalBetAmounts = [];
        $noonhourlyTotalBetAmounts = [];
        $afternoonhourlyTotalBetAmounts = [];
        $eveninghourlyTotalBetAmounts = [];

        for ($hour = 0; $hour <= 23; $hour++) {
          $startTime = $currentDate->copy()->setHour($hour)->setMinute(0)->setSecond(0);
          $endTime = $currentDate->copy()->setHour($hour)->setMinute(59)->setSecond(59);

          $totalBetAmountMorning = MmMorningBet::whereBetween('created_at', [$startTime, $endTime])
               ->sum('amount');
         $totalBetAmountNoon = MmNoonBet::whereBetween('created_at', [$startTime, $endTime])
               ->sum('amount');
        $totalBetAmountAfterNoon = MmAfterNoonBet::whereBetween('created_at', [$startTime, $endTime])
               ->sum('amount');
        $totalBetAmountEvening = MmEveningBet::whereBetween('created_at', [$startTime, $endTime])
               ->sum('amount');

          $morninghourlyTotalBetAmounts[] = $totalBetAmountMorning;
          $noonhourlyTotalBetAmounts[] = $totalBetAmountNoon;
          $afternoonhourlyTotalBetAmounts[] = $totalBetAmountAfterNoon;
          $eveninghourlyTotalBetAmounts[] = $totalBetAmountEvening;
        
        }

        $hourList = [];

        for ($hour = 0; $hour <= 23; $hour++) {
         $hourLabel = ($hour == 0) ? '12 AM' : (($hour <= 11) ? $hour . ' AM' : (($hour == 12) ? '12 PM' : ($hour - 12) . ' PM'));
         $hourList[] = $hourLabel;
        }



        
        return [
         
    (new BarChart())
    ->title('BET AMOUNT')
    ->animations([
        'enabled' => true,
        'easing' => 'easeinout',
    ])
    ->series(array(
     [
        'barPercentage' => 0.5,
        'label' => 'Two D Bet',
        'backgroundColor' => '#F87900',
        'data' => $totalbetAmountTwoD,
     ],
    [
        'barPercentage' => 0.5,
        'label' => 'Three D Bet',
        'backgroundColor' =>  '#90ed7d',
        'data' => $totalbetAmountThreeD,
    ]
    ),
    )
    ->options([
        'xaxis' => [
            'categories' => $days
        ],
         'btnReload' => true
    ]),

    (new BarChart())
    ->title('TwoD BET AMOUNT Buy Section')
    ->animations([
        'enabled' => true,
        'easing' => 'easeinout',
    ])
    ->series(array(
     [
        'barPercentage' => 0.5,
        'label' => 'Moring Number Bet',
        'backgroundColor' => '#ffcc5c',
        'data' => $morningsbetAmountTwoD,
     ],
    [
        'barPercentage' => 0.5,
        'label' => 'Noon Number Bet',
        'backgroundColor' =>  '#91e8e1',
        'data' => $noonsbetAmountTwoD,
    ],  [
        'barPercentage' => 0.5,
        'label' => 'After Noon Number Bet',
        'backgroundColor' =>  '#ff6f69',
        'data' => $afterNoonsbetAmountTwoD,
    ],  [
        'barPercentage' => 0.5,
        'label' => 'Evening Number Bet',
        'backgroundColor' =>  '#90ed7d',
        'data' => $eveningsbetAmountTwoD,
    ]
    ),
    )
    ->options([
        'xaxis' => [
            'categories' => $days
        ],
         'btnReload' => true
    ]),

    (new BarChart())
    ->title("Today's Hourly Session Lottery Bet Amount")
    ->animations([
        'enabled' => true,
        // 'easing' => 'easeinout',
    ])
    ->series(array(
     [
        'barPercentage' => 0.5,
        'label' => 'Moring Number Bet',
        'backgroundColor' => '#ffcc5c',
        'data' => $morninghourlyTotalBetAmounts,
     ],
    [
        'barPercentage' => 0.5,
        'label' => 'Noon Number Bet',
        'backgroundColor' =>  '#91e8e1',
        'data' => $noonhourlyTotalBetAmounts,
    ],  [
        'barPercentage' => 0.5,
        'label' => 'After Noon Number Bet',
        'backgroundColor' =>  '#ff6f69',
        'data' => $afternoonhourlyTotalBetAmounts,
    ],  [
        'barPercentage' => 0.5,
        'label' => 'Evening Number Bet',
        'backgroundColor' =>  '#90ed7d',
        'data' => $eveninghourlyTotalBetAmounts,
    ]
    ),
    )
    ->options([
        'xaxis' => [
            'categories' => $hourList
        ],
         'btnReload' => true
    ]),

        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'sale-analysis';
    }
}