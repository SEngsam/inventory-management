<?php

namespace App\Livewire\Purchases;

use Livewire\Component;
use App\Models\Purchase;

class PurchaseList extends Component
{
    public $purchases;

    public function mount()
    {
        $this->loadPurchases();
    }

    public function loadPurchases()
    {
        // Load all purchases, eager load supplier to avoid N+1
        $this->purchases = Purchase::with('supplier')->orderBy('purchase_date', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.purchases.purchase-list');
    }
}
