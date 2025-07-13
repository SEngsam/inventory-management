<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $selectedCustomers = [];
    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCustomers = Customer::latest()->paginate(10)->pluck('id')->toArray();
        } else {
            $this->selectedCustomers = [];
        }
    }

    public function deleteCustomer()
    {
        if (!empty($this->selectedCustomers)) {
            Customer::whereIn('id', $this->selectedCustomers)->delete();
            session()->flash('message', 'Selected suppliers deleted!');
            $this->selectedCustomers = [];
            $this->selectAll = false;
            $this->resetPage();
        }
    }

    public function delete($supplierId)
    {
        Customer::find($supplierId)->delete();
        session()->flash('message', 'Supplier deleted!');
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        $customers = Customer::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('company', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.customers.customer-list', compact('customers'));
    }
}
