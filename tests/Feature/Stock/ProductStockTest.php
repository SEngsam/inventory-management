<?php

namespace Tests\Feature\Stock;

use App\Enums\StockMovementType;
use App\Livewire\Products\ProductForm;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Feature\Stock\Concerns\InteractsWithStock;
use Tests\TestCase;

class ProductStockTest extends TestCase
{
    use InteractsWithStock;
    use RefreshDatabase;

    public function test_creating_a_product_records_its_opening_stock(): void
    {
        Livewire::test(ProductForm::class)
            ->set('name', 'Widget')
            ->set('sku', 'W-1')
            ->set('price', 9.99)
            ->set('category_id', Category::factory()->create()->id)
            ->set('stock_quantity', 12)
            ->call('save')
            ->assertHasNoErrors();

        $product = Product::where('sku', 'W-1')->sole();
        $this->assertStock(12, $product);
        $this->assertSame(StockMovementType::Opening, $product->stockMovements()->sole()->type);
    }

    public function test_editing_the_stock_field_records_an_adjustment(): void
    {
        $product = $this->productWithStock(10);

        Livewire::test(ProductForm::class, ['product' => $product->id])
            ->assertSet('stock_quantity', 10)
            ->set('stock_quantity', 8)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertStock(8, $product);
        $adjustment = $product->stockMovements()->latest('id')->first();
        $this->assertSame(StockMovementType::Adjustment, $adjustment->type);
        $this->assertSame(-2, $adjustment->quantity);
    }

    public function test_stock_cannot_be_mass_assigned(): void
    {
        $product = $this->productWithStock(10);

        $product->update(['stock_quantity' => 999]);

        $this->assertStock(10, $product);
    }
}
