<?php
namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Sale;

class SaleList extends Component
{
    public $sales;

    public function mount()
    {
        $this->loadSales();
    }

    public function loadSales()
    {
        $this->sales = Sale::withCount('items')->latest()->get();
    }

    public function render()
    {
        return view('livewire.sales.sale-list');
    }
}
