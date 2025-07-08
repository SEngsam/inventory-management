<?php
namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteCustomer($id)
    {
        Customer::findOrFail($id)->delete();
        session()->flash('message', 'Customer deleted successfully!');
    }

    public function render()
    {
        $customers = Customer::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('company', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.customers.customer-list', compact('customers'));
    }
}

