<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductList extends Component
{
    public $products;
    public $title = 'Product';
    public $selectedProducts = [];
    public $selectAll = false;


    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
{
        $this->products = Product::with(['category', 'brand'])->latest()->get();

}

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProducts = Product::pluck('id')->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function deleteSelected()
    {
        $products = Product::whereIn('id', $this->selectedProducts)->get();

        foreach ($products as $product) {
            // Delete the image if it exists
            if ($product->product_image && file_exists(public_path('storage/' . $product->product_image))) {
                unlink(public_path('storage/' . $product->product_image));
            }

            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }

            // Delete the product
            $product->delete();
        }

        // Reset selection
        $this->selectedProducts = [];
        $this->selectAll = false;
        $this->loadProducts();

        session()->flash('message', 'Selected products deleted successfully.');
    }

    public function render()
    {
        return view('livewire.product-list',);
    }

    // public function deleteProduct($id)
    // {
    //     $product = Product::findOrFail($id);
    //     // Delete image file if exists - optional
    //     if ($product->product_image && file_exists(public_path('storage/' . $product->product_image))) {
    //         unlink(public_path('storage/' . $product->product_image));
    //     }
    //     $product->delete();

    //     // $this->products = Product::with(['category', 'brand'])->latest()->get();

    //     // session()->flash('message', 'Product deleted successfully.');
    // }
}
