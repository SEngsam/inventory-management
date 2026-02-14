<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;

class CustomerForm extends Component
{
    public $customer;

    public $name;
    public $email;
    public $phone;
    public $company;
    public $address;
    public $note;

    public function mount(?Customer $customer = null)
    {
        $this->customer = $customer;

        if ($this->customer) {
            abort_unless(auth()->user()->can('customers.update'), 403);

            $this->name = $customer->name;
            $this->email = $customer->email;
            $this->phone = $customer->phone;
            $this->company = $customer->company;
            $this->address = $customer->address;
            $this->note = $customer->note;
        } else {
            abort_unless(auth()->user()->can('customers.create'), 403);
        }
    }

    public function save()
    {
        if ($this->customer) {
            abort_unless(auth()->user()->can('customers.update'), 403);
        } else {
            abort_unless(auth()->user()->can('customers.create'), 403);
        }

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        if ($this->customer) {
            $this->customer->update($validated);
            session()->flash('message', 'Customer updated successfully.');
        } else {
            Customer::create($validated);
            session()->flash('message', 'Customer created successfully.');
        }

        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customers.customer-form');
    }
}