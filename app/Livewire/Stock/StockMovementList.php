<?php

namespace App\Livewire\Stock;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StockMovementList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'product')]
    public ?string $productId = null;

    #[Url]
    public ?string $type = null;

    public function updating(string $property): void
    {
        if (in_array($property, ['productId', 'type'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $movements = StockMovement::query()
            ->with(['product:id,name,sku', 'user:id,name', 'reference'])
            ->when($this->productId, fn ($query) => $query->where('product_id', $this->productId))
            ->when(StockMovementType::tryFrom((string) $this->type), fn ($query, $type) => $query->where('type', $type))
            ->latest('id')
            ->paginate(20);

        return view('livewire.stock.stock-movement-list', [
            'movements' => $movements,
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku', 'stock_quantity']),
            'types' => StockMovementType::cases(),
        ]);
    }
}
