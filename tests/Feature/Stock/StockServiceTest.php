<?php

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->stock = app(StockService::class);
});

it('records added stock with the acting user and resulting balance', function () {
    $product = Product::factory()->withStock(10)->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    $movement = $this->stock->add(StockMovementType::Purchase, [$product->id => 5])->sole();

    expect($movement)
        ->type->toBe(StockMovementType::Purchase)
        ->quantity->toBe(5)
        ->balance_after->toBe(15)
        ->user_id->toBe($user->id)
        ->and($product)->toHaveStock(15);
});

it('stores removed stock as a negative quantity linked to its document', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = Sale::factory()->create();

    $movement = $this->stock->remove(StockMovementType::Sale, [$product->id => 3], $sale)->sole();

    expect($movement)
        ->quantity->toBe(-3)
        ->reference_type->toBe('sale')
        ->reference_id->toBe($sale->id)
        ->and($movement->reference->is($sale))->toBeTrue()
        ->and($product)->toHaveStock(7);
});

it('rejects the whole change when any product is short', function () {
    $plenty = Product::factory()->withStock(10)->create();
    $scarce = Product::factory()->withStock(2)->create();
    $movements = StockMovement::count();

    try {
        $this->stock->remove(StockMovementType::Sale, [$plenty->id => 5, $scarce->id => 3]);
        $this->fail('Expected an InsufficientStockException.');
    } catch (InsufficientStockException $e) {
        expect($e->shortages)->toBe([[
            'product_id' => $scarce->id,
            'product' => $scarce->name,
            'available' => 2,
            'requested' => 3,
        ]])->and($e->errors())->toHaveKey('items');
    }

    expect($plenty)->toHaveStock(10)
        ->and($scarce)->toHaveStock(2)
        ->and(StockMovement::count())->toBe($movements);
});

it('reports insufficient stock as a validation error', function () {
    $product = Product::factory()->withStock(1)->create();

    $this->stock->remove(StockMovementType::Sale, [$product->id => 2]);
})->throws(ValidationException::class);

it('requires positive quantities when adding or removing', function (string $method) {
    $product = Product::factory()->withStock(5)->create();

    $this->stock->{$method}(StockMovementType::Adjustment, [$product->id => 0]);
})->with(['add', 'remove'])->throws(InvalidArgumentException::class);

it('rejects unknown products', function () {
    $this->stock->add(StockMovementType::Purchase, [999 => 1]);
})->throws(ModelNotFoundException::class);

it('reverses a document with the same movement type and opposite sign', function () {
    $a = Product::factory()->withStock(10)->create();
    $b = Product::factory()->withStock(10)->create();
    $sale = Sale::factory()->create();
    $this->stock->remove(StockMovementType::Sale, [$a->id => 4, $b->id => 1], $sale);

    $reversal = $this->stock->reverse($sale, 'Sale deleted');

    expect($reversal)->toHaveCount(2)
        ->each(fn ($movement) => $movement->type->toBe(StockMovementType::Sale)->quantity->toBeGreaterThan(0))
        ->and($this->stock->netFor($sale))->toBe([])
        ->and($a)->toHaveStock(10)
        ->and($b)->toHaveStock(10);

    expect($this->stock->reverse($sale))->toHaveCount(0);
});

it('refuses to reverse added stock that has since been used', function () {
    $product = Product::factory()->create();
    $purchase = Purchase::factory()->create();
    $this->stock->add(StockMovementType::Purchase, [$product->id => 5], $purchase);
    $this->stock->remove(StockMovementType::Sale, [$product->id => 4]);

    $this->stock->reverse($purchase);
})->throws(InsufficientStockException::class);

it('records a counted stock level as an adjustment of the difference', function () {
    $product = Product::factory()->withStock(10)->create();

    expect($this->stock->setLevel($product, 7, 'Stock count'))
        ->type->toBe(StockMovementType::Adjustment)
        ->quantity->toBe(-3)
        ->note->toBe('Stock count')
        ->and($this->stock->setLevel($product, 7))->toBeNull()
        ->and($product)->toHaveStock(7);
});

it('rejects negative levels and opening balances', function (string $method) {
    $this->stock->{$method}(Product::factory()->create(), -1);
})->with(['setLevel', 'opening'])->throws(InvalidArgumentException::class);

it('records nothing for an opening balance of zero', function () {
    expect($this->stock->opening(Product::factory()->create(), 0))->toBeNull()
        ->and(StockMovement::count())->toBe(0);
});
