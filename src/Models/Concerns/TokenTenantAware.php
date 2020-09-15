<?php

namespace Cidekar\Tenantmagic\Models\Concerns;

use Laravel\Passport\Token;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class TokenTenantAware extends Token
{
    // Extend internal Passport model and make it tenant aware
    // The model is call before / when creating a oauth token for
    // password client.
    use UsesTenantConnection;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_access_tokens';
}
