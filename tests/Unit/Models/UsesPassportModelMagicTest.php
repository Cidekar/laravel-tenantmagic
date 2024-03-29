<?php

namespace Cidekar\Tenantmagic\Tests\Unit\Models;

use Cidekar\Tenantmagic\Tests\TestCase;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;
use Illuminate\Support\Facades\Route as RouteFacade;

class UsesPassportModelMagicTest extends TestCase
{

    public function setUp($options = null): void
    {
        parent::setUp(['withoutMiddleware' => true]);

        parent::setUp();

        $this->migrateTenants();

        $this->createTenantUsers();

        config()->set('auth.guards.api.provider', 'users');
        config()->set('auth.providers.users.model', MagicUser::class);

        $this->passportSetup();
    }

    public function test_it_can_validate_for_passport_password_grant()
    {
        $model = new MagicUser();

        $model->password = \Hash::make('foo');

        $this->assertTrue($model->validateForPassportPasswordGrant('foo'));
    }

    public function test_it_can_find_user_for_passport()
    {
        $route = config('tenantmagic.route.prefix') . config('tenantmagic.route.name', 'tenantmagic');
       
        $response = $this->post($route, [
            'client_id' => $this->passport->id,
            'client_secret' => $this->passport->secret,
            'username' => 'tenant@magic.com',
            'password' => '123',
            'grant_type' => 'password',
            'scopes' => 'user project',
        ]);
        
        $response->assertOk();

        $decodedResponse = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('token_type', $decodedResponse);
        $this->assertArrayHasKey('expires_in', $decodedResponse);
        $this->assertArrayHasKey('access_token', $decodedResponse);
        $this->assertSame('Bearer', $decodedResponse['token_type']);
        $expiresInSeconds = 31536000;
        $this->assertEqualsWithDelta($expiresInSeconds, $decodedResponse['expires_in'], 5); 
    }

    public function test_it_can_get_token_from_passport() 
    {    
        $model = new MagicUser();

        $request = request();
        $request->request->add(['password' => '123']); 

        RouteFacade::shouldReceive('getCurrentRequest')
            ->andReturn($request)
            ->shouldReceive('bind')
            ->andReturn(true);

        $this->assertNotNull($model->findForPassport('tenant@magic.com'));
    }
}
