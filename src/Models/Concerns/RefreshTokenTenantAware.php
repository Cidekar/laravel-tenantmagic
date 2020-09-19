<?php

namespace Cidekar\Tenantmagic\Models\Concerns;

use Laravel\Passport\RefreshToken;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Multitenancy\Models\Tenant;

class RefreshTokenTenantAware extends RefreshToken
{
    // Extend internal Passport model and make it tenant aware
    // The model is call before / when creating a oauth token for
    // password client.
    use UsesTenantConnection;

}
