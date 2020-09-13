<?php

namespace Cidekar\Tenantmagic;

use Illuminate\Support\Facades\Route;

class Tenantmagic
{
    /**
     * Get domains for use with response header.
     *
     * @return String $domains A comma separated list of domains.
     */
    public static function getTenantDomainsFromRoute()
    {
        $tenant = Route::getBindingCallback('tenant');
        return collect($tenant)->pluck('domain')->join(',');
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
     * @param (\Illuminate\Http\Response $response
     * @return \Illuminate\Http\Response
     */
    public static function checkClientScopes(\Illuminate\Http\Request $request)
    {
        if (!config('tenantmagic.allowWildCardScopes')) {
            if ($request->get('scope') === '*') {
                return response('Requested scopes must be unique.', 400);
            }
        }
    }
}
