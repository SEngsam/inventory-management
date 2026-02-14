<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\InventoryService;
use App\Models\Product;

class Transaction extends Component
{
    public $productId;
    public $type;       // 'purchase' أو 'sale'
    public $quantity;
    public $message;

    private $inventoryService;

    public function boot(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function submit()
    {
        $this->validate([
            'productId' => 'required|exists:products,id',
            'type'      => 'required|in:purchase,sale',
            'quantity'  => 'required|integer|min:1',
        ]);

        try {
            $tx = $this->inventoryService->process(
                $this->productId, $this->type, $this->quantity
            );
            $this->message = "تمت العملية بنجاح. المعاملة رقم: {$tx->id}";
            $this->reset(['quantity', 'message']);
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.transaction', [
            'products' => Product::all()
        ]);
    }
}
