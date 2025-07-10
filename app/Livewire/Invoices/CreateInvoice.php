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
    public $items = []; // [ ['product_id' => 1, 'quantity' => 2], ... ]

    public function mount()
    {
        $this->customers = Customer::all();
        $this->products  = Product::all();
        $this->items[]   = ['product_id' => null, 'quantity' => 1];
    }

    public function addItem()
    {
        $this->items[] = ['product_id' => null, 'quantity' => 1];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

public function createInvoice()
{
    $customer = Customer::findOrFail($this->selectedCustomer);

    $items = [];

    foreach ($this->items as $item) {
        if (!$item['product_id']) {
            continue;
        }

        $product = Product::findOrFail($item['product_id']);

        $items[] = [
            'product_id' => $product->id,
            'quantity'   => $item['quantity'],
            'price'      => $product->price,
            'total'      => $product->price * $item['quantity'],
        ];
    }


    $invoice = app(InvoiceService::class)->createFromSale(
        $items,
        $customer
    );

    // 🚀 إعادة توجيه لصفحة عرض الفاتورة
    return redirect()->route('invoices.show', $invoice->id);
}

    public function render()
    {
        return view('livewire.invoices.create-invoice');
    }
}
