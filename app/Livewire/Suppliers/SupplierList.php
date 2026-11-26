<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierList extends Component
{
    use WithPagination;

    public $selectedSuppliers = [];

    public $selectAll = false;

    protected $listeners = [
        'supplierAdded' => '$refresh',
        'supplierUpdated' => '$refresh',
    ];

    protected $paginationTheme = 'bootstrap';

    public function mount(): void {}

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedSuppliers = Supplier::query()
                ->latest()
                ->paginate(10)
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedSuppliers = [];
        }
    }

    public function deleteSelected(): void
    {

        if (! empty($this->selectedSuppliers)) {
            Supplier::whereIn('id', $this->selectedSuppliers)->delete();

            session()->flash('message', __('messages.supplier.bulk_deleted'));
            $this->selectedSuppliers = [];
            $this->selectAll = false;
            $this->resetPage();
        }
    }

    public function delete($supplierId): void
    {

        $supplier = Supplier::find($supplierId);
        if ($supplier) {
            $supplier->delete();
            session()->flash('message', __('messages.supplier.deleted'));
        }

        $this->resetPage();
    }

    public function render()
    {
        $suppliers = Supplier::latest()->paginate(10);

        return view('livewire.suppliers.supplier-list', compact('suppliers'));
    }
}
