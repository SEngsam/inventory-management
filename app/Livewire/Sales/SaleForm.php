<?php

namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaleForm extends Component
{
    public $saleId;
    public $sale;
    public $reference_no = '';
    public $sale_date = '';
    public $status = 'completed';
    public $note = '';
    public $customer_id;
    public $customers;
    public $products;
    public $items = [];

    public function mount($id = null)
    {
        $this->products = Product::all();
        $this->customers = Customer::all();

        if ($id) {
            $sale = Sale::with('items')->findOrFail($id);
            $this->saleId = $sale->id;
            $this->sale = $sale;

            $this->reference_no = $sale->reference_no;
            $this->sale_date = optional($sale->sale_date)->format('Y-m-d');
            $this->status = $sale->status;
            $this->note = $sale->note;
            $this->customer_id = $sale->customer_id;

            $this->items = $sale->items->map(fn($item) => [
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
            $ref = 'SL-' . strtoupper(Str::random(6));
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
            'reference_no' => 'required|string|unique:sales,reference_no' . ($this->saleId ? ',' . $this->saleId : ''),
            'sale_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($this->status === 'completed') {
            foreach ($this->items as $index => $item) {
                $product = Product::find($item['product_id']);
                $oldQty = 0;

                if ($this->sale) {
                    $existing = $this->sale->items->where('product_id', $item['product_id'])->first();
                    $oldQty = $existing?->quantity ?? 0;
                }

                $change = $item['quantity'] - $oldQty;
                if ($product->stock_quantity < $change) {
                    $this->addError("items.$index.quantity", 'Insufficient stock for ' . $product->name);
                    return;
                }
            }
        }

        $data = [
            'reference_no' => $this->reference_no,
            'sale_date' => $this->sale_date,
            'status' => $this->status,
            'note' => $this->note,
            'customer_id' => $this->customer_id,
        ];

        $sale = Sale::updateOrCreate(['id' => $this->saleId], $data);

        if ($this->saleId) {
            foreach ($sale->items as $oldItem) {
                Product::find($oldItem->product_id)?->increment('stock_quantity', $oldItem->quantity);
            }
            $sale->items()->delete();
        }

        foreach ($this->items as $item) {
            $sale->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);

            if ($this->status === 'completed') {
                Product::find($item['product_id'])?->decrement('stock_quantity', $item['quantity']);
            }
        }

        session()->flash('message', $this->saleId ? 'Sale updated successfully!' : 'Sale created successfully!');
        return redirect()->route('sales.index');
    }

    public function render()
    {
        return view('livewire.sales.sale-form');
    }
}
