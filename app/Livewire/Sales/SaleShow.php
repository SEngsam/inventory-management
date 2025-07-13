<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Component;

class SaleShow extends Component
{
    public $id;
    public $sale;

    public function mount()
    {
        $this->sale = Sale::with('customer', 'items.product')->findOrFail($this->id);
    }

    public function render()
    {
        return view('livewire.sales.sale-show');
    }
}
