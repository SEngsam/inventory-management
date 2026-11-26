<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function __construct(
        private readonly StockService $stock,
    ) {}

    /**
     * Create or update a purchase. Stock is only added once the purchase is
     * received; editing puts the previous effect back first.
     *
     * @param  array{supplier_id: int, purchase_date: string, reference_no: string, status: string, notes?: ?string}  $data
     * @param  list<array{product_id: int|string, quantity: int|string, unit_cost: float|string}>  $items
     *
     * @throws ValidationException when received stock that is being removed was already sold
     */
    public function save(array $data, array $items, ?Purchase $purchase = null): Purchase
    {
        return DB::transaction(function () use ($data, $items, $purchase) {
            $newIds = array_column($items, 'product_id');

            if ($purchase) {
                $purchase = Purchase::query()->lockForUpdate()->findOrFail($purchase->id);

                $oldIds = $purchase->items()->pluck('product_id')->all();
                $this->stock->lock([...$oldIds, ...$newIds]);

                $this->stock->reverse($purchase, 'Purchase updated');
                $purchase->update($data);
                $purchase->items()->delete();
            } else {
                $this->stock->lock($newIds);
                $purchase = Purchase::create($data);
            }

            $quantities = [];

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];

                $purchase->items()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_cost' => $item['unit_cost'],
                    'total' => $quantity * (float) $item['unit_cost'],
                ]);

                $quantities[$productId] = ($quantities[$productId] ?? 0) + $quantity;
            }

            if ($purchase->status === Purchase::STATUS_RECEIVED) {
                $this->stock->add(StockMovementType::Purchase, $quantities, $purchase);
            }

            return $purchase->load('items');
        });
    }
}
