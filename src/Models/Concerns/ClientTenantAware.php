<?php

namespace Cidekar\Tenantmagic\Models\Concerns;

use Laravel\Passport\Client;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ClientTenantAware extends Client
{
    // Extend internal Passport model and make it tenant aware.

    // 1. Model call when migrating
    use UsesTenantConnection;
}
