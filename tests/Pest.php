<?php

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)
    ->in('Unit');

pest()->extend(TestCase::class)
    ->use(DatabaseTruncation::class)
    ->in('Concurrency');

expect()->extend('toHaveStock', function (int $expected) {
    $product = $this->value instanceof Product ? $this->value->fresh() : Product::findOrFail($this->value);

    expect($product->stock_quantity)->toBe($expected, "stock_quantity of product {$product->id}");
    expect((int) StockMovement::where('product_id', $product->id)->sum('quantity'))
        ->toBe($expected, "ledger total of product {$product->id}");

    return $this;
});

function actingAsAdmin(): User
{
    test()->artisan('permissions:sync');

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    test()->actingAs($admin);

    return $admin;
}
