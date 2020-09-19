<?php

namespace Cidekar\Tenantmagic\Tests\Stubs;

use Spatie\Multitenancy\Models\Tenant;

class MagicTenant extends Tenant
{
    // ...
    protected $table = 'tenants';
}
