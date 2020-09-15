<?php

namespace Cidekar\Tenantmagic;

use Illuminate\Support\ServiceProvider;

class TenantmagicServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // routes, event listeners, or any other functionality
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->publishes([
            __DIR__ . '/config/tenantmagic.php' => config_path('tenantmagic.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // bind any classes or functionality to the page
        $this->app->bind(Tenantmagic::class);

        $this->mergeConfigFrom(
            __DIR__ . '/config/tenantmagic.php',
            'tenantmagic'
        );
    }
}
