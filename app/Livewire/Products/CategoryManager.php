<?php

namespace App\Livewire\Products;

use App\Models\Category;
use Livewire\Component;

class CategoryManager extends Component
{
    public $categories;
    public $category_id;
    public $isEdit = false;
    public $name;
    public $code;
    public $selectedCategories = [];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::all();
    }

    public function save()
    {

        $isUpdating = (bool) $this->category_id;

        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:categories,code,' . $this->category_id,
        ]);

        Category::updateOrCreate(
            ['id' => $this->category_id],
            [
                'name' => $this->name,
                'code' => $this->code,
            ]
        );

        $this->reset(['name', 'code', 'category_id', 'isEdit']);
        $this->loadCategories();
        $this->dispatch('hide-category-modal');

        session()->flash(
            'message',
            $isUpdating
                ? 'Category updated successfully.'
                : 'Category created successfully.'
        );
    }

    public function deleteSelected()
    {

        if (count($this->selectedCategories) > 0) {
            Category::whereIn('id', $this->selectedCategories)->delete();
            $this->selectedCategories = [];
            $this->loadCategories();
            session()->flash('message', 'Selected categories deleted successfully.');
        }
    }

    public function edit($id)
    {

        $category = Category::findOrFail($id);

        $this->category_id = $category->id;
        $this->name = $category->name;
        $this->code = $category->code;
        $this->isEdit = true;

        $this->dispatch('show-category-modal');
    }

    public function resetForm()
    {
        $this->reset(['name', 'code', 'category_id', 'isEdit']);
    }

    public function render()
    {
        return view('livewire.products.category-manager', [
            'categories' => $this->categories,
        ]);
    }
}