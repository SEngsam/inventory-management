<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'dashboard' => ['view'],

            'users' => ['view', 'create', 'update', 'delete', 'roles.manage'],

            'settings' => ['view', 'update', 'currencies.manage'],

            'products' => ['view', 'create', 'update', 'delete', 'units.manage', 'categories.manage', 'brands.manage'],

            'purchases' => ['view', 'create', 'update', 'delete', 'show'],

            'sales' => ['view', 'create', 'update', 'delete', 'show'],

            'sale-returns' => ['view', 'create', 'update', 'delete', 'show'],

            'suppliers' => ['view', 'create', 'update', 'delete'],

            'customers' => ['view', 'create', 'update', 'delete'],

            'invoices' => ['view', 'create', 'show'],
        ];

        $all = [];

        foreach ($map as $module => $actions) {
            foreach ($actions as $action) {
                $all[] = "{$module}.{$action}";
            }
        }

        foreach ($all as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Admin role gets everything
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());
    }
}