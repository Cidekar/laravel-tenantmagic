<?php

namespace Cidekar\Tenantmagic\Tests\Unit\Models;

use Cidekar\Tenantmagic\Tests\TestCase;
use Cidekar\Tenantmagic\Tests\Stubs\MagicTenant;

class TenantmagicAuthCodeTenantAwareTest extends TestCase
{

    public function setUp(): void
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
