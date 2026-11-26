<?php

use App\Enums\StockMovementType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;

it('creates a valid :dataset record', function (string $model) {
    $record = $model::factory()->create();

    expect($record->exists)->toBeTrue();
    $this->assertModelExists($record);
})->with([
    'brand' => Brand::class,
    'category' => Category::class,
    'currency' => Currency::class,
    'customer' => Customer::class,
    'invoice' => Invoice::class,
    'invoice item' => InvoiceItem::class,
    'product' => Product::class,
    'purchase' => Purchase::class,
    'purchase item' => PurchaseItem::class,
    'sale' => Sale::class,
    'sale item' => SaleItem::class,
    'sale return' => SaleReturn::class,
    'sale return item' => SaleReturnItem::class,
    'stock movement' => StockMovement::class,
    'supplier' => Supplier::class,
    'unit' => Unit::class,
    'user' => User::class,
]);

test('products start without stock', function () {
    $product = Product::factory()->create();

    expect($product->stock_quantity)->toBe(0)
        ->and($product->stockMovements()->count())->toBe(0);
});

test('withStock records an opening movement', function () {
    $product = Product::factory()->withStock(25)->create();

    expect($product->stock_quantity)->toBe(25)
        ->and($product->stockMovements()->sole())
        ->type->toBe(StockMovementType::Opening)
        ->quantity->toBe(25);
});
