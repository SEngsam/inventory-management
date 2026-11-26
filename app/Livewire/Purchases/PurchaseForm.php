<?php

namespace App\Livewire\Purchases;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

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

    public function mount($id = null)
    {
        $this->suppliers = Supplier::all();
        $this->products = Product::all();

        if ($id) {
            $purchase = Purchase::with('items')->findOrFail($id);
            // Editing an existing purchase
            $this->purchaseId = $purchase->id;
            $this->fill($purchase->only('supplier_id', 'purchase_date', 'reference_no', 'status', 'notes'));
            $this->items = $purchase->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                ];
            })->toArray();
        } else {
            // Creating a new purchase
            $this->reference_no = 'REF-'.strtoupper(Str::random(6));
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
            $this->addError('items', __('messages.items.required'));

            return;
        }

        $totalAmount = 0;

        foreach ($this->items as $index => $item) {
            if (empty($item['product_id'])) {
                $this->addError("items.$index.product_id", __('messages.items.product_required'));

                return;
            }
            if ($item['quantity'] <= 0 || $item['unit_cost'] < 0) {
                $this->addError("items.$index.quantity", __('messages.items.quantity_cost_positive'));

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
            // Reverse the stock added by the old items before saving the new ones
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
                'total' => $item['quantity'] * $item['unit_cost'],
            ]);

            $product = Product::find($item['product_id']);
            if ($product) {
                $product->increment('stock_quantity', $item['quantity']);
            }
        }

        session()->flash('message', $this->purchaseId ? __('messages.purchase.updated') : __('messages.purchase.created'));

        return redirect()->route('purchases.index');
    }

    public function render()
    {
        return view('livewire.purchases.purchase-form');
    }
}
