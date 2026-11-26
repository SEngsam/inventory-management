<?php

use App\Exceptions\InsufficientStockException;
use App\Livewire\Purchases\PurchaseForm;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use App\Services\StockService;
use Livewire\Livewire;

function purchaseData(string $status = Purchase::STATUS_RECEIVED, array $overrides = []): array
{
    return [
        'supplier_id' => Supplier::factory()->create()->id,
        'purchase_date' => now()->toDateString(),
        'reference_no' => 'PO-'.uniqid(),
        'status' => $status,
        ...$overrides,
    ];
}

function purchaseLine(Product $product, int $quantity): array
{
    return ['product_id' => $product->id, 'quantity' => $quantity, 'unit_cost' => 3];
}

beforeEach(function () {
    $this->purchases = app(PurchaseService::class);
});

it('adds stock for a received purchase', function () {
    $product = Product::factory()->withStock(2)->create();

    $this->purchases->save(purchaseData(), [purchaseLine($product, 5)]);

    expect($product)->toHaveStock(7);
});

it('does not add stock while a purchase is :dataset', function (string $status) {
    $product = Product::factory()->withStock(2)->create();

    $this->purchases->save(purchaseData($status), [purchaseLine($product, 5)]);

    expect($product)->toHaveStock(2);
})->with([Purchase::STATUS_ORDERED, Purchase::STATUS_PENDING]);

it('adds stock once when an ordered purchase is received and saved again', function () {
    $product = Product::factory()->create();
    $purchase = $this->purchases->save(purchaseData(Purchase::STATUS_ORDERED), [purchaseLine($product, 5)]);
    $received = purchaseData(overrides: ['reference_no' => $purchase->reference_no]);

    $this->purchases->save($received, [purchaseLine($product, 5)], $purchase);
    $this->purchases->save($received, [purchaseLine($product, 5)], $purchase);

    expect($product)->toHaveStock(5);
});

it('refuses to reduce a received purchase whose stock was already sold', function () {
    $product = Product::factory()->create();
    $purchase = $this->purchases->save(purchaseData(), [purchaseLine($product, 5)]);
    app(StockService::class)->setLevel($product, 1);

    expect(fn () => $this->purchases->save(purchaseData(), [purchaseLine($product, 2)], $purchase))
        ->toThrow(InsufficientStockException::class);

    expect($product)->toHaveStock(1)
        ->and((int) $purchase->items()->sum('quantity'))->toBe(5);
});

describe('purchase form', function () {
    it('loads and updates an existing purchase on its edit page', function () {
        $product = Product::factory()->create();
        $purchase = $this->purchases->save(purchaseData(), [purchaseLine($product, 5)]);

        Livewire::test(PurchaseForm::class, ['purchaseId' => $purchase->id])
            ->assertSet('purchaseId', $purchase->id)
            ->assertSet('reference_no', $purchase->reference_no)
            ->set('items.0.quantity', 7)
            ->call('save')
            ->assertHasNoErrors();

        expect($product)->toHaveStock(7)
            ->and(Purchase::count())->toBe(1);
    });

    it('validates its fields', function (string $field, mixed $value, string $error, string $rule) {
        Livewire::test(PurchaseForm::class)
            ->set($field, $value)
            ->call('save')
            ->assertHasErrors([$error => $rule]);
    })->with([
        'missing supplier' => ['supplier_id', null, 'supplier_id', 'required'],
        'unknown status' => ['status', 'lost', 'status', 'in'],
        'no items' => ['items', [], 'items', 'required'],
        'negative cost' => ['items', [['product_id' => 1, 'quantity' => 1, 'unit_cost' => -1]], 'items.0.unit_cost', 'min'],
    ]);

    it('rejects a duplicate reference number', function () {
        $existing = Purchase::factory()->create();

        Livewire::test(PurchaseForm::class)
            ->set('reference_no', $existing->reference_no)
            ->call('save')
            ->assertHasErrors(['reference_no' => 'unique']);
    });
});
