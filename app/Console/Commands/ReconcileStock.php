<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcileStock extends Command
{
    protected $signature = 'stock:reconcile';

    protected $description = 'Check that every product\'s stock_quantity equals the sum of its stock movements';

    public function handle(): int
    {
        $ledger = DB::table('stock_movements')
            ->selectRaw('product_id, SUM(quantity) as total')
            ->groupBy('product_id');

        $mismatches = Product::query()
            ->leftJoinSub($ledger, 'ledger', 'ledger.product_id', '=', 'products.id')
            ->whereRaw('products.stock_quantity <> COALESCE(ledger.total, 0)')
            ->orderBy('products.id')
            ->get(['products.id', 'products.sku', 'products.name', 'products.stock_quantity', DB::raw('COALESCE(ledger.total, 0) as ledger_total')]);

        if ($mismatches->isEmpty()) {
            $this->info('Stock is in sync with the ledger for all '.Product::count().' products.');

            return self::SUCCESS;
        }

        $this->error("{$mismatches->count()} product(s) do not match their stock movements:");
        $this->table(
            ['ID', 'SKU', 'Name', 'stock_quantity', 'Ledger total'],
            $mismatches->map(fn ($row) => [$row->id, $row->sku, $row->name, $row->stock_quantity, (int) $row->ledger_total]),
        );

        return self::FAILURE;
    }
}
