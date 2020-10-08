<?php

namespace Cidekar\Tenantmagic\Tests\Feature;

use Cidekar\Tenantmagic\Tenantmagic;
use Cidekar\Tenantmagic\Tests\TestCase;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;
use \Illuminate\Http\Response;

class TenantmagicDomainHeaderTest extends TestCase
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

    public function test_it_can_parse_domains_from_header()
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

        $this->assertIsArray(Tenantmagic::parseTenantDomainsFromHeader($response));
    }

    public function test_it_can_parse_domains_from_header_returns_null()
    {
        $response = new Response();

        $response->headers->set(config('tenantmagic.header'), '');

        $this->assertNull(Tenantmagic::parseTenantDomainsFromHeader($response));
    }
}
