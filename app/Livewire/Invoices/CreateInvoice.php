<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Services\InvoiceService;

class CreateInvoice extends Component
{
    public $customers;
    public $products;
    public $selectedCustomer;
    public $items = [];

    public function mount()
    {
        abort_unless(auth()->user()->can('invoices.create'), 403);

        $this->customers = Customer::all();
        $this->products  = Product::all();
        $this->items[]   = ['product_id' => null, 'quantity' => 1];
    }

    public function addItem()
    {
        abort_unless(auth()->user()->can('invoices.create'), 403);

        $this->items[] = ['product_id' => null, 'quantity' => 1];
    }

    public function removeItem($index)
    {
        abort_unless(auth()->user()->can('invoices.create'), 403);

        unset($this->items[$index]);
        $this->items = array_values($this->items);

        if (count($this->items) === 0) {
            $this->items[] = ['product_id' => null, 'quantity' => 1];
        }
    }

    protected function rules(): array
    {
        return [
            'selectedCustomer' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function createInvoice()
    {
        abort_unless(auth()->user()->can('invoices.create'), 403);

        $this->validate();

        $customer = Customer::findOrFail($this->selectedCustomer);

        $items = [];

        foreach ($this->items as $item) {
            if (empty($item['product_id'])) {
                continue;
            }

            $product = Product::findOrFail($item['product_id']);
            $qty = (int) $item['quantity'];

            $items[] = [
                'product_id' => $product->id,
                'quantity'   => $qty,
                'price'      => $product->price,
                'total'      => (float) $product->price * $qty,
            ];
        }

        if (count($items) === 0) {
            $this->addError('items', 'Please select at least one product.');
            return;
        }

        $invoice = app(InvoiceService::class)->createFromSale($items, $customer);

        return redirect()->route('invoices.show', $invoice->id);
    }

    public function render()
    {
        return view('livewire.invoices.create-invoice');
    }
}