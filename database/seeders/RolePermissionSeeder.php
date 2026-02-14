<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $permissions = [
            'view_sales', 'edit_sales', 'manage_products', 'view_reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $sales = Role::firstOrCreate(['name' => 'sales']);
        $admin->givePermissionTo(Permission::all());
        $sales->givePermissionTo(['view_sales', 'edit_sales']);
    }
}
