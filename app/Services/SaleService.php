<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function __construct(
        private readonly StockService $stock,
    ) {}

    /**
     * @param  array{reference_no: string, sale_date: string, status: string, note?: ?string, customer_id?: ?int}  $data
     * @param  list<array{product_id: int|string, quantity: int|string, unit_price: float|string}>  $items
     *
     * @throws ValidationException
     */
    public function save(array $data, array $items, ?Sale $sale = null): Sale
    {
        return DB::transaction(function () use ($data, $items, $sale) {
            $newIds = array_column($items, 'product_id');

            if ($sale) {
                $sale = Sale::query()->lockForUpdate()->findOrFail($sale->id);
                $this->ensureHasNoReturns($sale);

                $oldIds = $sale->items()->pluck('product_id')->all();
                $this->stock->lock([...$oldIds, ...$newIds]);

                $this->stock->reverse($sale, 'Sale updated');
                $sale->update($data);
                $sale->items()->delete();
            } else {
                $this->stock->lock($newIds);
                $sale = Sale::create($data);
            }

            foreach ($items as $item) {
                $sale->items()->create([
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            if ($sale->status === Sale::STATUS_COMPLETED) {
                $this->stock->remove(StockMovementType::Sale, $this->quantitiesByProduct($items), $sale);
            }

            return $sale->load('items');
        });
    }

    /**
     * @throws ValidationException
     */
    public function delete(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            $sale = Sale::query()->lockForUpdate()->findOrFail($sale->id);
            $this->ensureHasNoReturns($sale);

            $this->stock->reverse($sale, 'Sale deleted');
            $sale->items()->delete();
            $sale->delete();
        });
    }

    private function ensureHasNoReturns(Sale $sale): void
    {
        if ($sale->returns()->exists()) {
            throw ValidationException::withMessages([
                'items' => __('messages.sale.has_returns'),
            ]);
        }
    }

    /**
     * @param  list<array{product_id: int|string, quantity: int|string}>  $items
     * @return array<int, int>
     */
    private function quantitiesByProduct(array $items): array
    {
        $quantities = [];

        foreach ($items as $item) {
            $productId = (int) $item['product_id'];
            $quantities[$productId] = ($quantities[$productId] ?? 0) + (int) $item['quantity'];
        }

        return $quantities;
    }
}
