<?php

namespace Cidekar\Tenantmagic;

use Illuminate\Support\ServiceProvider;
use Cidekar\Tenantmagic\Commands\CreateTenantCommand;

class TenantmagicServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->publishes([
            __DIR__ . '/config/tenantmagic.php' => config_path('tenantmagic.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateTenantCommand::class,
            ]);
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Tenantmagic::class);

        $this->mergeConfigFrom(
            __DIR__ . '/config/tenantmagic.php',
            'tenantmagic'
        );
    }
}
