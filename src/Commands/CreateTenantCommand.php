<?php

namespace Cidekar\Tenantmagic\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant;

use Illuminate\Support\Str;

class CreateTenantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:create tenant
                                { tenant       : The name of the tenant to create a database for }
                                { --domain=     : A custom domain for the tenant }
                                { --d|dryrun   : Dry run database creation }
                                { --m|magic    : Automatically name non-unique tenant }
                                { --id=        : The id of a tenant }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant';

    /**
     * Database character set.
     *
     * @var string
     */
    protected $charset = null;

    /**
     * Database collation.
     */
    protected $collation = null;

    /**
     * Create new tenant database
     *
     * @var string
     */
    protected $query = null;

    /**
     * The name of the tenant database.
     *
     * @var string
     */
    public $tenantName = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->charset = config("database.connections.mysql.charset");
        $this->collation = config("database.connections.mysql.collation");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
        {
            $this->tenantName = $this->option('magic') ? $this->magicallyNameTenant() : $this->argument('tenant');
            if ($this->option('dryrun'))
            {
                $this->dryRun();
            } else
            {
                $this->dryRun()->create();
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

    }

    private function create()
    {

        $this->info("Create; started");

        $tenant = new Tenant();
        $tenant->domain = $this->option('domain') ? $this->option('domain') : $this->tenantName;
        $tenant->database = $this->tenantName;
        $tenant->name = Str::lower($this->argument('tenant'));
        $tenant->save();

        $this->info("Create; tenant addded to landlord");
        $this->info("Create; begin tenant database addition");
        if (DB::statement($this->getCreateTenantDBQuery()) === true) {
            $this->info("Create; tenant database added");
            $this->info("Create; complete");
        } else {
            throw new Exception("Error creating database for tenant.");
        }
        return $this;
    }


    public function dryRun()
    {
        $this->info("Dry run; started");
        if (DB::statement($this->getCreateTenantDBQuery()) === true) {
            $this->info("Dry run; database for tenant");
            if (DB::statement("DROP DATABASE ". $this->tenantName) === true) {
                $this->info("Dry run; dropped database for tenant");
            } else {
                throw new Exception("Dry run; error dropping tenant database");
            }
        }
        $this->info('Dry run; complete.');
        return $this;
    }

    /**
     * SQL statement to create a new tenant in the system
     *
     * @return string
     */
    private function getCreateTenantDBQuery()
    {
        return "CREATE DATABASE IF NOT EXISTS ". $this->tenantName." CHARACTER SET $this->charset COLLATE $this->collation;";
    }

    /**
     * Tenant determination by existence.
     *
     * @var string $checkTenantName The name of a tenant's database
     * @return boolean $exist
     */
    private function hasTenant($checkTenantName = null)
    {
        // Database is present in the system
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
        $tenant = DB::select($query, [$checkTenantName ? $checkTenantName : $this->tenantName]);
        $exist = true;
        if(empty($tenant)){
            $exist = false;
        }

        // Landlord can claim tenant
        if(Tenant::where('database', $checkTenantName ? $checkTenantName : $this->tenantName)->get()->isEmpty())
        {
            $exist = false;
        }
        return $exist;
    }

    /**
     * Create a tenant name from the current tenant.
     *
     * @return string $magicName
     */
    private function magicallyNameTenant()
    {
        $this->info("Magic name; started");
        $magicName = $this->tenantName;
        while($magicName === null){
            $magicName = $this->argument('tenant'). '_' . rand(1, 32767);
            if($this->hasTenant($magicName) === false){
                $this->info("Magic name; created {$magicName}");
                $this->info("Magic name; complete");
                return $magicName;
            }
        }
        return $magicName;
    }
}
