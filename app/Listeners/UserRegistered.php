<?php

namespace App\Listeners;

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class UserRegistered
{
    public function created(Tenant $tenant)
    {
        Config::set("database.connections.tenant.database", "tenant_{$tenant->id}");
        Config::set("database.connections.tenant.username", "tenant_{$tenant->id}");
        Config::set("database.connections.tenant.password", $tenant->db_password);

        try {
            $this->dropDB("tenant_{$tenant->id}");
            $this->createDB("tenant_{$tenant->id}", $tenant->db_password);

            DB::reconnect('tenant');

            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/bookkeeping',
                '--force' => true,
            ]);

            \App\Models\Bookkeeping\User::create([
                'name' => $tenant->name,
                'email' => $tenant->email,
                'password' => $tenant->password,
            ]);
        } catch (\Throwable $exception) {
            $this->dropDB("tenant_{$tenant->id}");

            $tenant->delete();

            throw $exception;
        }
    }

    protected function createDB($name, $password)
    {
        DB::connection('creator')
            ->statement("CREATE DATABASE IF NOT EXISTS {$name};");

        DB::connection('creator')
            ->statement("CREATE USER IF NOT EXISTS '{$name}'@'127.0.0.1' IDENTIFIED BY '{$password}';");

        DB::connection('creator')
            ->statement("GRANT ALL ON `{$name}`.* TO '{$name}'@'127.0.0.1';");

        DB::connection('creator')
            ->statement("FLUSH PRIVILEGES;");
    }

    protected function dropDB($name)
    {
        DB::connection('root')
            ->statement("DROP DATABASE IF EXISTS {$name};");

        DB::connection('root')
            ->statement("DROP USER IF EXISTS '{$name}'@'127.0.0.1';");

        DB::connection('root')
            ->statement("FLUSH PRIVILEGES;");
    }
}
