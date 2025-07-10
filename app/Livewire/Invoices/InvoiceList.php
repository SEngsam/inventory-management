<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;
    public $selectedInvoices = [];
    public $selectAll = false;


    public function deleteSelected()
    {
        if (count($this->selectedInvoices) > 0) {
            Invoice::whereIn('id', $this->selectedInvoices)->delete();
            $this->selectedInvoices = [];
            $this->selectAll = false;
            $this->resetPage();
            session()->flash('message', 'Selected Invoices deleted successfully.');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedInvoices = Invoice::latest()->paginate(10)->pluck('id')->toArray();
        } else {
            $this->selectedInvoices = [];
        }
    }

    public function render()
    {
        $invoices = Invoice::with('customer')->latest()->get();
        return view('livewire.invoices.invoice-list', compact('invoices'));
    }
}
