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

    protected $paginationTheme = 'bootstrap'; // لو تستخدم Bootstrap

    public function updatedSelectAll($value)
    {
        if ($value) {
            // نجيب الـ IDs للمنتجات الموجودة فقط في الصفحة الحالية
            $this->selectedProducts = Product::latest()->paginate(10)->pluck('id')->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }

    public function deleteSelected()
    {
        $products = Product::whereIn('id', $this->selectedProducts)->get();

        foreach ($products as $product) {
            // حذف الصورة من التخزين
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }
            // حذف المنتج
            $product->delete();
        }

        $this->selectedProducts = [];
        $this->selectAll = false;
        $this->resetPage(); // العودة للصفحة الأولى بعد الحذف

        session()->flash('message', 'Selected products deleted successfully.');
    }

    public function render()
    {
        // جلب المنتجات مع العلاقات، مع استخدام pagination
        $products = Product::with(['category', 'brand'])->latest()->paginate(10);
        return view('livewire.products.product-list', compact('products'));
    }
}
