<?php

namespace App\Livewire\Sales;

use App\Models\Customer;
use Livewire\Component;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaleForm extends Component
{
    public $sale;
    public $reference_no = '';
    public $sale_date = '';
    public $status = 'completed';
    public $note = '';
    public $customer_id = null;
    public $customers;
    public $products;
    public $items = [];

    public function mount(?Sale $sale = null)
    {
        $this->products = Product::all();
        $this->customers = Customer::all();

        if ($sale && $sale->exists){
            $this->sale = $sale;
            $this->reference_no = $sale->reference_no;
            $this->sale_date = optional($sale->sale_date)->format('Y-m-d') ?? now()->format('Y-m-d');
            $this->status = $sale->status;
            $this->note = $sale->note;
            $this->customer_id = $sale->customer_id;

            $this->items = $sale->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];
            })->toArray();
        } else {
            $this->reference_no = $this->generateUniqueReference();
            $this->sale_date = now()->format('Y-m-d');
            $this->status = 'completed';
            $this->items = [
                ['product_id' => '', 'quantity' => 1, 'unit_price' => 0],
            ];
        }
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        if (Str::endsWith($key, '.product_id')) {
            $index = explode('.', $key)[0];
            $productId = $this->items[$index]['product_id'];

            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $this->items[$index]['unit_price'] = $product->price;
                }
            } else {
                $this->items[$index]['unit_price'] = 0;
            }
        }
    }

    protected function generateUniqueReference(): string
    {
        do {
            $ref = 'SL-' . strtoupper(Str::random(6));
        } while (Sale::where('reference_no', $ref)->exists());

        return $ref;
    }

    public function save(): void
    {
        $rules = [
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'reference_no' => 'required|string|unique:sales,reference_no' . ($this->sale ? ',' . $this->sale->id : ''),
            'status' => 'required|in:pending,completed,cancelled',
        ];

        $messages = [
            'items.required' => 'At least one item is required for the sale.',
            'items.*.product_id.required' => 'Please select a product for each item.',
            'items.*.product_id.exists' => 'The selected product does not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be a whole number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.unit_price.required' => 'Unit price is required for each item.',
            'items.*.unit_price.numeric' => 'Unit price must be a number.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
            'reference_no.unique' => 'This reference number is already in use. Please try again.',
        ];

        $this->validate($rules, $messages);

        if ($this->status === 'completed') {
            foreach ($this->items as $index => $item) {
                $product = Product::find($item['product_id']);
                $oldQuantity = 0;
                if ($this->sale) {
                    $oldItem = $this->sale->items->where('product_id', $item['product_id'])->first();
                    $oldQuantity = $oldItem ? $oldItem->quantity : 0;
                }

                $netQuantityChange = $item['quantity'] - $oldQuantity;

                if ($product->stock_quantity < $netQuantityChange) {
                    $this->addError("items.{$index}.quantity", 'Insufficient stock for ' . $product->name . '. Available: ' . $product->stock_quantity);
                    return;
                }
            }
        }

        if (!$this->sale) {
            $sale = Sale::create([
                'reference_no' => $this->reference_no,
                'sale_date' => $this->sale_date,
                'status' => $this->status,
                'note' => $this->note,
                'customer_id' => $this->customer_id,
            ]);
            $this->sale = $sale;
        } else {
            foreach ($this->sale->items as $oldItem) {
                Product::find($oldItem->product_id)?->increment('stock_quantity', $oldItem->quantity);
            }

            $this->sale->update([
                'sale_date' => $this->sale_date,
                'status' => $this->status,
                'note' => $this->note,
                'customer_id' => $this->customer_id,
            ]);

            $this->sale->items()->delete();
        }

        $sale = $this->sale->fresh();

        foreach ($this->items as $item) {
            $product = Product::find($item['product_id']);

            $sale->items()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);

            if ($sale->status === 'completed') {
                $product->decrement('stock_quantity', $item['quantity']);
            }
        }

        session()->flash('message', $sale->wasRecentlyCreated
            ? 'Sale created successfully!'
            : 'Sale updated and stock adjusted!');

        $this->redirectRoute('sales.index');
    }

    public function render()
    {
        return view('livewire.sales.sale-form');
    }
}
