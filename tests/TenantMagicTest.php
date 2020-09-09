<?php

namespace Cidekar\Tenantmagic\Tests;

use Cidekar\Tenantmagic\Tests\TestCase;

class TenantMagicTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->migrateTenants();

        $this->createTenantUsers();

    }

    public function test_it_can_get_user_from_tenant()
    {
       // ...
    }
}
