<?php

namespace App\Livewire\Sales;

use App\Livewire\BaseComponent;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Illuminate\Support\Str;

class ReturnForm extends BaseComponent
{
    protected string $module = 'sale-returns';

    public $sales;
    public $sale_id;
    public $return_date;
    public $reference_no;
    public $note;
    public $items = [];

    public function mount()
    {
        $this->authorizeAction('create');

        $this->sales = Sale::with('items.product')->get();
        $this->reference_no = 'RET-' . strtoupper(Str::random(6));
        $this->return_date = now()->format('Y-m-d');
    }

    public function updatedSaleId($saleId)
    {
        $this->authorizeAction('create');

        $sale = Sale::with('items.product')->find($saleId);

        if ($sale) {
            $this->items = $sale->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => 1,
                    'max_quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                ];
            })->toArray();
        } else {
            $this->items = [];
        }
    }

    public function save()
    {
        $this->authorizeAction('create');

        $this->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $sale = Sale::with('items')->find($this->sale_id);
        if (!$sale) {
            session()->flash('error', 'Selected sale not found.');
            return;
        }

        $soldQtyByProduct = $sale->items
            ->groupBy('product_id')
            ->map(fn ($rows) => (int) $rows->sum('quantity'));

        foreach ($this->items as $row) {
            $pid = (int) $row['product_id'];
            $qty = (int) $row['quantity'];

            $max = (int) ($soldQtyByProduct[$pid] ?? 0);
            if ($max <= 0) {
                session()->flash('error', 'Invalid return items.');
                return;
            }

            if ($qty > $max) {
                session()->flash('error', 'Return quantity cannot exceed sold quantity.');
                return;
            }
        }

        $total = 0;
        foreach ($this->items as $item) {
            $total += (int) $item['quantity'] * (float) $item['unit_price'];
        }

        $return = SaleReturn::create([
            'sale_id' => $this->sale_id,
            'customer_id' => $sale->customer_id,
            'reference_no' => $this->reference_no,
            'return_date' => $this->return_date,
            'note' => $this->note,
            'total' => $total,
        ]);

        $products = Product::whereIn('id', collect($this->items)->pluck('product_id'))
            ->get()
            ->keyBy('id');

        foreach ($this->items as $item) {
            SaleReturnItem::create([
                'sale_return_id' => $return->id,
                'product_id' => $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'total' => (int) $item['quantity'] * (float) $item['unit_price'],
            ]);

            $pid = (int) $item['product_id'];
            if (isset($products[$pid])) {
                $products[$pid]->increment('stock_quantity', (int) $item['quantity']);
            }
        }

        session()->flash('message', 'Return saved successfully!');
        return redirect()->route('sale-returns.index');
    }

    public function render()
    {
        return view('livewire.sales.return-form');
    }
}