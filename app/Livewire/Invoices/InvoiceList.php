<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;

    public $selectedInvoices = [];

    public $selectAll = false;

    protected $paginationTheme = 'bootstrap';

    public function mount(): void {}

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

    public function deleteSelected(InvoiceService $invoices): void
    {
        if (count($this->selectedInvoices) > 0) {
            try {
                DB::transaction(function () use ($invoices) {
                    Invoice::whereIn('id', $this->selectedInvoices)->get()
                        ->each(fn (Invoice $invoice) => $invoices->delete($invoice));
                });
            } catch (ValidationException $e) {
                session()->flash('error', collect($e->errors())->flatten()->first());

                return;
            }

            $this->selectedInvoices = [];
            $this->selectAll = false;
            $this->resetPage();

            session()->flash('message', __('messages.invoice.bulk_deleted'));
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
