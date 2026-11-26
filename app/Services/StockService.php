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

class StockService
{
    /**
     * @param  iterable<int>  $productIds
     * @return EloquentCollection<int, Product>
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
     * @param  array<int, int>  $changes
     * @return Collection<int, StockMovement>
     *
     * @throws InsufficientStockException
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
     * @param  array<int, int>  $quantities
     * @return Collection<int, StockMovement>
     */
    public function add(StockMovementType $type, array $quantities, ?Model $reference = null, ?string $note = null): Collection
    {
        return $this->record($type, $this->positive($quantities), $reference, $note);
    }

    /**
     * @param  array<int, int>  $quantities
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
     * @return array<string, array<int, int>>
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
     * @return Collection<int, StockMovement>
     *
     * @throws InsufficientStockException
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

    public function opening(Product $product, int $quantity, ?string $note = null): ?StockMovement
    {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Opening stock cannot be negative.');
        }

        return $this->record(StockMovementType::Opening, [$product->id => $quantity], note: $note)->first();
    }

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
