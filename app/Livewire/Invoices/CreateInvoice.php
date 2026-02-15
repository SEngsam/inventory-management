<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Validation\ValidationException;

class CreateInvoice extends Component
{
    public $customers;
    public $products;
    public $selectedCustomer;
    public $items = [];

    public function mount(): void
    {
        $this->customers = Customer::query()->orderBy('name')->get();
        $this->products  = Product::query()->orderBy('name')->get();

        $this->items[] = ['product_id' => null, 'quantity' => 1];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'quantity' => 1];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);

        if (count($this->items) === 0) {
            $this->items[] = ['product_id' => null, 'quantity' => 1];
        }
    }

    protected function rules(): array
    {
        return [
            'selectedCustomer' => ['required', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
        ];
    }

    public function createInvoice()
    {
        $this->validate();

        $customer = Customer::findOrFail($this->selectedCustomer);

        $merged = collect($this->items)
            ->filter(fn ($row) => !empty($row['product_id']))
            ->groupBy('product_id')
            ->map(function ($rows) {
                return (int) $rows->sum(fn ($r) => (int)($r['quantity'] ?? 1));
            });

        if ($merged->isEmpty()) {
            $this->addError('items', 'Please select at least one product.');
            return;
        }

        $productIds = $merged->keys()->values();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $lines = [];
        $subtotal = 0.0;
        $taxTotal = 0.0;

        foreach ($merged as $pid => $qty) {
            $product = $products->get((int)$pid);

            if (!$product) {
                throw ValidationException::withMessages([
                    'items' => 'One or more selected products were not found.',
                ]);
            }

            if ((int)$product->stock_quantity < (int)$qty) {
                throw ValidationException::withMessages([
                    'items' => "Insufficient stock for {$product->name}. Available: {$product->stock_quantity}, Requested: {$qty}",
                ]);
            }

            $unitPrice = (float) $product->price;
            $lineSub = $unitPrice * $qty;

            $taxValue = $product->tax_value !== null ? (float) $product->tax_value : null;
            $taxType  = $product->tax_type ?? 'percent';

            $lineTax = 0.0;
            if ($taxValue !== null) {
                $lineTax = $taxType === 'percent'
                    ? ($lineSub * ($taxValue / 100))
                    : ($taxValue * $qty);  
            }

            $lineTotal = $lineSub + $lineTax;

            $subtotal += $lineSub;
            $taxTotal += $lineTax;

            $lines[] = [
                'product_id' => (int) $product->id,
                'quantity'   => (int) $qty,

                'unit_price' => round($unitPrice, 2),
                'tax_value'  => $taxValue !== null ? round($taxValue, 2) : null,
                'tax_type'   => $taxType,

                'discount'   => 0,  

                'line_tax'   => round($lineTax, 2),
                'line_total' => round($lineTotal, 2),
            ];
        }

        $payload = [
            'customer_id' => $customer->id,
            'issued_at'   => now(),
            'status'      => 'issued',
   
        ];

        $invoice = app(InvoiceService::class)->createFromSale($payload, $lines);

        return redirect()->route('invoices.show', $invoice->id);
    }

    public function render()
    {
        return view('livewire.invoices.create-invoice');
    }
}
