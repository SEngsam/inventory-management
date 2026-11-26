<?php

namespace Tests\Feature\Stock;

use App\Livewire\Stock\StockMovementList;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Feature\Stock\Concerns\InteractsWithStock;
use Tests\TestCase;

class StockMovementListTest extends TestCase
{
    use InteractsWithStock;
    use RefreshDatabase;

    public function test_admins_can_open_the_ledger_page(): void
    {
        $this->withoutVite();
        $this->artisan('permissions:sync');
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('stock-movements.index'))
            ->assertOk()
            ->assertSeeLivewire(StockMovementList::class);
    }

    public function test_it_lists_movements_with_a_link_to_their_document(): void
    {
        $product = $this->productWithStock(10, ['name' => 'Blue Widget']);
        $sale = app(SaleService::class)->save([
            'reference_no' => 'SL-LEDGER',
            'sale_date' => now()->toDateString(),
            'status' => Sale::STATUS_COMPLETED,
        ], [['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 5]]);

        Livewire::test(StockMovementList::class)
            ->assertSee('Blue Widget')
            ->assertSee(__('stock.types.opening'))
            ->assertSee('-3')
            ->assertSee(route('sales.show', $sale->id))
            ->assertSee('SL-LEDGER');
    }

    public function test_it_filters_by_product_and_type(): void
    {
        $this->productWithStock(5);
        $other = $this->productWithStock(5);
        app(StockService::class)->setLevel($other, 2);

        Livewire::withQueryParams(['product' => $other->id, 'type' => 'adjustment'])
            ->test(StockMovementList::class)
            ->assertViewHas('movements', function ($movements) use ($other) {
                return $movements->count() === 1
                    && $movements->first()->product_id === $other->id
                    && $movements->first()->quantity === -3;
            });
    }
}
