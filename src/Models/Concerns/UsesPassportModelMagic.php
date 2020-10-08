<?php

namespace Cidekar\Tenantmagic\Models\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Multitenancy\Models\Tenant;

trait UsesPassportModelMagic
{
    /**
     *  The tenants.
     *
     * @var array
     */
    private $tenants = [];

    /**
     * Passport override to find a validate for passport grant; requests token.
     *
     * @see /vendors/laravel/passport/bridge/userrepository
     * @param string $email The users email address
     * @return Boolean
     */
    public function validateForPassportPasswordGrant($password)
    {
        return \Hash::check($password, $this->password);
    }

    /**
     * Passport override to find a user for passport grant; requests token.
     *
     * @see /vendors/laravel/passport/bridge/userrepository
     * @param string $email The users email address
     * @return User
     */
    public function findForPassport($email)
    {
        $tenant = Tenant::current();

        if($tenant){
           $tenant->makeCurrent();

            $user = $this::where('email', $email)->first();
            if($user){
                $user->tenantId = $tenant->id;
                $user->domain = $tenant->domain;
                $user->tenantDatabase = $tenant->database;
                array_push($this->tenants, $user);
            }
        } else {
            // For every tenant, search until we have a match ...
            Tenant::all()->eachCurrent(function (Tenant $tenant) use ($email) {
                Tenant::current() === $tenant->id;
                $user = $this::where('email', $email)->first();
                if ($user) {
                    $user->tenantId = $tenant->id;
                    $user->domain = $tenant->domain;
                    $user->tenantDatabase = $tenant->database;
                    array_push($this->tenants, $user);
                    return;
                }
            });
        }

        if (empty($this->tenants)) {
            return;
        }

        $this->setTenantDatabaseConnection();

        // Inject a model instance into our routes!
        // Explicit model binding to inject the tenant model into the route.
        Route::bind('tenant', $this->tenants);

        $this->purgeTenantDatabaseConnection();

        return $this->tenants[0];
    }

    /**
     * Set the tenant's connection; allows for token storage into tenant's database.
     *
     * @return void
     */
    public function setTenantDatabaseConnection()
    {
        // We pick the first tenant from the tenants array and set this as the
        // database connection.
        $tenantConnectionName = $this->tenantDatabaseConnectionName();

        config([
            "database.connections.{$tenantConnectionName}.database" => $this->tenants[0]->tenantDatabase
        ]);
    }

    /**
     * Purge the tenant's connection.
     *
     * @return void
     */
    public function purgeTenantDatabaseConnection()
    {
        DB::purge($this->tenantDatabaseConnectionName());
    }
}
