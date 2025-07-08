<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;
use Livewire\WithPagination;

class SupplierList extends Component
{
    use WithPagination;

    public $selectedSuppliers = [];
    public $selectAll = false;

    protected $listeners = ['supplierAdded' => '$refresh', 'supplierUpdated' => '$refresh'];
    protected $paginationTheme = 'bootstrap';

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedSuppliers = Supplier::latest()->paginate(10)->pluck('id')->toArray();
        } else {
            $this->selectedSuppliers = [];
        }
    }

    public function deleteSelected()
    {
        if (!empty($this->selectedSuppliers)) {
            Supplier::whereIn('id', $this->selectedSuppliers)->delete();
            session()->flash('message', 'Selected suppliers deleted!');
            $this->selectedSuppliers = [];
            $this->selectAll = false;
            $this->resetPage();
        }
    }

    public function delete($supplierId)
    {
        Supplier::find($supplierId)->delete();
        session()->flash('message', 'Supplier deleted!');
        $this->resetPage();
    }

    public function render()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('livewire.suppliers.supplier-list', compact('suppliers'));
    }
}
