<?php

namespace App\Livewire\Sales;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class SaleForm extends Component
{
    public $saleId;

    public $reference_no = '';

    public $sale_date = '';

    public $status = 'completed';

    public $note = '';

    public $customer_id;

    public $items = [];

    public $products;

    public $customers;

    public function mount($id = null)
    {
        $this->products = Product::all();
        $this->customers = Customer::all();

        if ($id) {
            $sale = Sale::with('items')->findOrFail($id);
            $this->saleId = $sale->id;

            $this->reference_no = $sale->reference_no;
            $this->sale_date = optional($sale->sale_date)->format('Y-m-d');
            $this->status = $sale->status;
            $this->note = $sale->note;
            $this->customer_id = $sale->customer_id;

            $this->items = $sale->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ])->toArray();
        } else {
            $this->reference_no = $this->generateReference();
            $this->sale_date = Carbon::now()->format('Y-m-d');
            $this->items[] = ['product_id' => '', 'quantity' => 1, 'unit_price' => 0];
        }
    }

    protected function generateReference(): string
    {
        do {
            $ref = 'SL-'.strtoupper(Str::random(6));
        } while (Sale::where('reference_no', $ref)->exists());

        return $ref;
    }

    public function addItem()
    {
        $this->items[] = ['product_id' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        if (Str::endsWith($key, '.product_id')) {
            $index = explode('.', $key)[0];
            $productId = $this->items[$index]['product_id'];
            $product = Product::find($productId);
            $this->items[$index]['unit_price'] = $product?->price ?? 0;
        }
    }

    public function save()
    {
        $this->validate([
            'reference_no' => 'required|string|unique:sales,reference_no'.($this->saleId ? ','.$this->saleId : ''),
            'sale_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $sale = $this->saleId ? Sale::findOrFail($this->saleId) : null;

        app(SaleService::class)->save([
            'reference_no' => $this->reference_no,
            'sale_date' => $this->sale_date,
            'status' => $this->status,
            'note' => $this->note,
            'customer_id' => $this->customer_id ?: null,
        ], $this->items, $sale);

        session()->flash('message', __('messages.sale.saved'));

        return redirect()->route('sales.index');
    }

    public function render()
    {
        return view('livewire.sales.sale-form');
    }
}
