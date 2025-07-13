<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturnItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DashboardPanel extends Component
{
    public int $productCount = 0;
    public int $invoiceCount = 0;
    public int $customerCount = 0;
    public float $salesTotal = 0;
    public float $returnsTotal = 0;
    public $recentSales;

    public $monthlySales = [];
    public $topSellingProducts;
    public $recentReturns;

    public function mount()
    {

        $this->productCount = Product::count();
        $this->invoiceCount = Invoice::count();
        $this->customerCount = Customer::count();
        $this->salesTotal = SaleItem::sum('total');
        $this->returnsTotal = SaleReturnItem::sum('total');

        $this->monthlySales = Sale::with('items')
            ->whereYear('sale_date', now()->year)
            ->get()
            ->groupBy(function ($sale) {
                return $sale->sale_date->format('m');
            })
            ->map(function ($sales) {
                return $sales->flatMap->items->sum(function ($item) {
                    return $item->unit_price * $item->quantity;
                });
            })
            ->toArray();
        $this->topSellingProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(7)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'N/A',
                    'quantity' => $item->total_quantity,
                ];
            })
            ->toArray();

        $this->recentSales = Sale::with('customer')
            ->latest('sale_date')
            ->take(10)
            ->get();

               $this->recentReturns = \App\Models\SaleReturn::with('customer')
        ->latest('return_date')
        ->take(10)
        ->get();
    }



    public function render()
    {
        return view('livewire.dashboard-panel');
    }
}
