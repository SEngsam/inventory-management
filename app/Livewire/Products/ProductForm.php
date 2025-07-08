<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Unit;

class ProductForm extends Component
{
    use WithFileUploads;

    public $productId;
    public $name, $type, $code, $product_image, $new_product_image;
    public $category_id, $brand_id, $unit_id;
    public $order_tax, $tax_type = 'percent';
    public $description, $product_price;
    public $warranty_period, $guarantee, $guarantee_period;
    public $has_imei = false, $not_for_selling = false;

    public $categories = [], $brands = [], $units = [];

    public function mount($productId = null)
    {
        $this->categories = Category::all();
        $this->brands = Brand::all();
        $this->units = Unit::all();

        if ($productId) {
            $product = Product::findOrFail($productId);
            $this->productId = $product->id;
            $this->name = $product->name;
            $this->type = $product->type;
            $this->code = $product->code;
            $this->product_image = $product->product_image;
            $this->category_id = $product->category_id;
            $this->brand_id = $product->brand_id;
            $this->unit_id = $product->unit_id;
            $this->order_tax = $product->order_tax;
            $this->tax_type = $product->tax_type;
            $this->description = $product->description;
            $this->product_price = $product->product_price;
            $this->warranty_period = $product->warranty_period;
            $this->guarantee = $product->guarantee;
            $this->guarantee_period = $product->guarantee_period;
            $this->has_imei = $product->has_imei;
            $this->not_for_selling = $product->not_for_selling;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'order_tax' => 'nullable|numeric',
            'tax_type' => 'required|in:percent,fixed',
            'product_price' => 'required|numeric',
            'new_product_image' => $this->productId ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $imagePath = $this->product_image;

        if ($this->new_product_image) {
            $imagePath = $this->new_product_image->store('product_images', 'public');
        }

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'unit_id' => $this->unit_id,
            'order_tax' => $this->order_tax,
            'tax_type' => $this->tax_type,
            'description' => $this->description,
            'product_price' => $this->product_price,
            'warranty_period' => $this->warranty_period,
            'guarantee' => $this->guarantee,
            'guarantee_period' => $this->guarantee_period,
            'has_imei' => $this->has_imei,
            'not_for_selling' => $this->not_for_selling,
            'product_image' => $imagePath,
        ];

        Product::updateOrCreate(['id' => $this->productId], $data);

        session()->flash('message', $this->productId ? 'Product updated successfully.' : 'Product created successfully.');
        return redirect()->route('product.list');
    }

    public function render()
    {
        return view('livewire.products.product-form');
    }
}
