<?php

namespace Cidekar\Tenantmagic\Tasks;

use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use Laravel\Passport\Passport;
use Cidekar\Tenantmagic\Models\Concerns\AuthCodeTenantAware;
use Cidekar\Tenantmagic\Models\Concerns\TokenTenantAware;
use Cidekar\Tenantmagic\Models\Concerns\RefreshTokenTenantAware;

class TenantmagicPassportTask implements SwitchTenantTask
{
    public function makeCurrent(Tenant $tenant): void
    {
        $this->setTenantPassport($tenant);
    }

    public function forgetCurrent(): void
    {
        $this->setTenantPassport();
    }

    protected function setTenantPassport(?Tenant $tenant = null): void
    {
        Passport::useAuthCodeModel(AuthCodeTenantAware::class);
        Passport::useRefreshTokenModel(RefreshTokenTenantAware::class);
        Passport::useTokenModel(TokenTenantAware::class);

        // Makes Passport tenant aware before grant client can be picked from the
        // Landlord database. You may uncomment however doing so will cause the
        // underlying OAuth server to return a 401 - you will need to augment
        // the configuring to meet your specific needs.
        // Passport::useClientModel(ClientTenantAware::class);
    }
}
