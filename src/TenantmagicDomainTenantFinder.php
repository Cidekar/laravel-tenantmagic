<?php

namespace Cidekar\Tenantmagic;

use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Concerns\UsesTenantModel;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class TenantmagicDomainTenantFinder extends TenantFinder
{
    use UsesTenantModel;

    public function findForRequest(Request $request):?Tenant
    {
        $host = $request->getHost();
        $tenant = null;

        // First, use framework to find the tenant from the request,
        // if trusted proxies is configured.
        $tenant = $this->getTenantModel()::whereDomain($host)->first();

        // Get the tenant from the headers
        if(is_null($tenant));
        {
            $tenant = $this->getTenantModel()::whereDomain($request->headers->get('X-Forwarded-Host'))->first();
            return $tenant;
        }
        return $tenant;
    }
}
