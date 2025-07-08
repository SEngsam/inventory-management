<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Livewire\Component;
use Illuminate\Support\Str;

class ReturnForm extends Component
{
    public $sales;
    public $sale_id;
    public $return_date;
    public $reference_no;
    public $note;
    public $items = [];

    public function mount()
    {
        $this->sales = Sale::with('items.product')->get();
        $this->reference_no = 'RET-' . strtoupper(Str::random(6));
        $this->return_date = now()->format('Y-m-d');
    }

    public function updatedSaleId($saleId)
    {
        $sale = Sale::with('items.product')->find($saleId);
        if ($sale) {
            $this->items = $sale->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => 1,
                    'unit_price' => $item->unit_price,
                ];
            })->toArray();
        }
    }

    public function save()
    {
        $this->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $return = SaleReturn::create([
            'sale_id' => $this->sale_id,
            'reference_no' => $this->reference_no,
            'return_date' => $this->return_date,
            'note' => $this->note,
        ]);

        foreach ($this->items as $item) {
            SaleReturnItem::create([
                'sale_return_id' => $return->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);

            // Update stock
            Product::find($item['product_id'])?->increment('stock_quantity', $item['quantity']);
        }

        session()->flash('message', 'Return saved successfully!');
        return redirect()->route('sale-returns.create');
    }

    public function render()
    {
        return view('livewire.sales.return-form');
    }
}
