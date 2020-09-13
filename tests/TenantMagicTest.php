<?php

namespace Cidekar\Tenantmagic\Tests;

use Cidekar\Tenantmagic\Tests\TestCase;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;

class TenantMagicTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->migrateTenants();

        $this->createTenantUsers();

        config()->set('auth.guards.api.provider', 'users');
        config()->set('auth.providers.users.model', MagicUser::class);

        $this->passportSetup();
    }

    public function test_it_can_get_password_grant_token()
    {
        $route = config('tenantmagic.route.prefix') . config('tenantmagic.route.name', 'tenantmagic');
        $response = $this->post($route, [
            'client_id' => $this->passport->id,
            'client_secret' => $this->passport->secret,
            'username' => 'tenant@magic.com',
            'password' => 'password',
            'grant_type' => 'password',
            'scopes' => 'user project',
        ]);

        $response
            ->assertSee('token_type')
            ->assertSee('access_token');
    }

    public function test_it_can_get_domain_header()
    {
        $route = config('tenantmagic.route.prefix') . config('tenantmagic.route.name', 'tenantmagic');
        $response = $this->post($route, [
            'client_id' => $this->passport->id,
            'client_secret' => $this->passport->secret,
            'username' => 'tenant@magic.com',
            'password' => 'password',
            'grant_type' => 'password',
            'scopes' => 'user project',
        ]);

        $this->assertNotNull($response->headers->get(config('tenantmagic.header')));
    }

    // test_it_can_get_x_header_domain
    // test_it_can_get_passport_token
    // test_it_can
}
