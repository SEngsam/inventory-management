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
        $this->loadBrands();
    }

    public function loadBrands()
    {
        $this->brands = Brand::all();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'newImage' => $this->isEdit ? 'nullable|image|max:1024' : 'required|image|max:1024',
        ]);

        $imagePath = $this->newImage ? $this->newImage->store('brands', 'public') : $this->image;

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
        $msg = $this->isEdit ? 'Brand updated successfully.' : 'Brand created successfully.';
        session()->flash('message', $msg);
    }

    public function edit($id)
    {
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
        return view('livewire.brand-manager', [
            'brands' => $this->brands,
        ]);
    }
}
