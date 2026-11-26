<?php

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;

beforeEach(function () {
    $this->service = app(ProductService::class);
    $this->category = Category::factory()->create();
});

it('creates a product without stock', function () {
    $product = $this->service->create('iPhone', 799, $this->category->id, 'SKU-IPHONE');

    expect($product)
        ->toBeInstanceOf(Product::class)
        ->name->toBe('iPhone')
        ->and((float) $product->price)->toBe(799.0)
        ->and($product)->toHaveStock(0);
});

it('requires a name', function () {
    $this->service->create('', 100, $this->category->id, 'SKU-EMPTY');
})->throws(InvalidArgumentException::class, 'Product name is required');

it('requires a price above 1', function () {
    $this->service->create('Headphones', 1, $this->category->id, 'SKU-CHEAP');
})->throws(InvalidArgumentException::class, 'Product price must be greater than 1');

it('requires a unique name', function () {
    Product::factory()->create(['name' => 'Speaker']);

    $this->service->create('Speaker', 55, $this->category->id, 'SKU-SPEAKER');
})->throws(InvalidArgumentException::class, 'Product name must be unique');
