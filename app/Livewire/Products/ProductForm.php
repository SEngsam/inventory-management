<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;

class ProductForm extends Component
{
    use WithFileUploads;

    public $productId;

    public $name, $sku, $code, $type = 'standard';
    public $price, $description, $tax_value, $tax_type = 'percent';
    public $stock_quantity = 0, $category_id, $brand_id, $unit_id;
    public $warranty_period, $has_warranty = false;
    public $has_imei = false, $image, $new_product_image;

    public $categories = [];
    public $brands = [];
    public $units = [];

    public function mount($product = null): void
    {
         $this->productId = $product;

 

        $this->categories = Category::all();
        $this->brands = Brand::all();
        $this->units = Unit::all();

        if ($this->productId) {
            $p = Product::findOrFail($this->productId);
            $this->fill($p->toArray());
            $this->image = $p->image; 
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $this->productId,
            'code' => 'nullable|string|max:255',
            'type' => 'required|in:standard,service',
            'price' => 'required|numeric|min:0',
            'tax_value' => 'nullable|numeric|min:0',
            'tax_type' => 'required|in:percent,fixed',
            'stock_quantity' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'warranty_period' => 'nullable|string',
            'has_warranty' => 'boolean',
         
            'has_imei' => 'boolean',
            'description' => 'nullable|string',
            'new_product_image' => 'nullable|image|max:2048',
        ];
    }

    public function save()
    {
      
        $this->validate();

        $data = $this->only([
            'name', 'sku', 'code', 'type', 'price', 'description',
            'tax_value', 'tax_type', 'stock_quantity', 'category_id',
            'brand_id', 'unit_id', 'warranty_period', 'has_warranty',
             'has_imei',
        ]);

        $data['has_warranty'] = $this->has_warranty ? 1 : 0;
        $data['has_imei'] = $this->has_imei ? 1 : 0;

        if ($this->new_product_image) {
            if ($this->productId) {
                $old = Product::find($this->productId);
                if ($old && $old->image && Storage::disk('public')->exists($old->image)) {
                    Storage::disk('public')->delete($old->image);
                }
            }

            $data['image'] = $this->new_product_image->store('product_images', 'public');
        }

        Product::updateOrCreate(['id' => $this->productId], $data);

        session()->flash('message', $this->productId ? 'Product updated successfully!' : 'Product created successfully!');

        return redirect()->route('products.list');
    }

    public function render()
    {
        return view('livewire.products.product-form');
    }
}