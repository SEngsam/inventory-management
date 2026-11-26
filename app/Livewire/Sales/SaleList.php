<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class SaleList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function delete(SaleService $sales, $id)
    {
        try {
            $sales->delete(Sale::findOrFail($id));
        } catch (ValidationException $e) {
            session()->flash('error', collect($e->errors())->flatten()->first());

            return;
        }

        session()->flash('message', __('messages.sale.deleted'));
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
