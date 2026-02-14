<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Sale;
use Livewire\WithPagination;

class SaleList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function delete($id)
    {
        $sale = Sale::findOrFail($id);

        if ($sale->status === 'completed') {
            foreach ($sale->items as $item) {
                $product = $item->product;
                $product->stock_quantity  += $item->quantity;
                $product->save();
            }
        }

        $sale->items()->delete();
        $sale->delete();

        session()->flash('success', 'Invoice Successfully Deleted');
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.sales.sale-list', [
            'sales' => Sale::with('customer', 'items')
                ->latest()
                ->paginate(10),
        ]);
    }
}
