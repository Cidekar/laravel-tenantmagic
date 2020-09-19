<?php

namespace Cidekar\Tenantmagic\Tests\Stubs;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Cidekar\Tenantmagic\Models\Concerns\UsesPassportModelMagic;

class MagicUser extends Authenticatable
{
    use HasApiTokens,
        UsesTenantConnection,
        UsesPassportModelMagic;

    // ...
    protected $table = "users";
}
