<?php

namespace Cidekar\Tenantmagic\Models\Concerns;

use Laravel\Passport\PersonalAccessClient;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PersonalAccessClientTenantAware extends PersonalAccessClient
{
    // Extend internal Passport model and make it tenant aware.
    use UsesTenantConnection;

}
