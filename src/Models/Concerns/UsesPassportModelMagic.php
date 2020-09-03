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
     * @return User
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

        // For every tenant, search until we have a match ...
        Tenant::all()->eachCurrent(function (Tenant $tenant) use ($email) {
            Tenant::current() === $tenant->id;
            $user = $this::where('email', $email)->first();
            if ($user) {
                $user->tenantId = $tenant->id;
                array_push($this->tenants, $user);
                return;
            }
        });

        if (empty($this->tenants)) {
            return;
        }

        // Tenant lookup from user
        $tenant = Tenant::where('id', $this->tenants[0]->tenantId)->get()->first();

        if (!$tenant) {
            return;
        }

        $tenantConnectionName = $this->tenantDatabaseConnectionName();

        config([
            "database.connections.{$tenantConnectionName}.database" => $tenant->database
        ]);

        // Inject a model instance into our routes!
        // Explicit model binding to inject the tenant model into the route.
        Route::bind('tenant', $tenant);

        DB::purge($tenantConnectionName);

        return $this->tenants[0];
    }
}
