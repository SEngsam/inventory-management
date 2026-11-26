<?php

use App\Livewire\Products\BrandManager;
use App\Livewire\Products\CategoryManager;
use App\Livewire\Products\UnitManager;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

describe('categories', function () {
    it('creates and edits a category', function () {
        Livewire::test(CategoryManager::class)
            ->set('name', 'Phones')
            ->set('code', 'PHN')
            ->call('save')
            ->assertHasNoErrors();

        $category = Category::sole();

        Livewire::test(CategoryManager::class)
            ->call('edit', $category->id)
            ->assertSet('name', 'Phones')
            ->set('name', 'Mobile Phones')
            ->call('save')
            ->assertHasNoErrors();

        expect($category->fresh()->name)->toBe('Mobile Phones');
    });

    it('requires a name and a unique code', function () {
        $existing = Category::factory()->create();

        Livewire::test(CategoryManager::class)
            ->set('code', $existing->code)
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'code' => 'unique']);
    });
});

describe('brands', function () {
    it('creates a brand with its logo', function () {
        Storage::fake('public');

        Livewire::test(BrandManager::class)
            ->set('name', 'Contoso')
            ->set('newImage', UploadedFile::fake()->image('logo.png'))
            ->call('save')
            ->assertHasNoErrors();

        $brand = Brand::sole();

        expect($brand->name)->toBe('Contoso');
        Storage::disk('public')->assertExists($brand->image);
    });

    it('requires a name and a logo image', function () {
        Livewire::test(BrandManager::class)
            ->set('newImage', UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'))
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'newImage' => 'image']);
    });
});

describe('units', function () {
    it('creates a base unit and a derived unit', function () {
        Livewire::test(UnitManager::class)
            ->set('name', 'Piece')
            ->set('short_code', 'pc')
            ->call('save')
            ->assertHasNoErrors();

        $piece = Unit::sole();

        Livewire::test(UnitManager::class)
            ->set('name', 'Box')
            ->set('short_code', 'box')
            ->set('base_unit_id', $piece->id)
            ->set('operator', '*')
            ->set('operator_value', 12)
            ->call('save')
            ->assertHasNoErrors();

        expect(Unit::where('short_code', 'box')->sole())
            ->base_unit_id->toBe($piece->id)
            ->operator->toBe('*');
    });

    it('requires a conversion when a base unit is chosen', function () {
        $piece = Unit::factory()->create();

        Livewire::test(UnitManager::class)
            ->set('name', 'Box')
            ->set('base_unit_id', $piece->id)
            ->call('save')
            ->assertHasErrors(['operator' => 'required', 'operator_value' => 'required']);
    });
});
