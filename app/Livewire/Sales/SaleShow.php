<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Component;

class SaleShow extends Component
{
    public Sale $sale;

    public function mount(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function render()
    {
        return view('livewire.sales.sale-show');
    }
}
