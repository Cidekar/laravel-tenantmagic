<?php

namespace Cidekar\Tenantmagic\Tests;

use Cidekar\Tenantmagic\Tests\TestCase;

class TenantmagicRouteTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp(['withoutMiddleware'=> true]);

        $this->migrateTenants();

        $this->createTenantUsers();

        config()->set('auth.guards.api.provider', 'users');
        config()->set('auth.providers.users.model', MagicUser::class);

        $this->passportSetup();
    }

    public function test_it_can_get_default_route()
    {
        $route = config('tenantmagic.route.prefix') . config('tenantmagic.route.name', 'tenantmagic');

        $response = $this->post($route);

        $response->assertStatus(400);
    }
}
