<?php

namespace App\Livewire\Purchases;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
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

    public function save(PurchaseService $purchases)
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'reference_no' => 'required|string|unique:purchases,reference_no'.($this->purchaseId ? ','.$this->purchaseId : ''),
            'status' => 'required|in:received,pending,ordered',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $purchases->save([
            'supplier_id' => $this->supplier_id,
            'purchase_date' => $this->purchase_date,
            'reference_no' => $this->reference_no,
            'status' => $this->status,
            'notes' => $this->notes,
        ], $this->items, $this->purchaseId ? Purchase::findOrFail($this->purchaseId) : null);

        session()->flash('message', $this->purchaseId ? __('messages.purchase.updated') : __('messages.purchase.created'));

        return redirect()->route('purchases.index');
    }

    public function render()
    {
        return view('livewire.purchases.purchase-form');
    }
}
