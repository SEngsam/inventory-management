<?php

namespace App\Livewire\Purchases;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;

class PurchaseForm extends Component
{
    public $purchaseId;

    public $supplier_id;
    public $purchase_date;
    public $reference_no;
    public $status = 'received';
    public $note;

    public $suppliers;
    public $products;
    public $items = [];

    public function mount($purchaseId = null)
    {
        $this->suppliers = Supplier::all();
        $this->products = Product::all();

        $this->purchaseId = $purchaseId;

        if ($purchaseId) {
            $purchase = Purchase::with('items')->find($purchaseId);

            if ($purchase) {
                $this->fill($purchase->only('supplier_id', 'purchase_date', 'reference_no', 'status', 'note'));

                $this->items = $purchase->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_cost' => $item->unit_cost,
                    ];
                })->toArray();
            } else {
                $this->initEmptyItem();
            }
        } else {
            $this->initEmptyItem();
        }
    }

    protected function initEmptyItem()
    {
        $this->items = [
            ['product_id' => '', 'quantity' => 1, 'unit_cost' => 0],
        ];
    }

    public function addItem()
    {
        $this->items[] = ['product_id' => '', 'quantity' => 1, 'unit_cost' => 0];
    }

    public function removeItem($index)
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function save()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'reference_no' => 'required|string',
            'status' => 'required|in:received,pending,ordered',
            'note' => 'nullable|string',
        ]);

        // Validate items array is not empty
        if (empty($this->items)) {
            $this->addError('items', 'You must add at least one purchase item.');
            return;
        }

        // Validate each item
        foreach ($this->items as $index => $item) {
            if (empty($item['product_id'])) {
                $this->addError("items.$index.product_id", 'Product is required.');
                return;
            }
            if (empty($item['quantity']) || $item['quantity'] <= 0) {
                $this->addError("items.$index.quantity", 'Quantity must be at least 1.');
                return;
            }
            if (!isset($item['unit_cost']) || $item['unit_cost'] <= 0) {
                $this->addError("items.$index.unit_cost", 'Unit cost must be greater than zero.');
                return;
            }
        }

        // Save purchase
        $purchase = Purchase::updateOrCreate(
            ['id' => $this->purchaseId],
            [
                'supplier_id' => $this->supplier_id,
                'purchase_date' => $this->purchase_date,
                'reference_no' => $this->reference_no,
                'status' => $this->status,
                'note' => $this->note,
            ]
        );


        // Delete old items (if editing)
        if ($this->purchaseId) {
            $purchase->items()->delete();
        }

        foreach ($this->items as $item) {
            $purchase->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'total' => $item['quantity'] * $item['unit_cost'],
            ]);
        }
        $product = Product::find($item['product_id']);
        if ($product) {
            $product->increment('stock_quantity', $item['quantity']);
        }

        if ($this->purchaseId) {
            foreach ($purchase->items as $oldItem) {
                $oldItem->product->decrement('stock_quantity', $oldItem->quantity);
            }

            $purchase->items()->delete();
        }

        session()->flash('message', $this->purchaseId ? 'Purchase updated!' : 'Purchase created!');
        return redirect()->route('purchases.index');
    }

    public function render()
    {
        return view('livewire.purchases.purchase-form');
    }
}
