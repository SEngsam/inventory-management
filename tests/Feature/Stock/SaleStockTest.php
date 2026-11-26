<?php

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Livewire\Sales\SaleForm;
use App\Livewire\Sales\SaleList;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Services\SaleReturnService;
use App\Services\SaleService;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

function saleData(string $status = Sale::STATUS_COMPLETED, array $overrides = []): array
{
    return [
        'reference_no' => 'SL-'.uniqid(),
        'sale_date' => now()->toDateString(),
        'status' => $status,
        ...$overrides,
    ];
}

function saleLine(Product $product, int $quantity, float $price = 5): array
{
    return ['product_id' => $product->id, 'quantity' => $quantity, 'unit_price' => $price];
}

function returnOne(Sale $sale, Product $product): void
{
    app(SaleReturnService::class)->create($sale, [
        'reference_no' => 'RET-'.uniqid(),
        'return_date' => now()->toDateString(),
    ], [$product->id => 1]);
}

beforeEach(function () {
    $this->sales = app(SaleService::class);
});

it('deducts stock for a completed sale and references the sale', function () {
    $product = Product::factory()->withStock(10)->create();

    $sale = $this->sales->save(saleData(), [saleLine($product, 3)]);

    expect($product)->toHaveStock(7);
    expect(StockMovement::where('reference_type', 'sale')->sole())
        ->type->toBe(StockMovementType::Sale)
        ->quantity->toBe(-3)
        ->reference_id->toBe($sale->id);
});

it('combines repeated lines for the same product', function () {
    $product = Product::factory()->withStock(10)->create();

    $this->sales->save(saleData(), [saleLine($product, 2), saleLine($product, 3)]);

    expect($product)->toHaveStock(5);
});

it('does not touch stock for pending sales', function () {
    $product = Product::factory()->withStock(10)->create();

    $this->sales->save(saleData(Sale::STATUS_PENDING), [saleLine($product, 3)]);

    expect($product)->toHaveStock(10);
});

it('rejects overselling and saves nothing', function () {
    $product = Product::factory()->withStock(2)->create();

    expect(fn () => $this->sales->save(saleData(), [saleLine($product, 3)]))
        ->toThrow(InsufficientStockException::class);

    expect($product)->toHaveStock(2);
    expect(Sale::count())->toBe(0);
});

it('applies only the difference when a completed sale is edited', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = $this->sales->save(saleData(), [saleLine($product, 3)]);

    $this->sales->save(saleData(overrides: ['reference_no' => $sale->reference_no]), [saleLine($product, 4)], $sale);

    expect($product)->toHaveStock(6);
});

it('moves stock between products when an edit swaps them', function () {
    $old = Product::factory()->withStock(10)->create();
    $new = Product::factory()->withStock(10)->create();
    $sale = $this->sales->save(saleData(), [saleLine($old, 3)]);

    $this->sales->save(saleData(), [saleLine($new, 2)], $sale);

    expect($old)->toHaveStock(10);
    expect($new)->toHaveStock(8);
});

it('puts stock back when a completed sale is cancelled', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = $this->sales->save(saleData(), [saleLine($product, 3)]);

    $this->sales->save(saleData(Sale::STATUS_CANCELLED), [saleLine($product, 3)], $sale);

    expect($product)->toHaveStock(10);
    expect($sale->fresh()->status)->toBe(Sale::STATUS_CANCELLED);
});

it('puts stock back when a sale is deleted', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = $this->sales->save(saleData(), [saleLine($product, 3)]);

    $this->sales->delete($sale);

    expect($product)->toHaveStock(10);
    $this->assertModelMissing($sale);
});

it('refuses to change a sale that has returns', function (string $change) {
    $product = Product::factory()->withStock(10)->create();
    $sale = $this->sales->save(saleData(), [saleLine($product, 3)]);
    returnOne($sale, $product);

    $attempt = $change === 'edit'
        ? fn () => $this->sales->save(saleData(), [saleLine($product, 1)], $sale)
        : fn () => $this->sales->delete($sale);

    expect($attempt)->toThrow(ValidationException::class, __('messages.sale.has_returns'));

    expect($product)->toHaveStock(8);
})->with(['edit', 'delete']);

describe('sale form', function () {
    it('creates a sale and deducts stock', function () {
        $product = Product::factory()->withStock(5)->create();

        Livewire::test(SaleForm::class)
            ->set('items', [saleLine($product, 2)])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('sales.index'));

        expect($product)->toHaveStock(3);
    });

    it('reports overselling as a validation error', function () {
        $product = Product::factory()->withStock(1)->create();

        Livewire::test(SaleForm::class)
            ->set('items', [saleLine($product, 2)])
            ->call('save')
            ->assertHasErrors('items');

        expect($product)->toHaveStock(1);
    });

    it('validates its fields', function (string $field, mixed $value, string $error, string $rule) {
        Livewire::test(SaleForm::class)
            ->set($field, $value)
            ->call('save')
            ->assertHasErrors([$error => $rule]);
    })->with([
        'missing date' => ['sale_date', '', 'sale_date', 'required'],
        'unknown status' => ['status', 'shipped', 'status', 'in'],
        'no items' => ['items', [], 'items', 'required'],
        'unknown product' => ['items', [['product_id' => 999, 'quantity' => 1, 'unit_price' => 1]], 'items.0.product_id', 'exists'],
        'zero quantity' => ['items', [['product_id' => 999, 'quantity' => 0, 'unit_price' => 1]], 'items.0.quantity', 'min'],
    ]);

    it('loads an existing sale for editing', function () {
        $product = Product::factory()->withStock(10)->create();
        $sale = $this->sales->save(saleData(), [saleLine($product, 3, 7.5)]);

        Livewire::test(SaleForm::class, ['id' => $sale->id])
            ->assertSet('reference_no', $sale->reference_no)
            ->assertSet('items.0.quantity', 3);
    });
});

it('shows why a delete was refused on the sale list', function () {
    $product = Product::factory()->withStock(10)->create();
    $sale = $this->sales->save(saleData(), [saleLine($product, 3)]);
    returnOne($sale, $product);

    Livewire::test(SaleList::class)
        ->call('delete', $sale->id)
        ->assertSee(__('messages.sale.has_returns'));

    $this->assertModelExists($sale);
});
