<?php

namespace Cidekar\Tenantmagic;

use Illuminate\Support\Facades\Facade;

class TenantmagicFadade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Tenantmagic';
    }
}
