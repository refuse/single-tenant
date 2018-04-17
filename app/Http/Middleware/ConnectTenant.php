<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class ConnectTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $tenant = Tenant::where('name', '=', $request->route()->parameter('tenant'))->firstOrFail();

        Config::set("database.connections.tenant.database", "tenant_{$tenant->id}");
        Config::set("database.connections.tenant.username", "tenant_{$tenant->id}");
        Config::set("database.connections.tenant.password", $tenant->db_password);

        DB::purge();
        DB::reconnect('tenant');

        URL::defaults(['tenant' => $tenant->name]);

        return $next($request);
    }
}
