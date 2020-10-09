<?php

namespace Cidekar\Tenantmagic\Tests;

use Orchestra\Testbench\Concerns\WithLaravelMigrations;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Multitenancy\MultitenancyServiceProvider;
use Cidekar\Tenantmagic\TenantmagicServiceProvider;
use Cidekar\Tenantmagic\Tests\Stubs\MagicTenant;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;
use Spatie\Multitenancy\Tasks\SwitchTenantDatabaseTask;
use Laravel\Passport\PassportServiceProvider;

abstract class TestCase extends Orchestra
{
    use WithLaravelMigrations;

    protected MagicTenant $tenant;

    protected MagicTenant $anotherTenant;

    public $passport;

    public function setUp($options = null): void
    {
        $this->options = Collect($options);

        parent::setUp();

        $this->withFactories(__DIR__.'/factories');

        $this->migrateLandlord();
    }

    protected function getPackageProviders($app)
    {
        return [
            MultitenancyServiceProvider::class,
            PassportServiceProvider::class,
            TenantmagicServiceProvider::class
            ];
    }

    public function migrateLandlord() : self
    {
        $migrationPaths = [
            'landlord' => realpath(__DIR__ . '/database/migrations/landlord'),
            'tenant' => realpath(__DIR__ . '/database/migrations/tenant')
        ];

        $this->artisan("migrate:fresh --database=landlord --path={$migrationPaths['landlord']} --realpath")->assertExitCode(0);

        return $this;
    }

    public function migrateTenants() : self
    {

        config()->set('multitenancy.switch_tenant_tasks', [SwitchTenantDatabaseTask::class]);

        $this->tenant = factory(MagicTenant::class)->create(['database' => 'laravel_tenantmagic_tenant_1']);

        $this->anotherTenant = factory(MagicTenant::class)->create(['database' => 'laravel_tenantmagic_tenant_2']);

        $this->tenant->makeCurrent();

        $migrationPaths = [
            'landlord' => realpath(__DIR__ . '/database/migrations/landlord'),
            'tenant' => realpath(__DIR__ . '/database/migrations/tenant')
        ];

        $this->artisan("migrate:fresh --database=tenant --path={$migrationPaths['tenant']} --realpath")->assertExitCode(0);

        $this->anotherTenant->makeCurrent();

        $this->artisan("migrate:fresh --database=tenant --path={$migrationPaths['tenant']} --realpath")->assertExitCode(0);

        return $this;
    }

    public function createTenantUsers()
    {

        config(['database.default' => 'tenant']);

        $this->tenant->makeCurrent();

        factory(MagicUser::class)->create(['email' => 'tenant@magic.com', 'name' => 'Tenant Magic']);

        $this->tenant->forget();

        $this->anotherTenant->makeCurrent();

        factory(MagicUser::class)->create(['email' => 'tenant@magic.com']);

        $this->tenant->forget();
    }

    public function getEnvironmentSetUp($app)
    {
        config(['database.default' => 'landlord']);

        config()->set('multitenancy.landlord_database_connection_name', 'landlord');

        config()->set('multitenancy.tenant_database_connection_name', 'tenant');

        config([
            'database.connections.landlord' => [
                'driver' => 'mysql',
                'url' => env('DATABASE_URL'),
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD'),
                'database' => 'laravel_tenantmagic_landlord',
            ],

            'database.connections.tenant' => [
                'driver' => 'mysql',
                'username' => env('DB_USERNAME', 'root'),
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'password' => env('DB_PASSWORD'),
                'database' => null,
            ],
        ]);

        config()->set('queue.default', 'database');

        config()->set('queue.connections.database', [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'connection' => 'landlord',
        ]);

        if($this->options->get('withoutMiddleware') !== true)
        {
            $app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware('\Spatie\Multitenancy\Http\Middleware\NeedsTenant');
            $app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware('\Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession');
        }
    }

    public function passportSetup()
    {

        config()->set('passport.storage.database.connection','landlord');

        \Artisan::call("passport:client --password --name=tenantmagic --provider=users");

        config()->set('database.default','landlord');

        $this->passport = \DB::table('oauth_clients')->where('id', 1)->get()->first();

    }
}
