<?php

namespace Cidekar\Tenantmagic;

use \Exception;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Exceptions\MissingScopeException;

class Tenantmagic
{
    /**
     * Get domains for use with response header.
     *
     * @return String $domains A comma separated list of domains.
     */
    public static function getTenantDomainsFromRoute()
    {
        try
        {
            $domains = Route::getBindingCallback('domains');            

            if(!empty($domains))
            {
               return join(',',$domains);
            }

            throw new Exception('Domain not found in route binding.');
        }
        catch(\Error $e)
        {
            throw new Exception('Unable to obtain domain from route binding.');
        }
        
    }

    /**
     * Parse domains from response header.
     *
     * @param \Illuminate\Http\Response $response
     * @return Array $domains An array of domains
     */
    public static function parseTenantDomainsFromHeader($response)
    {
        $rawHeader = $response->headers->get(config('tenantmagic.header'));

        if(empty($rawHeader))
        {
            return null;
        }
        return explode(',', $rawHeader);
    }

    /**
     * Return a response with tenant's domain attached to the header.
     *
     * @param (\Illuminate\Http\Response $response
     * @return \Illuminate\Http\Response
     */
    public static function issueResponse(\Illuminate\Http\Response $response)
    {
        return $response->withHeaders([
            config('tenantmagic.header') => self::getTenantDomainsFromRoute(),
        ]);
    }

    /**
     *  Check grant client requests a specific set of permission when authorizing
     *
     * @param \Illuminate\Http\Response $response
     * @return \Illuminate\Http\Response
     * @throws \Laravel\Passport\Exceptions\MissingScopeException
     */
    public static function checkClientScopes(\Illuminate\Http\Request $request)
    {
        if (config('tenantmagic.allowWildCardScopes') === false) {
            if ($request->get('scopes') === '*') {
                throw new MissingScopeException();
            }
        }
    }
}
