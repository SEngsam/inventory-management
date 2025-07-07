<?php
namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;

class SupplierList extends Component
{
    public $suppliers;
    public $selectedSuppliers = [];
    public $selectAll = false;

    public function mount()
    {
        $this->loadSuppliers();
    }

    public function loadSuppliers()
    {
        $this->suppliers = Supplier::latest()->get();
    }

    public function updatedSelectAll($value)
    {
        $this->selectedSuppliers = $value ? $this->suppliers->pluck('id')->toArray() : [];
    }

    public function deleteSelected()
    {
        Supplier::whereIn('id', $this->selectedSuppliers)->delete();
        session()->flash('message', 'Selected suppliers deleted!');
        $this->selectedSuppliers = [];
        $this->selectAll = false;
        $this->loadSuppliers();
    }

    public function render()
    {
        return view('livewire.suppliers.supplier-list');
    }
}
