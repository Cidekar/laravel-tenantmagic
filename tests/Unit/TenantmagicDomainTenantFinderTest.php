<?php

namespace Cidekar\Tenantmagic\Tests\Unit;

use Cidekar\Tenantmagic\Tests\TestCase;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;
use Illuminate\Http\Request;
use Cidekar\Tenantmagic\TenantmagicDomainTenantFinder;
use Cidekar\Tenantmagic\Tests\Stubs\MagicTenant;

class TenantmagicDomainTenantFinderTest extends TestCase
{

    private TenantmagicDomainTenantFinder $TenantmagicDomainTenantFinder;

    public function setUp($options = null): void
    {
        parent::setUp();

        $this->migrateTenants();

        $this->createTenantUsers();

        config()->set('auth.guards.api.provider', 'users');
        config()->set('auth.providers.users.model', MagicUser::class);

        config()->set('multitenancy.tenant_finder', TenantmagicDomainTenantFinder::class);
        config()->set('multitenancy.switch_tenant_tasks', [SwitchTenantDatabaseTask::class, TenantmagicPassportTask::class]);

        $this->TenantmagicDomainTenantFinder = new TenantmagicDomainTenantFinder();


        $this->passportSetup();
    }

    public function test_can_find_tenant_from_request()
    {
        $tenant = factory(MagicTenant::class)->create(['domain' => 'foo.bar.com']);

        \Illuminate\Support\Facades\Route::get('/user' , function () {
            return auth()->user();
        })->middleware(['tenant']);

        $request = Request::create('/user', 'GET', [], [], []);
        $request->headers->set('X-Forwarded-Host', 'foo.bar.com');

        $this->assertEquals($this->TenantmagicDomainTenantFinder->findForRequest($request)->id, $tenant->id);
    }

    public function test_it_is_null_if_tenant_not_found()
    {
        $tenant = factory(MagicTenant::class)->create(['domain' => 'foo.bar.com']);

        \Illuminate\Support\Facades\Route::get('/user', function () {
            return auth()->user();
        })->middleware(['tenant']);

        $request = Request::create('/user', 'GET', [], [], []);
        $request->headers->set('X-Forwarded-Host', 'foo.baz.com');

        $this->assertNull($this->TenantmagicDomainTenantFinder->findForRequest($request));
    }

}
