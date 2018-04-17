<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CreateDatabse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:tenants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Tenants';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tenants = Tenant::all();

        try {
            $this->migrateTenants($tenants);
        } catch (\Throwable $exception) {
            /*
             * @TODO Inform admin
             */
        }
    }

    protected function migrateTenants(Collection $tenants)
    {
        $migrated = [];

        foreach ($tenants as $tenant) {
            try {
                $this->info("Migrating tenant {$tenant->id}");
                $this->reconnect($tenant);

                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/bookkeeping',
                    '--force' => true,
                ]);

                $migrated[] = $tenant;
                $this->info("done");
            } catch (\Throwable $exception) {
                $this->rollbackTenants($migrated);

                break;
            }
        }
    }

    protected function rollbackTenants(array $tenants)
    {
        foreach ($tenants as $tenant) {
            $this->reconnect($tenant);

            Artisan::call('migrate:rollback', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/bookkeeping',
                '--force' => true,
            ]);
        }
    }

    protected function reconnect(Tenant $tenant)
    {
        Config::set("database.connections.tenant.database", "tenant_{$tenant->id}");
        Config::set("database.connections.tenant.username", "tenant_{$tenant->id}");
        Config::set("database.connections.tenant.password", $tenant->db_password);

        DB::purge();
        DB::reconnect('tenant');
    }
}
