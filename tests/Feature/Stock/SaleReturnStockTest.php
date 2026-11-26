<?php

namespace Tests\Feature\Stock;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Livewire\Sales\ReturnForm;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Services\SaleReturnService;
use App\Services\SaleService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\Feature\Stock\Concerns\InteractsWithStock;
use Tests\TestCase;

class SaleReturnStockTest extends TestCase
{
    use InteractsWithStock;
    use RefreshDatabase;

    private SaleReturnService $returns;

    protected function setUp(): void
    {
        parent::setUp();
        $this->returns = app(SaleReturnService::class);
    }

    private function sale(Product $product, int $quantity, string $status = Sale::STATUS_COMPLETED, float $price = 5): Sale
    {
        return app(SaleService::class)->save([
            'reference_no' => 'SL-'.uniqid(),
            'sale_date' => now()->toDateString(),
            'status' => $status,
        ], [['product_id' => $product->id, 'quantity' => $quantity, 'unit_price' => $price]]);
    }

    private function returnItems(Sale $sale, array $quantities): SaleReturn
    {
        return $this->returns->create($sale, [
            'reference_no' => 'RET-'.uniqid(),
            'return_date' => now()->toDateString(),
        ], $quantities);
    }

    public function test_a_return_restores_stock(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sale($product, 4);

        $return = $this->returnItems($sale, [$product->id => 3]);

        $this->assertStock(9, $product);
        $this->assertSame(StockMovementType::Return, $return->items->first()->product->stockMovements()->latest('id')->first()->type);
    }

    public function test_return_prices_come_from_the_sale(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sale($product, 4, price: 12.5);

        $return = $this->returnItems($sale, [$product->id => 2]);

        $this->assertEquals(12.5, $return->items->first()->unit_price);
        $this->assertEquals(25, $return->total);
    }

    public function test_cumulative_returns_cannot_exceed_the_quantity_sold(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sale($product, 4);
        $this->returnItems($sale, [$product->id => 3]);

        $this->expectException(ValidationException::class);

        try {
            $this->returnItems($sale, [$product->id => 2]);
        } finally {
            $this->assertStock(9, $product);
            $this->assertSame([$product->id => 1], $this->returns->returnableQuantities($sale));
        }
    }

    public function test_products_that_were_not_sold_cannot_be_returned(): void
    {
        $sold = $this->productWithStock(10);
        $other = $this->productWithStock(10);
        $sale = $this->sale($sold, 2);

        $this->expectException(ValidationException::class);
        $this->returnItems($sale, [$other->id => 1]);
    }

    public function test_pending_sales_cannot_be_returned(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sale($product, 2, Sale::STATUS_PENDING);

        $this->expectException(ValidationException::class);
        $this->returnItems($sale, [$product->id => 1]);
    }

    public function test_zero_quantities_are_ignored_but_at_least_one_item_is_required(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sale($product, 2);

        $this->expectException(ValidationException::class);
        $this->returnItems($sale, [$product->id => 0]);
    }

    public function test_deleting_a_return_removes_the_stock_again(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sale($product, 4);
        $return = $this->returnItems($sale, [$product->id => 3]);

        $this->returns->delete($return);

        $this->assertStock(6, $product);
        $this->assertModelMissing($return);
    }

    public function test_a_return_cannot_be_deleted_once_its_goods_were_sold_again(): void
    {
        $product = $this->productWithStock(4);
        $sale = $this->sale($product, 4);
        $return = $this->returnItems($sale, [$product->id => 2]);
        app(StockService::class)->setLevel($product, 0);

        $this->expectException(InsufficientStockException::class);
        $this->returns->delete($return);
    }

    public function test_the_return_form_offers_only_completed_sales_with_returnable_quantities(): void
    {
        $product = $this->productWithStock(10);
        $completed = $this->sale($product, 4);
        $pending = $this->sale($product, 1, Sale::STATUS_PENDING);
        $this->returnItems($completed, [$product->id => 1]);

        $component = Livewire::test(ReturnForm::class);

        $this->assertTrue($component->get('sales')->contains($completed));
        $this->assertFalse($component->get('sales')->contains($pending));

        $component->set('sale_id', $completed->id)
            ->assertSet('items.0.max_quantity', 3)
            ->assertSet('items.0.quantity', 0)
            ->set('items.0.quantity', 2)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('sale-returns.index'));

        $this->assertStock(9, $product);
    }

    public function test_the_return_form_rejects_returning_too_much(): void
    {
        $product = $this->productWithStock(10);
        $sale = $this->sale($product, 2);

        Livewire::test(ReturnForm::class)
            ->set('sale_id', $sale->id)
            ->set('items.0.quantity', 3)
            ->call('save')
            ->assertHasErrors('items');

        $this->assertStock(8, $product);
    }
}
