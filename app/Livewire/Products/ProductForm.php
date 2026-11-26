<?php

namespace App\Livewire\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public $productId;

    public $name;

    public $sku;

    public $code;

    public $type = 'standard';

    public $price;

    public $description;

    public $tax_value;

    public $tax_type = 'percent';

    public $stock_quantity = 0;

    public $category_id;

    public $brand_id;

    public $unit_id;

    public $warranty_period;

    public $has_warranty = false;

    public $has_imei = false;

    public $image;

    public $new_product_image;

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
            $this->type = $p->type ?? 'standard';
            $this->image = $p->image;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,'.$this->productId,
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

    public function save(StockService $stock)
    {

        $this->validate();

        $data = $this->only([
            'name', 'sku', 'code', 'type', 'price', 'description',
            'tax_value', 'tax_type', 'category_id',
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

        DB::transaction(function () use ($data, $stock) {
            $product = Product::updateOrCreate(['id' => $this->productId], $data);

            // Stock never changes through the product itself: a new product
            // gets an opening movement, an edited one an adjustment.
            $quantity = (int) ($this->stock_quantity ?? 0);

            $product->wasRecentlyCreated
                ? $stock->opening($product, $quantity)
                : $stock->setLevel($product, $quantity, 'Stock level edited on the product form');
        });

        session()->flash('message', $this->productId ? __('messages.product.updated') : __('messages.product.created'));

        return redirect()->route('products.list');
    }

    public function render()
    {
        return view('livewire.products.product-form');
    }
}
