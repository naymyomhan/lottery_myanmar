<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Admin' => 'App\Policies\AdminPolicy',
            \App\Models\Admin::class => \App\Policies\AdminPolicy::class,
            \App\Models\Ads::class => \App\Policies\AdsPolicy::class,
            \App\Models\Agent::class => \App\Policies\AgentPolicy::class,
            \App\Models\GameRoom::class => \App\Policies\GameRoomPolicy::class,
        // \Pktharindu\NovaPermissions\Role::class => \App\Policies\RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}