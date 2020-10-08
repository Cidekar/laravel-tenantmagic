<?php

namespace Cidekar\Tenantmagic\Tests\Feature;

use Cidekar\Tenantmagic\Tests\TestCase;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;

class TenantmagicPassportTest extends TestCase
{

    public function setUp($options = null): void
    {
        parent::setUp(['withoutMiddleware' => true]);

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
            'scopes' => '*',
        ]);

        $response
            ->assertStatus(200)
            ->assertSee('token_type')
            ->assertSee('access_token');
    }

    public function test_it_can_provide_domain_and_get_password_grant_token()
    {
        $route = config('tenantmagic.route.prefix') . config('tenantmagic.route.name', 'tenantmagic');
        $response = $this->post($route, [
            'client_id' => $this->passport->id,
            'client_secret' => $this->passport->secret,
            'username' => 'tenant@magic.com',
            'password' => 'password',
            'grant_type' => 'password',
            'scopes' => '*',
        ], [
            'X-Forwarded-Host' => $this->tenant->domain
        ]);

        $response
            ->assertStatus(200)
            ->assertSee('token_type')
            ->assertSee('access_token');
    }
}
