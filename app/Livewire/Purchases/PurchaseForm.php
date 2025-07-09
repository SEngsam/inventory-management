<?php

namespace App\Livewire\Purchases;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PurchaseForm extends Component
{
    public $purchase;
    public $purchaseId;
    public $supplier_id;
    public $purchase_date;
    public $reference_no;
    public $status = 'received';
    public $notes;

    public $suppliers;
    public $products;
    public $items = [];

    public function mount(?Purchase $purchase= null)
    {
        $this->suppliers = Supplier::all();
        $this->products = Product::all();

        if ($purchase && $purchase->exists) {
        $this->purchaseId = $purchase->id;

            $purchase = Purchase::with('items')->findOrFail($this->purchaseId);
            $this->fill($purchase->only('supplier_id', 'purchase_date', 'reference_no', 'status', 'notes'));

            $this->items = $purchase->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                ];
            })->toArray();
        } else {
            $this->reference_no = 'REF-' . strtoupper(Str::random(6));
            $this->purchase_date = Carbon::now()->format('Y-m-d');
            $this->items[] = ['product_id' => '', 'quantity' => 1, 'unit_cost' => 0];
        }
    }

    public function addItem()
    {
        $this->items[] = ['product_id' => '', 'quantity' => 1, 'unit_cost' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'status' => 'required|in:received,pending,ordered',
        ]);

        if (empty($this->items)) {
            $this->addError('items', 'At least one item is required.');
            return;
        }

        $totalAmount = 0;

        foreach ($this->items as $index => $item) {
            if (empty($item['product_id'])) {
                $this->addError("items.$index.product_id", 'Product is required.');
                return;
            }
            if ($item['quantity'] <= 0 || $item['unit_cost'] < 0) {
                $this->addError("items.$index.quantity", 'Quantity and cost must be positive.');
                return;
            }
            $totalAmount += $item['quantity'] * $item['unit_cost'];
        }

        $purchase = Purchase::updateOrCreate(
            ['id' => $this->purchaseId],
            [
                'supplier_id' => $this->supplier_id,
                'purchase_date' => $this->purchase_date,
                'reference_no' => $this->reference_no,
                'status' => $this->status,
                'notes' => $this->notes,
            ]
        );

        if ($this->purchaseId) {
            foreach ($purchase->items as $oldItem) {
                $oldItem->product->decrement('stock_quantity', $oldItem->quantity);
            }
            $purchase->items()->delete();
        }

        foreach ($this->items as $item) {
            $purchase->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'total' => $totalAmount,

            ]);

            $product = Product::find($item['product_id']);
            if ($product) {
                $product->increment('stock_quantity', $item['quantity']);
            }
        }

        session()->flash('message', $this->purchaseId ? 'Purchase updated!' : 'Purchase created!');
        return redirect()->route('purchase.index');
    }

    public function render()
    {
        return view('livewire.purchases.purchase-form');
    }
}
