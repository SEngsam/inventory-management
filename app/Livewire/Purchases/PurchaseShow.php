<?php
namespace App\Livewire\Purchases;

use Livewire\Component;
use App\Models\Purchase;

class PurchaseShow extends Component
{
    public $purchase;

    public function mount($purchaseId)
    {
        $this->purchase = Purchase::with(['supplier', 'items.product'])->findOrFail($purchaseId);
    }

    public function render()
    {
        return view('livewire.purchases.purchase-show');
    }
}

