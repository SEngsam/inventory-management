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

    public function mount(): void
    {
        abort_unless(auth()->user()->can('customers.view'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectAll = false;
        $this->selectedCustomers = [];
    }

    protected function customersQuery()
    {
        return Customer::query()
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('company', 'like', '%' . $this->search . '%');
            })
            ->latest();
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedCustomers = $this->customersQuery()
                ->paginate(10)
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedCustomers = [];
        }
    }

    public function deleteCustomer(): void
    {
        abort_unless(auth()->user()->can('customers.delete'), 403);

        if (!empty($this->selectedCustomers)) {
            Customer::whereIn('id', $this->selectedCustomers)->delete();

            session()->flash('message', 'Selected customers deleted!');
            $this->selectedCustomers = [];
            $this->selectAll = false;
            $this->resetPage();
        }
    }

    public function delete($customerId): void
    {
        abort_unless(auth()->user()->can('customers.delete'), 403);

        $customer = Customer::find($customerId);
        if ($customer) {
            $customer->delete();
            session()->flash('message', 'Customer deleted!');
        }

        $this->resetPage();
    }

    public function render()
    {
        $customers = $this->customersQuery()->paginate(10);

        return view('livewire.customers.customer-list', compact('customers'));
    }
}