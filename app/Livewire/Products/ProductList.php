<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductList extends Component
{
    use WithPagination;

    public $selectedProducts = [];
    public $selectAll = false;

    protected $paginationTheme = 'bootstrap';

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProducts = Product::latest()->paginate(10)->pluck('id')->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function deleteSelected()
    {
        $products = Product::whereIn('id', $this->selectedProducts)->get();

        foreach ($products as $product) {
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }
            $product->delete();
        }

        $this->selectedProducts = [];
        $this->selectAll = false;
        $this->resetPage();

        session()->flash('message', 'Selected products deleted successfully.');
    }

    public function render()
    {
        $products = Product::with(['category', 'brand'])->latest()->paginate(10);
        return view('livewire.products.product-list', compact('products'));
    }
}
