<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    /**
     * Processes a purchase or sale.
     *
     * @param  int    $productId
     * @param  string $type      // 'purchase' أو 'sale'
     * @param  int    $quantity
     * @return InventoryTransaction
     */
    public function process(int $productId, string $type, int $quantity, ?int $customerId = null): InventoryTransaction
    {
        return DB::transaction(function () use ($productId, $type, $quantity, $customerId) {

            // Validate quantity
            if ($quantity <= 0) {
                throw new InvalidArgumentException("Quantity must be greater than zero");
            }
            $product = Product::findOrFail($productId);

            // Validate stock on sale
            if ($type === 'sale' && $product->stock_quantity  < $quantity) {
                throw new InvalidArgumentException("Insufficient stock");
            }
            // Calculate new quantity
            $newQty = $type === 'purchase'
                ? $product->stock_quantity  + $quantity
                : $product->stock_quantity  - $quantity;
            // Prevent negative stock
            if ($newQty < 0) {
                throw new InvalidArgumentException("لا يمكن أن تصبح الكمية سالبة");
            }
            // Update product quantity
            $product->stock_quantity  = $newQty;
            $product->save();

            // Record transaction
            return InventoryTransaction::create([
                'product_id' => $product->id,
                'type'       => $type,
                'quantity'   => $quantity,
                'performed_at' => now(),
                'customer_id'  => $type === 'sale' ? $customerId : null,
            ]);
        });
    }
}
