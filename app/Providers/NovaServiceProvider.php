<?php

namespace App\Providers;

use App\Nova\Admin;
use App\Nova\Agent;
use App\Nova\CashOut;
use App\Nova\CashOutMethod;
use App\Nova\Dashboards\AnalysisDashboard;
use App\Nova\Dashboards\UserInsights;
use App\Nova\PaymentAccount;
use App\Nova\PaymentMethod;
use App\Nova\Result;
use App\Nova\Section;
use App\Nova\User;
use App\Nova\GameRoom;
use App\Nova\GameServer;
use App\Nova\GameTransaction;
use App\Nova\Winner;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use App\Nova\Ads;
use App\Nova\Notification;
use App\Nova\Games;
use App\Nova\Faq;
use App\Nova\ContactUs;
use App\Nova\Tutorial;
use App\Nova\Programs;
use App\Nova\PrivacyPolicy as NovaPrivacyPolicy;
use App\Nova\TermsAndConditions;
use App\Nova\ThreeDLedger;
use App\Nova\TwoDCloseDay;
use App\Nova\TwoDHistory;
use App\Nova\ThreeDHistory;
use App\Nova\TopUp;
use App\Nova\Ledger;
use App\Nova\BanList;
use App\Nova\Recommendation;
use App\Nova\ThreeVoucher;
use App\Nova\Voucher;
use App\Nova\Promotion;
use App\Nova\PromotionTopUp;
use App\Nova\Dashboards\Ledger as DLedger;
use App\Nova\Dashboards\SaleAnalysis;
use App\Nova\Dashboards\UserAnalysis;
use App\Nova\ExchangeRate;
use Laravel\Nova\Dashboards\Main;

// use App\Nova\Permission;
// use App\Nova\Role;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::mainMenu(
            function (Request $request) {
                return [
                    MenuSection::dashboard(Main::class)->icon('chart-bar'),

                    MenuSection::make("Analysis", [
                        MenuItem::dashboard(AnalysisDashboard::class),
                        MenuItem::dashboard(UserAnalysis::class),
                        MenuItem::dashboard(SaleAnalysis::class)
                    ])->icon('document-text')->collapsable(),

                    MenuSection::make("Lottery", [
                        MenuItem::resource(TwoDHistory::class),
                        MenuItem::resource(ThreeDHistory::class),
                        MenuItem::resource(TwoDCloseDay::class),
                    ])->icon('document-text')->collapsable(),

                    MenuSection::make("2D Ledger", [
                        MenuItem::resource(Ledger::class),
                        // MenuItem::resource(Section::class),
                        MenuItem::resource(Voucher::class),
                    ])->icon('play')->collapsable(),

                    MenuSection::make("3D Ledger", [
                        MenuItem::resource(ThreeDLedger::class),
                        MenuItem::resource(ThreeVoucher::class),
                    ])->icon('play')->collapsable(),

                    MenuSection::make("Promotion", [
                        MenuItem::resource(Promotion::class),
                        MenuItem::resource(PromotionTopUp::class),
                    ])->icon('play')->collapsable(),

                    MenuSection::make("Shan Koe Mee", [
                        MenuItem::resource(GameRoom::class),
                        MenuItem::resource(GameServer::class),
                        MenuItem::resource(GameTransaction::class),
                        // MenuItem::resource(Section::class),
                    ])->icon('play')->collapsable(),

                    MenuSection::make("Game", [
                        MenuItem::resource(Games::class),
                    ])->icon('document-text')->collapsable(),

                    MenuSection::make('Top Up', [
                        MenuItem::resource(TopUp::class),
                        MenuItem::resource(PaymentMethod::class),
                        MenuItem::resource(PaymentAccount::class),
                    ])->icon('shopping-bag')->collapsable(),

                    MenuSection::make('Cash Out', [
                        MenuItem::resource(CashOut::class),
                        MenuItem::resource(CashOutMethod::class),
                    ])->icon('shopping-bag')->collapsable(),

                    MenuSection::make('Users', [
                        MenuItem::dashboard(UserInsights::class),
                        MenuItem::resource(Agent::class),
                        MenuItem::resource(Admin::class),
                        MenuItem::resource(User::class),
                        MenuItem::resource(BanList::class),

                    ])->icon('user')->collapsable(),

                    MenuSection::make('Role & Permission', [
                        MenuItem::link('Roles', '/resources/roles'),
                        MenuItem::link('Permission', '/resources/permissions'),
                    ])->icon('user')->collapsable(),

                    MenuSection::make('Accountant', [
                        MenuItem::dashboard(DLedger::class),
                        MenuItem::resource(TopUp::class),
                        MenuItem::resource(CashOut::class),
                    ])->icon('document-report')->collapsable(),

                    MenuSection::make('Utils', [
                        MenuItem::resource(ExchangeRate::class),
                        MenuItem::resource(Ads::class),
                        MenuItem::resource(Programs::class),
                        MenuItem::resource(Notification::class),
                        MenuItem::resource(Recommendation::class),
                    ])->icon('document-text')->collapsable(),

                    MenuSection::make('Settings', [
                        MenuItem::resource(Tutorial::class),
                        MenuItem::resource(Faq::class),
                        MenuItem::resource(ContactUs::class),
                        MenuItem::resource(NovaPrivacyPolicy::class),
                        MenuItem::resource(TermsAndConditions::class),
                    ])->icon('tag')->collapsable(),


                    MenuSection::make('Log Viewer', [
                        MenuItem::link('Dashboard', '/nova-logs/dashboard'),
                        MenuItem::link('Logs', '/nova-logs/list'),
                    ])
                        ->collapsable()
                        ->icon('document-text'),


                ];
            }
        );
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        // Gate::define('viewNova', function ($user) {
        //     return in_array($user->email, [
        //         'admin@gmail.com',
        //     ]);
        // });
        Gate::define('viewNova', function ($user) {
            return true;
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
            new \App\Nova\Dashboards\Ledger,
            new \App\Nova\Dashboards\UserInsights,
            new \App\Nova\Dashboards\UserAnalysis,
            new \App\Nova\Dashboards\SaleAnalysis,
            new \App\Nova\Dashboards\AnalysisDashboard,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new \PhpJunior\NovaLogViewer\Tool(),
            // new \Sereny\NovaPermissions\NovaPermissions(),
            // (new \Sereny\NovaPermissions\NovaPermissions())->canSee(function ($request) {
            //      return $request->user()->isSuperAdmin();
            //  }),
            // (new \Sereny\NovaPermissions\NovaPermissions())->canSee(function ($request) {
            //     return $request->user()->isSuperAdmin();
            // }),
            \Sereny\NovaPermissions\NovaPermissions::make()
                // ->roleResource("Role::class")
                // ->permissionResource(Permission::class)
                ->disablePermissions()
                ->hideFieldsFromRole([
                    'id',
                    'guard_name'
                ])
                ->hideFieldsFromPermission([
                    'id',
                    'guard_name',
                    'users',
                    'roles'
                ])
                ->resolveGuardsUsing(function ($request) {
                    return ['web'];
                })
            // ->resolveModelForGuardUsing(function($request) {
            //     /** @var App\Auth\CustomGuard $guard */
            //     $guard = auth()->guard();
            //     return $guard->getProvider()->getModel();
            // })
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
