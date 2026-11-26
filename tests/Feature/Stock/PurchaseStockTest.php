<?php

namespace Tests\Feature\Stock;

use App\Exceptions\InsufficientStockException;
use App\Livewire\Purchases\PurchaseForm;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Feature\Stock\Concerns\InteractsWithStock;
use Tests\TestCase;

class PurchaseStockTest extends TestCase
{
    use InteractsWithStock;
    use RefreshDatabase;

    private PurchaseService $purchases;

    protected function setUp(): void
    {
        parent::setUp();
        $this->purchases = app(PurchaseService::class);
    }

    private function data(string $status = Purchase::STATUS_RECEIVED): array
    {
        return [
            'supplier_id' => Supplier::factory()->create()->id,
            'purchase_date' => now()->toDateString(),
            'reference_no' => 'PO-'.uniqid(),
            'status' => $status,
        ];
    }

    public function test_a_received_purchase_adds_stock(): void
    {
        $product = $this->productWithStock(2);

        $this->purchases->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 3],
        ]);

        $this->assertStock(7, $product);
    }

    public function test_ordered_or_pending_purchases_do_not_add_stock(): void
    {
        $product = $this->productWithStock(2);

        foreach ([Purchase::STATUS_ORDERED, Purchase::STATUS_PENDING] as $status) {
            $this->purchases->save($this->data($status), [
                ['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 3],
            ]);
        }

        $this->assertStock(2, $product);
    }

    public function test_receiving_an_ordered_purchase_adds_its_stock_once(): void
    {
        $product = $this->productWithStock(0);
        $purchase = $this->purchases->save($this->data(Purchase::STATUS_ORDERED), [
            ['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 3],
        ]);

        $items = [['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 3]];
        $this->purchases->save([...$this->data(), 'reference_no' => $purchase->reference_no], $items, $purchase);
        $this->purchases->save([...$this->data(), 'reference_no' => $purchase->reference_no], $items, $purchase);

        $this->assertStock(5, $product);
    }

    public function test_reducing_a_received_purchase_fails_if_the_stock_was_already_sold(): void
    {
        $product = $this->productWithStock(0);
        $purchase = $this->purchases->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 3],
        ]);
        app(StockService::class)->setLevel($product, 1);

        $this->expectException(InsufficientStockException::class);

        try {
            $this->purchases->save($this->data(), [
                ['product_id' => $product->id, 'quantity' => 2, 'unit_cost' => 3],
            ], $purchase);
        } finally {
            $this->assertStock(1, $product);
            $this->assertSame(5, (int) $purchase->items()->sum('quantity'));
        }
    }

    public function test_the_edit_page_loads_the_existing_purchase(): void
    {
        $product = $this->productWithStock(0);
        $purchase = $this->purchases->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 3],
        ]);

        Livewire::test(PurchaseForm::class, ['purchaseId' => $purchase->id])
            ->assertSet('purchaseId', $purchase->id)
            ->assertSet('reference_no', $purchase->reference_no)
            ->set('items.0.quantity', 7)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertStock(7, $product);
        $this->assertSame(1, Purchase::count());
    }
}
