<?php

namespace Cidekar\Tenantmagic\Tests\Feature;

use Cidekar\Tenantmagic\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTeanantCommandTest extends TestCase
{

    public function setUp($options = null): void
    {
        parent::setUp();
    }

    public function teardown():void
    {
      \DB::table('tenants')->delete();

    }

    public function testConsoleCommand(){
        $database = 'tenantmagic';

        $this->artisan("tenants:create")
            ->expectsQuestion('What is tenant\'s name?', $database)
            ->expectsOutput('Create; complete')
            ->assertExitCode(0);

        \DB::statement("DROP DATABASE $database");
    }

    public function testConsoleCommandDryRun()
    {
        $database = 'tenantmagic';

        $this->artisan("tenants:create $database --dryrun")
        ->expectsOutput('Dry run; complete.')
        ->assertExitCode(0);
    }

    public function testConsoleCommandWithDomain()
    {
        $database = 'tenantmagic';

        $this->artisan("tenants:create $database --domain tenantmagic")
        ->expectsOutput('Create; complete')
        ->assertExitCode(0);

        \DB::statement("DROP DATABASE $database");
    }

    public function testConsoleCommandDryRunFailsForExisitingTenant()
    {
        \DB::table('tenants')->insert(
            ['name' => 'tenantmagic', 'domain' => 'tenantmagic', 'database' => 'tenantmagic']
        );

        $this->artisan("tenants:create tenantmagic --dryrun")->assertExitCode(1);

    }

    public function testConsoleCommandMagicNameTenantIfDomainInUse()
    {
        \DB::table('tenants')->insert(
            ['name' => 'tenantmagic', 'domain' => 'tenantmagic', 'database' => 'tenantmagic']
        );

        $this->artisan("tenants:create tenantmagic --dryrun --magic")->assertExitCode(0);
    }


}
