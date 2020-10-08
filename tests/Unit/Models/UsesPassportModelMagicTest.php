<?php

namespace Cidekar\Tenantmagic\Tests\Unit\Models;

use Cidekar\Tenantmagic\Tests\TestCase;
use Cidekar\Tenantmagic\Tests\Stubs\MagicUser;

class UsesPassportModelMagicTest extends TestCase
{

    public function setUp($options = null): void
    {
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

    public function test_it_can_find_for_passport()
    {
        $model = new MagicUser();
        $this->assertNotNull($model->findForPassport('tenant@magic.com'));
    }
}
