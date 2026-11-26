<?php

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Livewire\Sales\ReturnForm;
use App\Livewire\Sales\ReturnList;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Services\SaleReturnService;
use App\Services\SaleService;
use App\Services\StockService;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

function completedSale(Product $product, int $quantity, string $status = Sale::STATUS_COMPLETED, float $price = 5): Sale
{
    return app(SaleService::class)->save([
        'reference_no' => 'SL-'.uniqid(),
        'sale_date' => now()->toDateString(),
        'status' => $status,
    ], [['product_id' => $product->id, 'quantity' => $quantity, 'unit_price' => $price]]);
}

function recordReturn(Sale $sale, array $quantities): SaleReturn
{
    return app(SaleReturnService::class)->create($sale, [
        'reference_no' => 'RET-'.uniqid(),
        'return_date' => now()->toDateString(),
    ], $quantities);
}

it('restores stock with a return movement', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = completedSale($product, 4);

    $return = recordReturn($sale, [$product->id => 3]);

    expect($product)->toHaveStock(9);
    expect($product->stockMovements()->latest('id')->first())
        ->type->toBe(StockMovementType::Return)
        ->quantity->toBe(3)
        ->reference_type->toBe('sale_return')
        ->reference_id->toBe($return->id);
});

it('takes unit prices from the sale', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = completedSale($product, 4, price: 12.5);

    $return = recordReturn($sale, [$product->id => 2]);

    expect((float) $return->items->first()->unit_price)->toBe(12.5)
        ->and((float) $return->total)->toBe(25.0);
});

it('limits cumulative returns to the quantity sold', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = completedSale($product, 4);
    recordReturn($sale, [$product->id => 3]);

    expect(fn () => recordReturn($sale, [$product->id => 2]))->toThrow(ValidationException::class);

    expect($product)->toHaveStock(9)
        ->and(app(SaleReturnService::class)->returnableQuantities($sale))->toBe([$product->id => 1]);
});

it('rejects products that were not on the sale', function () {
    $sold = Product::factory()->withStock(10)->create();
    $other = Product::factory()->withStock(10)->create();

    recordReturn(completedSale($sold, 2), [$other->id => 1]);
})->throws(ValidationException::class);

it('rejects returns for pending sales', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = completedSale($product, 2, Sale::STATUS_PENDING);

    expect(fn () => recordReturn($sale, [$product->id => 1]))
        ->toThrow(ValidationException::class, __('messages.return.sale_not_completed'));
});

it('ignores zero quantities but requires at least one item', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = completedSale($product, 2);

    expect(fn () => recordReturn($sale, [$product->id => 0]))
        ->toThrow(ValidationException::class, __('messages.items.required'));
});

it('takes the stock out again when a return is deleted', function () {
    $product = Product::factory()->withStock(10)->create();
    $return = recordReturn(completedSale($product, 4), [$product->id => 3]);

    app(SaleReturnService::class)->delete($return);

    expect($product)->toHaveStock(6);
    $this->assertModelMissing($return);
});

it('refuses to delete a return whose goods were sold again', function () {
    $product = Product::factory()->withStock(4)->create();
    $return = recordReturn(completedSale($product, 4), [$product->id => 2]);
    app(StockService::class)->setLevel($product, 0);

    app(SaleReturnService::class)->delete($return);
})->throws(InsufficientStockException::class);

describe('return form', function () {
    it('offers only completed sales and the quantity still returnable', function () {
        $product = Product::factory()->withStock(10)->create();
        $completed = completedSale($product, 4);
        $pending = completedSale($product, 1, Sale::STATUS_PENDING);
        recordReturn($completed, [$product->id => 1]);

        $component = Livewire::test(ReturnForm::class);

        expect($component->get('sales')->contains($completed))->toBeTrue()
            ->and($component->get('sales')->contains($pending))->toBeFalse();

        $component->set('sale_id', $completed->id)
            ->assertSet('items.0.max_quantity', 3)
            ->assertSet('items.0.quantity', 0)
            ->set('items.0.quantity', 2)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('sale-returns.index'));

        expect($product)->toHaveStock(9);
    });

    it('rejects returning more than was sold', function () {
        $product = Product::factory()->withStock(10)->create();
        $sale = completedSale($product, 2);

        Livewire::test(ReturnForm::class)
            ->set('sale_id', $sale->id)
            ->set('items.0.quantity', 3)
            ->call('save')
            ->assertHasErrors('items');

        expect($product)->toHaveStock(8);
    });

    it('requires a sale and a date', function () {
        Livewire::test(ReturnForm::class)
            ->set('return_date', '')
            ->call('save')
            ->assertHasErrors(['sale_id' => 'required', 'return_date' => 'required']);
    });
});

it('shows why a delete was refused on the return list', function () {
    $product = Product::factory()->withStock(4)->create();
    $return = recordReturn(completedSale($product, 4), [$product->id => 2]);
    app(StockService::class)->setLevel($product, 0);

    Livewire::test(ReturnList::class)
        ->call('confirmDelete', $return->id)
        ->assertSee(e(__('messages.stock.insufficient_detail', [
            'product' => $product->name,
            'available' => 0,
            'requested' => 2,
        ])), false);

    $this->assertModelExists($return);
});
