<?php

namespace Cidekar\Tenantmagic\Tests\Unit\Tasks;

use Cidekar\Tenantmagic\Tests\TestCase;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;
use Cidekar\Tenantmagic\Tasks\TenantmagicPassportTask;
use Cidekar\Tenantmagic\Tests\Stubs\MagicTenant;

class TenantmagicPassportTaskTest extends TestCase
{
    public function setUp($options = null): void
    {
        parent::setUp();

        $this->migrateTenants();
    }

    public function test_it_can_make_tenant_current()
    {
        $tenant = factory(MagicTenant::class)->create(['domain' => 'foo.bar.com']);

        $this->assertNotNull($tenant->makeCurrent());
    }

    public function test_it_can_forget_tenant_current()
    {
        $tenant = factory(MagicTenant::class)->create(['domain' => 'foo.bar.com']);

        $this->assertNotNull($tenant->forgetCurrent());
    }
}
