<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;

class SupplierForm extends Component
{
    public $supplierId;
    public $supplier;

    public $name, $email, $phone, $company, $address, $note;

    protected $listeners = ['resetForm'];

    public function mount($supplier = null)
    {
        if ($supplier) {

            $supplier = Supplier::findOrFail($supplier);
            $this->supplierId = $supplier->id;
            $this->supplier = $supplier;

            $this->fill($supplier->only('name', 'email', 'phone', 'company', 'address', 'note'));
        } else {
        }
    }

    public function save()
    {
  

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $supplier = Supplier::updateOrCreate(
            ['id' => $this->supplierId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
                'address' => $this->address,
                'note' => $this->note,
            ]
        );

        session()->flash('message', $this->supplierId ? 'Supplier updated!' : 'Supplier created!');

         $this->dispatch($this->supplierId ? 'supplierUpdated' : 'supplierAdded');

        return redirect()->route('suppliers.index');
    }

    public function resetForm()
    {
        $this->supplier = null;
        $this->supplierId = null;
        $this->name = $this->email = $this->phone = $this->company = $this->address = $this->note = null;
    }

    public function render()
    {
        return view('livewire.suppliers.supplier-form');
    }
}