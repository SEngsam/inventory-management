<?php

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests boot the application and run each test inside a database
| transaction that is rolled back afterwards. Unit tests boot the
| application too (for config and translations) but never touch the
| database.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

/**
 * Assert a product's stock and that its ledger adds up to the same number.
 */
expect()->extend('toHaveStock', function (int $expected) {
    $product = $this->value instanceof Product ? $this->value->fresh() : Product::findOrFail($this->value);

    expect($product->stock_quantity)->toBe($expected, "stock_quantity of product {$product->id}");
    expect((int) StockMovement::where('product_id', $product->id)->sum('quantity'))
        ->toBe($expected, "ledger total of product {$product->id}");

    return $this;
});

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Sign in as a user holding the admin role with every route permission.
 */
function actingAsAdmin(): User
{
    test()->artisan('permissions:sync');

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    test()->actingAs($admin);

    return $admin;
}
