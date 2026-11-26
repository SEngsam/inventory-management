<?php

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

function startConcurrentSale(Product $product, int $quantity, int $holdMilliseconds): Process
{
    $process = new Process(
        [PHP_BINARY, __DIR__.'/Fixtures/remove-stock.php', $product->id, $quantity, $holdMilliseconds],
        base_path(),
        ['DB_CONNECTION' => DB::getDefaultConnection(), 'DB_DATABASE' => DB::getDatabaseName()],
    );

    $process->start();
    $process->waitUntil(fn (string $type, string $output) => str_contains($output, 'locked'));

    return $process;
}

beforeEach(function () {
    if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
        $this->markTestSkipped('Row locking needs MySQL or MariaDB; SQLite ignores FOR UPDATE.');
    }
});

it('makes a second sale of the last unit wait for the first and then refuses it', function () {
    $product = Product::factory()->withStock(1)->create();
    $other = startConcurrentSale($product, 1, holdMilliseconds: 1500);

    $started = microtime(true);

    expect(fn () => app(StockService::class)->remove(StockMovementType::Sale, [$product->id => 1]))
        ->toThrow(InsufficientStockException::class);

    $waited = microtime(true) - $started;
    $other->wait();

    expect($waited)->toBeGreaterThan(1.0)
        ->and(trim($other->getOutput()))->toEndWith('sold')
        ->and($product)->toHaveStock(0);
});

it('serialises concurrent sales so no quantity is lost', function () {
    $product = Product::factory()->withStock(10)->create();
    $other = startConcurrentSale($product, 3, holdMilliseconds: 800);

    app(StockService::class)->remove(StockMovementType::Sale, [$product->id => 4]);
    $other->wait();

    expect(trim($other->getOutput()))->toEndWith('sold')
        ->and($product)->toHaveStock(3)
        ->and($product->stockMovements()->orderBy('id')->pluck('balance_after')->all())->toBe([10, 7, 3]);
});
