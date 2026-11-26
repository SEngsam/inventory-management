<?php

namespace Tests\Feature\Stock;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Livewire\Sales\SaleForm;
use App\Livewire\Sales\SaleList;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Services\SaleReturnService;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\Feature\Stock\Concerns\InteractsWithStock;
use Tests\TestCase;

class SaleStockTest extends TestCase
{
    use InteractsWithStock;
    use RefreshDatabase;

    private SaleService $sales;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sales = app(SaleService::class);
    }

    private function data(string $status = Sale::STATUS_COMPLETED): array
    {
        return [
            'reference_no' => 'SL-'.uniqid(),
            'sale_date' => now()->toDateString(),
            'status' => $status,
        ];
    }

    public function test_a_completed_sale_deducts_stock_and_references_the_sale(): void
    {
        $product = $this->productWithStock(10);

        $sale = $this->sales->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
        ]);

        $this->assertStock(7, $product);
        $movement = StockMovement::where('reference_type', 'sale')->sole();
        $this->assertSame(StockMovementType::Sale, $movement->type);
        $this->assertSame($sale->id, $movement->reference_id);
    }

    public function test_repeated_lines_for_the_same_product_are_combined(): void
    {
        $product = $this->productWithStock(10);

        $this->sales->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 5],
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
        ]);

        $this->assertStock(5, $product);
    }

    public function test_pending_sales_do_not_touch_stock(): void
    {
        $product = $this->productWithStock(10);

        $this->sales->save($this->data(Sale::STATUS_PENDING), [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
        ]);

        $this->assertStock(10, $product);
    }

    public function test_overselling_is_rejected_and_nothing_is_saved(): void
    {
        $product = $this->productWithStock(2);

        try {
            $this->sales->save($this->data(), [
                ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
            ]);
            $this->fail('Expected the sale to be rejected.');
        } catch (InsufficientStockException) {
        }

        $this->assertStock(2, $product);
        $this->assertSame(0, Sale::count());
    }

    public function test_editing_a_completed_sale_only_applies_the_difference(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sales->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
        ]);

        $this->sales->save([...$this->data(), 'reference_no' => $sale->reference_no], [
            ['product_id' => $product->id, 'quantity' => 4, 'unit_price' => 5],
        ], $sale);

        $this->assertStock(6, $product);
    }

    public function test_editing_can_move_stock_between_products(): void
    {
        $old = $this->productWithStock(10);
        $new = $this->productWithStock(10);
        $sale = $this->sales->save($this->data(), [
            ['product_id' => $old->id, 'quantity' => 3, 'unit_price' => 5],
        ]);

        $this->sales->save($this->data(), [
            ['product_id' => $new->id, 'quantity' => 2, 'unit_price' => 5],
        ], $sale);

        $this->assertStock(10, $old);
        $this->assertStock(8, $new);
    }

    public function test_cancelling_a_completed_sale_puts_stock_back(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sales->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
        ]);

        $this->sales->save($this->data(Sale::STATUS_CANCELLED), [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
        ], $sale);

        $this->assertStock(10, $product);
        $this->assertSame(Sale::STATUS_CANCELLED, $sale->fresh()->status);
    }

    public function test_deleting_a_sale_puts_stock_back(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sales->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
        ]);

        $this->sales->delete($sale);

        $this->assertStock(10, $product);
        $this->assertModelMissing($sale);
    }

    public function test_sales_with_returns_cannot_be_edited_or_deleted(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sales->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
        ]);
        app(SaleReturnService::class)->create($sale, [
            'reference_no' => 'RET-1',
            'return_date' => now()->toDateString(),
        ], [$product->id => 1]);

        foreach ([
            fn () => $this->sales->delete($sale),
            fn () => $this->sales->save($this->data(), [['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 5]], $sale),
        ] as $attempt) {
            try {
                $attempt();
                $this->fail('Expected the change to be rejected.');
            } catch (ValidationException $e) {
                $this->assertSame(__('messages.sale.has_returns'), $e->errors()['items'][0]);
            }
        }

        $this->assertStock(8, $product);
    }

    public function test_the_sale_form_reports_overselling_as_a_validation_error(): void
    {
        $product = $this->productWithStock(1);

        Livewire::test(SaleForm::class)
            ->set('items', [['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 5]])
            ->call('save')
            ->assertHasErrors('items');

        $this->assertStock(1, $product);
    }

    public function test_the_sale_form_creates_a_sale_and_deducts_stock(): void
    {
        $product = $this->productWithStock(5);

        Livewire::test(SaleForm::class)
            ->set('items', [['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 5]])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('sales.index'));

        $this->assertStock(3, $product);
    }

    public function test_the_sale_list_shows_why_a_delete_was_refused(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sales->save($this->data(), [
            ['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5],
        ]);
        app(SaleReturnService::class)->create($sale, [
            'reference_no' => 'RET-1',
            'return_date' => now()->toDateString(),
        ], [$product->id => 1]);

        Livewire::test(SaleList::class)
            ->call('delete', $sale->id)
            ->assertSee(__('messages.sale.has_returns'));

        $this->assertModelExists($sale);
    }
}
