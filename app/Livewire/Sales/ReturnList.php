<?php

namespace App\Livewire\Sales;

use App\Models\Customer;
use App\Models\SaleReturn;
use App\Services\SaleReturnService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class ReturnList extends Component
{
    use WithPagination;

    public $filter = [
        'from' => null,
        'to' => null,
        'customer_id' => null,
    ];

    public $selectedReturns = [];

    public $selectAll = false;

    protected $paginationTheme = 'bootstrap';

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedReturns = SaleReturn::pluck('id')->toArray();
        } else {
            $this->selectedReturns = [];
        }
    }

    public function confirmDelete(SaleReturnService $returns, $id)
    {
        try {
            $returns->delete(SaleReturn::findOrFail($id));
        } catch (ValidationException $e) {
            session()->flash('error', collect($e->errors())->flatten()->first());

            return;
        }

        session()->flash('message', __('messages.return.deleted'));
    }

    public function render()
    {
        $query = SaleReturn::query()->with('sale.customer');

        if ($this->filter['from']) {
            $query->whereDate('return_date', '>=', $this->filter['from']);
        }

        if ($this->filter['to']) {
            $query->whereDate('return_date', '<=', $this->filter['to']);
        }

        if ($this->filter['customer_id']) {
            $query->whereHas('sale', fn ($q) => $q->where('customer_id', $this->filter['customer_id']));
        }

        return view('livewire.sales.return-list', [
            'returns' => $query->latest()->paginate(10),
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }
}
