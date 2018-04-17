## Laravel - Single Tenancy

The demo application showing single-tenancy approach using Laravel framework.

Adjusted files worth seeing:
- [config/database.php](https://github.com/refuse/single-tenant/blob/master/config/database.php) 
- [app/Listeners/UserRegistered.php](https://github.com/refuse/single-tenant/blob/master/app/Listeners/UserRegistered.php)
- [app/Http/Middleware/ConnectTenant.php](https://github.com/refuse/single-tenant/blob/master/app/Http/Middleware/ConnectTenant.php)
- [routes/web.php](https://github.com/refuse/single-tenant/blob/master/routes/web.php)
 