<?php

use App\Enums\StockMovementType;
use App\Livewire\Products\ProductForm;
use App\Models\Category;
use App\Models\Product;
use Livewire\Livewire;

describe('product form', function () {
    it('records the opening stock of a new product', function () {
        Livewire::test(ProductForm::class)
            ->set('name', 'Widget')
            ->set('sku', 'W-1')
            ->set('price', 9.99)
            ->set('category_id', Category::factory()->create()->id)
            ->set('stock_quantity', 12)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('products.list'));

        $product = Product::where('sku', 'W-1')->sole();

        expect($product)->toHaveStock(12)
            ->and($product->stockMovements()->sole()->type)->toBe(StockMovementType::Opening);
    });

    it('records a changed stock level as an adjustment', function () {
        $product = Product::factory()->withStock(10)->create();

        Livewire::test(ProductForm::class, ['product' => $product->id])
            ->assertSet('stock_quantity', 10)
            ->set('stock_quantity', 8)
            ->call('save')
            ->assertHasNoErrors();

        expect($product)->toHaveStock(8)
            ->and($product->stockMovements()->latest('id')->first())
            ->type->toBe(StockMovementType::Adjustment)
            ->quantity->toBe(-2);
    });

    it('can edit products that were created without a type', function () {
        $product = Product::factory()->create(['type' => null]);

        Livewire::test(ProductForm::class, ['product' => $product->id])
            ->assertSet('type', 'standard')
            ->call('save')
            ->assertHasNoErrors();
    });

    it('validates its fields', function (string $field, mixed $value, string $rule) {
        Livewire::test(ProductForm::class)
            ->set($field, $value)
            ->call('save')
            ->assertHasErrors([$field => $rule]);
    })->with([
        'missing name' => ['name', '', 'required'],
        'missing category' => ['category_id', null, 'required'],
        'negative price' => ['price', -1, 'min'],
        'negative stock' => ['stock_quantity', -5, 'min'],
        'unknown type' => ['type', 'digital', 'in'],
    ]);

    it('requires a unique SKU', function () {
        $existing = Product::factory()->create();

        Livewire::test(ProductForm::class)
            ->set('sku', $existing->sku)
            ->call('save')
            ->assertHasErrors(['sku' => 'unique']);
    });
});

it('does not allow stock to be mass assigned', function () {
    $product = Product::factory()->withStock(10)->create();

    $product->update(['stock_quantity' => 999]);

    expect($product)->toHaveStock(10);
});
