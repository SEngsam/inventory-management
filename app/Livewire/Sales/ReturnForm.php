<?php

namespace App\Livewire\Sales;

use App\Livewire\BaseComponent;
use App\Models\Sale;
use App\Services\SaleReturnService;
use Illuminate\Support\Str;

class ReturnForm extends BaseComponent
{
    protected string $module = 'sale-returns';

    public $sales;

    public $sale_id;

    public $return_date;

    public $reference_no;

    public $note;

    /**
     * One row per product on the selected sale.
     *
     * @var list<array{product_id: int, product_name: string, quantity: int, max_quantity: int, unit_price: float}>
     */
    public $items = [];

    public function mount()
    {
        $this->authorizeAction('create');

        // Only completed sales took stock out, so only they can be returned.
        $this->sales = Sale::with('customer')
            ->where('status', Sale::STATUS_COMPLETED)
            ->latest('sale_date')
            ->get();

        $this->reference_no = 'RET-'.strtoupper(Str::random(6));
        $this->return_date = now()->format('Y-m-d');
    }

    public function updatedSaleId($saleId, SaleReturnService $returns)
    {
        $this->authorizeAction('create');
        $this->items = [];

        $sale = Sale::with('items.product')->find($saleId);

        if (! $sale) {
            return;
        }

        $returnable = $returns->returnableQuantities($sale);

        $this->items = $sale->items
            ->groupBy('product_id')
            ->map(fn ($rows, $productId) => [
                'product_id' => (int) $productId,
                'product_name' => $rows->first()->product->name,
                'quantity' => 0,
                'max_quantity' => $returnable[$productId] ?? 0,
                'unit_price' => round((float) $rows->avg('unit_price'), 2),
            ])
            ->filter(fn ($row) => $row['max_quantity'] > 0)
            ->values()
            ->all();
    }

    public function save(SaleReturnService $returns)
    {
        $this->authorizeAction('create');

        $this->validate([
            'sale_id' => 'required|exists:sales,id',
            'reference_no' => 'required|string|unique:sale_returns,reference_no',
            'return_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        $returns->create(
            Sale::findOrFail($this->sale_id),
            [
                'reference_no' => $this->reference_no,
                'return_date' => $this->return_date,
                'note' => $this->note,
            ],
            collect($this->items)->pluck('quantity', 'product_id')->all(),
        );

        session()->flash('message', __('messages.return.saved'));

        return redirect()->route('sale-returns.index');
    }

    public function render()
    {
        return view('livewire.sales.return-form');
    }
}
