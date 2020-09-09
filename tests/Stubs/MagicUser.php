<?php

namespace Cidekar\Tenantmagic\Tests\Stubs;

use Illuminate\Foundation\Auth\User as Authenticatable;

class MagicUser extends Authenticatable
{
    // ...
    protected $table = "users";
}
