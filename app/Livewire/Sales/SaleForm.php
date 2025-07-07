<?php
namespace App\Livewire\Sales;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaleForm extends Component
{
    public $reference_no;
    public $sale_date;
    public $status = 'completed';
    public $note;

    public $products;
    public $items = [];

    public function mount()
    {
        $this->products = Product::all();
        $this->reference_no = 'SL-' . strtoupper(Str::random(6));
        $this->sale_date = Carbon::now()->format('Y-m-d');
        $this->items = [
            ['product_id' => '', 'quantity' => 1, 'unit_price' => 0],
        ];
    }

    public function addItem()
    {
        $this->items[] = ['product_id' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        $this->validate([
            'sale_date' => 'required|date',
            'reference_no' => 'required|string|unique:sales,reference_no',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $sale = Sale::create([
            'reference_no' => $this->reference_no,
            'sale_date' => $this->sale_date,
            'status' => $this->status,
            'note' => $this->note,
        ]);

        foreach ($this->items as $item) {
            $product = Product::find($item['product_id']);
            $total = $item['quantity'] * $item['unit_price'];

            $sale->items()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $total,
            ]);

            // 🔻 Deduct stock
            $product->decrement('stock_quantity', $item['quantity']);
        }

        session()->flash('message', 'Sale created and stock updated!');
        return redirect()->route('sales.index');
    }

    public function render()
    {
        return view('livewire.sales.sale-form');
    }
}
