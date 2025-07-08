<?php

namespace App\Livewire\Sales;

use App\Models\SaleReturn;
use Livewire\Component;

class ReturnList extends Component
{
    public function render()
    {
        return view('livewire.sales.return-list', [
            'returns' => SaleReturn::with('sale.customer')->latest()->get(),
        ]);
    }
}
