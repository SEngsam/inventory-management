<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\SaleItem;
use App\Models\SaleReturnItem;
use Livewire\Component;

class DashboardPanel extends Component
{
    public int $productCount = 0;
    public int $invoiceCount = 0;
    public int $customerCount = 0;
    public float $salesTotal = 0;
    public float $returnsTotal = 0;

    public function mount()
    {
        $this->productCount = Product::count();
        $this->invoiceCount = Invoice::count();
        $this->customerCount = Customer::count();
        $this->salesTotal = SaleItem::sum('total');
        $this->returnsTotal = SaleReturnItem::sum('total');
    }



    public function render()
    {
        return view('livewire.dashboard-panel');
    }
}
