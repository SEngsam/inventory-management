<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Tapeli',
            'email' => 'demo@user.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);


        // Permissions
        Permission::create(['name' => 'view_sales']);
        Permission::create(['name' => 'edit_sales']);
        Permission::create(['name' => 'manage_products']);
        Permission::create(['name' => 'view_reports']);

        // Roles
        $admin = Role::create(['name' => 'admin']);
        $sales = Role::create(['name' => 'sales']);
        $manager = Role::create(['name' => 'stock']);
        $viewer = Role::create(['name' => 'viewer']);

        // Permissions to Roles
        $admin->givePermissionTo(Permission::all());
        $sales->givePermissionTo(['view_sales', 'edit_sales']);
        $manager->givePermissionTo(['manage_products']);
        $viewer->givePermissionTo(['view_sales']);

        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            UnitSeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            InvoiceSeeder::class

        ]);
    }
}
