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

    protected $paginationTheme = 'bootstrap';

    public function mount(): void
    {
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedInvoices = Invoice::query()
                ->latest()
                ->paginate(10)
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedInvoices = [];
        }
    }

    public function deleteSelected(): void
    {

        if (count($this->selectedInvoices) > 0) {
            Invoice::whereIn('id', $this->selectedInvoices)->delete();

            $this->selectedInvoices = [];
            $this->selectAll = false;
            $this->resetPage();

            session()->flash('message', 'Selected Invoices deleted successfully.');
        }
    }

    public function render()
    {
        $invoices = Invoice::with('customer')
            ->latest()
            ->paginate(10);

        return view('livewire.invoices.invoice-list', compact('invoices'));
    }
}