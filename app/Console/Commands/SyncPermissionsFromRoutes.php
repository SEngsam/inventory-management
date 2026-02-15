<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncPermissionsFromRoutes extends Command
{
    protected $signature = 'permissions:sync';
    protected $description = 'Sync permissions from named routes';

    public function handle()
    {
        $routes = Route::getRoutes();
        $created = 0;

        foreach ($routes as $route) {

            $name = $route->getName();

            if (!$name) {
                continue;
            }

            if (
                str_starts_with($name, 'debugbar') ||
                str_starts_with($name, 'livewire') ||
                str_starts_with($name, 'verification') ||
                str_starts_with($name, 'password') ||
                in_array($name, ['login', 'logout', 'register'])
            ) {
                continue;
            }

            if (!Permission::where('name', $name)->exists()) {
                Permission::create(['name' => $name]);
                $created++;
                $this->info("Created permission: $name");
            }
        }

        $this->info("Done. Created $created new permissions.");

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $this->info("Admin role synced with all permissions.");
    }
}
