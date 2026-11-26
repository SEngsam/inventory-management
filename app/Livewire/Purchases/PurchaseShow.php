<?php

namespace App\Livewire\Purchases;

use App\Models\Purchase;
use Livewire\Component;

class PurchaseShow extends Component
{
    public $purchaseId;

    public $purchase;

    public function mount($purchaseId)
    {
        $this->purchaseId = $purchaseId;
        $this->purchase = Purchase::with(['supplier', 'items.product'])->find($purchaseId);

        if (! $this->purchase) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.purchases.purchase-show', [
            'purchase' => $this->purchase,
        ]);
    }
}
