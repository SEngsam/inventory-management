<?php

namespace App\Livewire\Sales;

use App\Models\SaleReturn;
use Livewire\Component;

class ReturnShow extends Component
{
    public SaleReturn $return;

    public function mount(SaleReturn $return)
    {

        $this->return = $return->load('sale.customer', 'items.product');

    }

    public function render()
    {
        return view('livewire.sales.return-show');
    }
}
