<?php

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Services\StockService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

[, $productId, $quantity, $holdMilliseconds] = $argv;

require __DIR__.'/../../../vendor/autoload.php';

$app = require __DIR__.'/../../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

try {
    DB::transaction(function () use ($productId, $quantity, $holdMilliseconds) {
        app(StockService::class)->remove(StockMovementType::Sale, [(int) $productId => (int) $quantity]);

        fwrite(STDOUT, "locked\n");
        fflush(STDOUT);

        usleep((int) $holdMilliseconds * 1000);
    });

    fwrite(STDOUT, "sold\n");
} catch (InsufficientStockException) {
    fwrite(STDOUT, "refused\n");
}
