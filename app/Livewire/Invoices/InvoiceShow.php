<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use Livewire\Component;

class InvoiceShow extends Component
{
    public $invoice;

    public function mount(Invoice $invoice)
    {
         abort_unless(auth()->user()->can('invoices.view'), 403);

        $this->invoice = $invoice->load(['customer', 'items.product']);
    }

    public function render()
    {
        return view('livewire.invoices.invoice-show');
    }
}