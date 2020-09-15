<?php

namespace Cidekar\Tenantmagic\Models\Concerns;

use Laravel\Passport\AuthCode;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class AuthCodeTenantAware extends AuthCode
{
    // Extend internal Passport model and make it tenant aware.
    use UsesTenantConnection;

}
