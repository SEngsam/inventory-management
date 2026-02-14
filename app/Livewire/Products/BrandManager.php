<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class BrandManager extends Component
{
    use WithFileUploads;

    public $brands;
    public $brand_id;
    public $name;
    public $description;
    public $image;
    public $newImage;
    public $isEdit = false;
    public $selectedBrands = [];

    public function mount()
    {
        abort_unless(auth()->user()->can('products.brands.manage'), 403);
        $this->loadBrands();
    }

    public function loadBrands()
    {
        $this->brands = Brand::all();
    }

    public function save()
    {
        abort_unless(auth()->user()->can('products.brands.manage'), 403);

        $isUpdating = (bool) $this->brand_id;

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'newImage' => $isUpdating ? 'nullable|image|max:1024' : 'required|image|max:1024',
        ]);

        $imagePath = $this->image;

        if ($this->newImage) {
            if ($isUpdating && $this->image && Storage::disk('public')->exists($this->image)) {
                Storage::disk('public')->delete($this->image);
            }

            $imagePath = $this->newImage->store('brands', 'public');
        }

        Brand::updateOrCreate(
            ['id' => $this->brand_id],
            [
                'name' => $this->name,
                'description' => $this->description,
                'image' => $imagePath,
            ]
        );

        $this->resetForm();
        $this->loadBrands();
        $this->dispatch('hide-brand-modal');

        session()->flash(
            'message',
            $isUpdating
                ? 'Brand updated successfully.'
                : 'Brand created successfully.'
        );
    }

    public function edit($id)
    {
        abort_unless(auth()->user()->can('products.brands.manage'), 403);

        $brand = Brand::findOrFail($id);

        $this->brand_id = $brand->id;
        $this->name = $brand->name;
        $this->description = $brand->description;
        $this->image = $brand->image;
        $this->isEdit = true;

        $this->dispatch('show-brand-modal');
    }

    public function deleteSelected()
    {
        abort_unless(auth()->user()->can('products.brands.manage'), 403);

        if (count($this->selectedBrands) > 0) {
            $brands = Brand::whereIn('id', $this->selectedBrands)->get();

            foreach ($brands as $brand) {
                if ($brand->image && Storage::disk('public')->exists($brand->image)) {
                    Storage::disk('public')->delete($brand->image);
                }
                $brand->delete();
            }

            $this->selectedBrands = [];
            $this->loadBrands();
            session()->flash('message', 'Selected brands deleted successfully.');
        }
    }

    public function resetForm()
    {
        $this->reset(['brand_id', 'name', 'description', 'newImage', 'image', 'isEdit']);
    }

    public function render()
    {
        return view('livewire.products.brand-manager', [
            'brands' => $this->brands,
        ]);
    }
}