<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'invoices.view',
            'invoices.create',
            'invoices.show',
            'invoices.delete',
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
            'sale-returns.view',
            'sale-returns.create',
            'sale-returns.update',
            'sale-returns.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}