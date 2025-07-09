<?php
namespace App\Livewire\Purchases;

use Livewire\Component;
use App\Models\Purchase;

class PurchaseShow extends Component
{
    public $purchaseId;
    public $purchase;

    public function mount($purchaseId)
    {
        $this->purchaseId = $purchaseId;
        $this->purchase = Purchase::with(['supplier', 'items.product'])->find($purchaseId);

        if (!$this->purchase) {
            abort(404, 'Purchase not found');
        }
    }

    public function render()
    {
        return view('livewire.purchases.purchase-show', [
            'purchase' => $this->purchase,
        ]);
    }
}
