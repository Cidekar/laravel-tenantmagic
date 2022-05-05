<?php

namespace Cidekar\Tenantmagic\Models\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Spatie\Multitenancy\Models\Tenant;

trait UsesPassportModelMagic
{
    /**
      * Tenant domain cache for route binding.
      * 
      * @var array
      */
      private $domains = [];

    /**
     *  The tenant the current user is authorized to access.
     *
     * @var array
     */
    private $tenant = null;

    /**
     *  Tenant and user cache for route binding.
     *
     * @var array
     */
    private $tenants = [];

    /**
     * Passport override to find a validate for passport grant; requests tokeÍn.
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

    // Attempting to authorize the user for 
    // a know tenant; provided in the original 
    // request.
    if( Tenant::current() )
    {
        
        $user = $this::where('email', $email)->first();
        
        if($user){

            $this->mergeTenantUserForRoute($user);    
        }
    } 
    // Otherwise the tenant is not known, 
    // so search all tenants returning each 
    // where the user is found, and authorize 
    // against the provided user's password.
    else 
    {  
        Tenant::all()->eachCurrent(function (Tenant $tenant) use ($email) {
            
            $user = $this::where('email', $email)->first();
            
            if ($user && Hash::check(Route::getCurrentRequest()->request->get('password'), $user->password))
            {
                $this->tenant = Tenant::current();
                $this->mergeTenantUserForRoute($user, true);
            }
            else if($user)
            {
                array_push($this->domains, $tenant->domain);
            }
        });
    }

    if (empty($this->tenants)) {
        return;
    }

    $this->bindTenantUserToRoute();

    // Bind the proper tenant database,
    // otherwise Passport will not have 
    // the current database connection as
    // it will be set to null.
    $this->ensureTenantIsSet($this->tenant);

    // Return the user for Passport to handle 
    // authorization and token issue.
    return $this->user;
    }

    /**
     * Helper used to join tenant and user data for route binding
     */
    private function mergeTenantUserForRoute($user, $frontOfLine = false)
    {
    $this->tenant = Tenant::current();

    $user->tenantId = $this->tenant->id;
    $user->domain = $this->tenant->domain;
    $user->tenantDatabase = $this->tenant->database;  

    $this->user = $user;

    array_push($this->tenants, $user);

    $bindableTenantData = Collect([
        'domain' => $this->tenant->domain,
        'name' => $this->tenant->name
    ]);

    // The tenant a user is authorized against, 
    // be sure it is the first item in the $domains.
    if($frontOfLine)
    {
        array_unshift($this->domains, $bindableTenantData);
    }
    else
    {
        array_push($this->domains, $bindableTenantData);
    }

    }

    /**
     * Explicitly bind modal to the route.
     */
    private function bindTenantUserToRoute()
    {

    Route::bind('tenant', $this->tenants);

    Route::bind('domains', $this->domains);
    }

    /**
     * Set tenant to current if none is currently defined
     */
    private function ensureTenantIsSet(Tenant $tenant)
    {   
        try{
            if(Tenant::current() === null)
            {
                $tenant->makeCurrent();
            }
        }
        catch(\Error $error)
        {
            throw new Exception("Unable to set tenant.");
        }
    }
}