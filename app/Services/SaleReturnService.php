<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleReturnService
{
    public function __construct(
        private readonly StockService $stock,
    ) {}

    /**
     * @return array<int, int>
     */
    public function returnableQuantities(Sale $sale): array
    {
        $sold = $sale->items()
            ->selectRaw('product_id, SUM(quantity) as quantity')
            ->groupBy('product_id')
            ->pluck('quantity', 'product_id');

        $returned = DB::table('sale_return_items')
            ->join('sale_returns', 'sale_returns.id', '=', 'sale_return_items.sale_return_id')
            ->where('sale_returns.sale_id', $sale->id)
            ->selectRaw('sale_return_items.product_id, SUM(sale_return_items.quantity) as quantity')
            ->groupBy('sale_return_items.product_id')
            ->pluck('quantity', 'product_id');

        return $sold
            ->mapWithKeys(fn ($quantity, $productId) => [
                (int) $productId => max(0, (int) $quantity - (int) ($returned[$productId] ?? 0)),
            ])
            ->all();
    }

    /**
     * @param  array{reference_no: string, return_date: string, note?: ?string}  $data
     * @param  array<int, int>  $quantities
     *
     * @throws ValidationException
     */
    public function create(Sale $sale, array $data, array $quantities): SaleReturn
    {
        return DB::transaction(function () use ($sale, $data, $quantities) {
            $sale = Sale::query()->lockForUpdate()->findOrFail($sale->id);

            if ($sale->status !== Sale::STATUS_COMPLETED) {
                throw ValidationException::withMessages([
                    'sale_id' => __('messages.return.sale_not_completed'),
                ]);
            }

            $quantities = array_filter(array_map('intval', $quantities), fn (int $quantity) => $quantity > 0);

            if ($quantities === []) {
                throw ValidationException::withMessages([
                    'items' => __('messages.items.required'),
                ]);
            }

            $returnable = $this->returnableQuantities($sale);
            $items = $sale->items()->with('product')->get()->groupBy('product_id');

            foreach ($quantities as $productId => $quantity) {
                if (! isset($returnable[$productId])) {
                    throw ValidationException::withMessages([
                        'items' => __('messages.return.invalid_items'),
                    ]);
                }

                if ($quantity > $returnable[$productId]) {
                    throw ValidationException::withMessages([
                        'items' => __('messages.return.exceeds_returnable', [
                            'product' => $items[$productId]->first()->product->name,
                            'max' => $returnable[$productId],
                        ]),
                    ]);
                }
            }

            $this->stock->lock(array_keys($quantities));

            $lines = [];
            foreach ($quantities as $productId => $quantity) {
                $unitPrice = round((float) $items[$productId]->avg('unit_price'), 2);
                $lines[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => round($unitPrice * $quantity, 2),
                ];
            }

            $return = SaleReturn::create([
                ...$data,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'total' => array_sum(array_column($lines, 'total')),
            ]);

            $return->items()->createMany($lines);

            $this->stock->add(StockMovementType::Return, $quantities, $return);

            return $return->load('items');
        });
    }

    /**
     * @throws ValidationException
     */
    public function delete(SaleReturn $return): void
    {
        DB::transaction(function () use ($return) {
            $this->stock->reverse($return, 'Return deleted');
            $return->items()->delete();
            $return->delete();
        });
    }
}
