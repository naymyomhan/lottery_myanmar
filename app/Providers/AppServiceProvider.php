<?php

namespace App\Providers;

use App\Models\Ledger;
use App\Models\Result;
use App\Models\Section;
use App\Models\ThreeDLedger;
use App\Models\ThreeDResult;
use App\Observers\LedgerObserver;
use App\Observers\ResultObserver;
use App\Observers\SectionObserver;
use App\Observers\ThreeDLedgerObserver;
use App\Observers\ThreeDResultObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 2D
        Ledger::observe(new LedgerObserver);
        Section::observe(new SectionObserver);
        Result::observe(new ResultObserver);

        // 3D
        ThreeDLedger::observe(new ThreeDLedgerObserver);
        ThreeDResult::observe(new ThreeDResultObserver);
    }
}