<?php
namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\SaleReturn;

class ReturnList extends Component
{
    public function render()
    {

        return view('livewire.sales.return-list', [
            'returns' => SaleReturn::with('sale.customer')->latest()->get(),
        ]);
    }
}
