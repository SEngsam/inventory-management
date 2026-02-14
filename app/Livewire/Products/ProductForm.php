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
    public $price, $description, $order_tax, $tax_type = 'percent';
    public $stock_quantity = 0, $category_id, $brand_id, $unit_id;
    public $warranty_period, $guarantee = false, $guarantee_period;
    public $has_imei = false, $image, $new_product_image;

    public $categories = [];
    public $brands = [];
    public $units = [];

    public function mount($product = null): void
    {
         $this->productId = $product;

        if ($this->productId) {
            abort_unless(auth()->user()->can('products.update'), 403);
        } else {
            abort_unless(auth()->user()->can('products.create'), 403);
        }

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
            'order_tax' => 'nullable|numeric|min:0',
            'tax_type' => 'required|in:percent,fixed',
            'stock_quantity' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'warranty_period' => 'nullable|string',
            'guarantee' => 'boolean',
            'guarantee_period' => 'nullable|string',
            'has_imei' => 'boolean',
            'description' => 'nullable|string',
            'new_product_image' => 'nullable|image|max:2048',
        ];
    }

    public function save()
    {
        if ($this->productId) {
            abort_unless(auth()->user()->can('products.update'), 403);
        } else {
            abort_unless(auth()->user()->can('products.create'), 403);
        }

        $this->validate();

        $data = $this->only([
            'name', 'sku', 'code', 'type', 'price', 'description',
            'order_tax', 'tax_type', 'stock_quantity', 'category_id',
            'brand_id', 'unit_id', 'warranty_period', 'guarantee',
            'guarantee_period', 'has_imei',
        ]);

        $data['guarantee'] = $this->guarantee ? 1 : 0;
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