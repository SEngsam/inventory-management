<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class ProductForm extends Component
{
    use WithFileUploads;

    public $productId;
    public $name, $price, $description;
    public $order_tax, $tax_type = 'percent';
    public $stock_quantity, $threshold_stock = 5;
    public $image, $new_product_image;
    public $category_id, $brand_id;
    public $warranty_period, $guarantee = false, $guarantee_period;
    public $has_imei = false;

    public $categories = [], $brands = [];

    public function mount($id = null)
    {
        $this->categories = Category::all();
        $this->brands = Brand::all();

        if ($id) {
            $product = Product::findOrFail($id);

            $this->productId = $product->id;
            $this->name = $product->name;
            $this->price = $product->price;
            $this->description = $product->description;
            $this->order_tax = $product->order_tax;
            $this->tax_type = $product->tax_type;
            $this->stock_quantity = $product->stock_quantity;
            $this->threshold_stock = $product->threshold_stock;
            $this->image = $product->image;
            $this->category_id = $product->category_id;
            $this->brand_id = $product->brand_id;
            $this->warranty_period = $product->warranty_period;
            $this->guarantee = $product->guarantee;
            $this->guarantee_period = $product->guarantee_period;
            $this->has_imei = $product->has_imei;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'order_tax' => 'nullable|numeric',
            'tax_type' => 'required|in:percent,fixed',
            'new_product_image' => $this->productId ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $imagePath = $this->image;
        if ($this->new_product_image) {
            $imagePath = $this->new_product_image->store('product_images', 'public');
        }

        $data = [
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'order_tax' => $this->order_tax,
            'tax_type' => $this->tax_type,
            'stock_quantity' => $this->stock_quantity,
            'threshold_stock' => $this->threshold_stock,
            'image' => $imagePath,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'warranty_period' => $this->warranty_period,
            'guarantee' => $this->guarantee,
            'guarantee_period' => $this->guarantee_period,
            'has_imei' => $this->has_imei,
        ];

        Product::updateOrCreate(['id' => $this->productId], $data);

        session()->flash('message', $this->productId ? 'Product updated successfully.' : 'Product created successfully.');
        return redirect()->route('products.list');
    }

    public function render()
    {
        return view('livewire.products.product-form');
    }
}
