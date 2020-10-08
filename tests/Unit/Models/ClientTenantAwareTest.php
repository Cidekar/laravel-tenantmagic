<?php

namespace Cidekar\Tenantmagic\Tests\Unit\Models;

use Cidekar\Tenantmagic\Tests\TestCase;
use Cidekar\Tenantmagic\Tests\Stubs\MagicTenant;

class TenantmagicClientTenantAwareTest extends TestCase
{

    public function setUp($options = null): void
    {
        parent::setUp();

    }

    public function test_it_uses_tenant_connection()
    {
        $tenant = factory(MagicTenant::class)->create(['domain' => 'foo.bar.com']);

        $tenant->makeCurrent();

        $this->assertNotNull(MagicTenant::current());
    }
}
