<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * The only place allowed to change product stock.
 *
 * Every change is written to the stock_movements ledger and applied to the
 * cached products.stock_quantity column in the same transaction, while the
 * affected product rows are locked. That keeps the two in sync and stops
 * concurrent requests from selling the same unit twice.
 */
class StockService
{
    /**
     * Lock the given products for the rest of the current transaction.
     *
     * Rows are always locked in ascending id order so two transactions that
     * touch the same products cannot deadlock by locking them in different
     * orders. Callers that change stock in several steps inside one
     * transaction should lock every product they will touch up front.
     *
     * @param  iterable<int>  $productIds
     * @return EloquentCollection<int, Product> keyed by product id
     */
    public function lock(iterable $productIds): EloquentCollection
    {
        $ids = collect($productIds)->map(fn ($id) => (int) $id)->unique()->sort()->values();

        if ($ids->isEmpty()) {
            return new EloquentCollection;
        }

        $products = Product::query()
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $missing = $ids->diff($products->keys());

        if ($missing->isNotEmpty()) {
            throw (new ModelNotFoundException)->setModel(Product::class, $missing->all());
        }

        return $products;
    }

    /**
     * Record stock changes and apply them to the products.
     *
     * Either every change is applied or none is: all lines are checked
     * before anything is written.
     *
     * @param  array<int, int>  $changes  product id => signed quantity (positive adds stock)
     * @return Collection<int, StockMovement>
     *
     * @throws InsufficientStockException when a product would go below zero
     */
    public function record(
        StockMovementType $type,
        array $changes,
        ?Model $reference = null,
        ?string $note = null,
    ): Collection {
        $changes = array_filter(
            array_map('intval', $changes),
            fn (int $quantity) => $quantity !== 0,
        );

        if ($changes === []) {
            return collect();
        }

        return DB::transaction(function () use ($type, $changes, $reference, $note) {
            $products = $this->lock(array_keys($changes));

            $shortages = [];

            foreach ($changes as $productId => $quantity) {
                $product = $products[$productId];

                if ($product->stock_quantity + $quantity < 0) {
                    $shortages[] = [
                        'product_id' => $product->id,
                        'product' => $product->name,
                        'available' => $product->stock_quantity,
                        'requested' => -$quantity,
                    ];
                }
            }

            if ($shortages !== []) {
                throw InsufficientStockException::forShortages($shortages);
            }

            $movements = collect();

            foreach ($changes as $productId => $quantity) {
                $product = $products[$productId];
                $product->stock_quantity += $quantity;
                $product->save();

                $movements->push(StockMovement::create([
                    'product_id' => $product->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'balance_after' => $product->stock_quantity,
                    'reference_type' => $reference?->getMorphClass(),
                    'reference_id' => $reference?->getKey(),
                    'user_id' => Auth::id(),
                    'note' => $note,
                ]));
            }

            return $movements;
        });
    }

    /**
     * Add stock, e.g. for a received purchase or a customer return.
     *
     * @param  array<int, int>  $quantities  product id => positive quantity
     * @return Collection<int, StockMovement>
     */
    public function add(StockMovementType $type, array $quantities, ?Model $reference = null, ?string $note = null): Collection
    {
        return $this->record($type, $this->positive($quantities), $reference, $note);
    }

    /**
     * Remove stock, e.g. for a completed sale.
     *
     * @param  array<int, int>  $quantities  product id => positive quantity
     * @return Collection<int, StockMovement>
     *
     * @throws InsufficientStockException
     */
    public function remove(StockMovementType $type, array $quantities, ?Model $reference = null, ?string $note = null): Collection
    {
        return $this->record(
            $type,
            array_map(fn (int $quantity) => -$quantity, $this->positive($quantities)),
            $reference,
            $note,
        );
    }

    /**
     * Net stock effect a document has had so far, per movement type and product.
     *
     * @return array<string, array<int, int>> movement type => [product id => signed quantity]
     */
    public function netFor(Model $reference): array
    {
        $net = [];

        StockMovement::query()
            ->where('reference_type', $reference->getMorphClass())
            ->where('reference_id', $reference->getKey())
            ->selectRaw('type, product_id, SUM(quantity) as net')
            ->groupBy('type', 'product_id')
            ->get()
            ->each(function ($row) use (&$net) {
                if ((int) $row->net !== 0) {
                    $net[$row->type->value][(int) $row->product_id] = (int) $row->net;
                }
            });

        return $net;
    }

    /**
     * Cancel out every stock change recorded for a document.
     *
     * Reversals keep the original movement type with the opposite sign, so a
     * deleted sale shows up as a positive "sale" movement.
     *
     * @return Collection<int, StockMovement>
     *
     * @throws InsufficientStockException when stock the document added has since been used
     */
    public function reverse(Model $reference, ?string $note = null): Collection
    {
        return DB::transaction(function () use ($reference, $note) {
            $net = $this->netFor($reference);

            $this->lock(collect($net)->flatMap(fn ($lines) => array_keys($lines)));

            return collect($net)->flatMap(fn (array $lines, string $type) => $this->record(
                StockMovementType::from($type),
                array_map(fn (int $quantity) => -$quantity, $lines),
                $reference,
                $note,
            ));
        });
    }

    /**
     * Record a product's starting stock.
     */
    public function opening(Product $product, int $quantity, ?string $note = null): ?StockMovement
    {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Opening stock cannot be negative.');
        }

        return $this->record(StockMovementType::Opening, [$product->id => $quantity], note: $note)->first();
    }

    /**
     * Correct a product's stock to a counted level with an adjustment movement.
     */
    public function setLevel(Product $product, int $level, ?string $note = null): ?StockMovement
    {
        if ($level < 0) {
            throw new InvalidArgumentException('Stock level cannot be negative.');
        }

        return DB::transaction(function () use ($product, $level, $note) {
            $current = $this->lock([$product->id])[$product->id]->stock_quantity;

            return $this->record(
                StockMovementType::Adjustment,
                [$product->id => $level - $current],
                note: $note,
            )->first();
        });
    }

    /**
     * @param  array<int, int>  $quantities
     * @return array<int, int>
     */
    private function positive(array $quantities): array
    {
        foreach ($quantities as $productId => $quantity) {
            if ((int) $quantity <= 0) {
                throw new InvalidArgumentException("Quantity for product {$productId} must be greater than zero.");
            }
        }

        return array_map('intval', $quantities);
    }
}
