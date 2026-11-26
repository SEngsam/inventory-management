<?php

use App\Livewire\Stock\StockMovementList;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use App\Services\StockService;
use Livewire\Livewire;

it('is available to admins', function () {
    $this->withoutVite();
    actingAsAdmin();

    $this->get(route('stock-movements.index'))
        ->assertOk()
        ->assertSeeLivewire(StockMovementList::class);
});

it('lists movements with a link to the document that caused them', function () {
    $product = Product::factory()->withStock(10)->create(['name' => 'Blue Widget']);
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
});

it('filters by product and movement type from the query string', function () {
    Product::factory()->withStock(5)->create();
    $other = Product::factory()->withStock(5)->create();
    app(StockService::class)->setLevel($other, 2);

    Livewire::withQueryParams(['product' => $other->id, 'type' => 'adjustment'])
        ->test(StockMovementList::class)
        ->assertViewHas('movements', fn ($movements) => $movements->count() === 1
            && $movements->first()->product_id === $other->id
            && $movements->first()->quantity === -3);
});
