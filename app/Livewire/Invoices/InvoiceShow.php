<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;

class InvoiceShow extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice->loadMissing([
            'customer',
            'items.product',
            'user',
        ]);
    }

    public function render()
    {
        return view('livewire.invoices.invoice-show');
    }
}
