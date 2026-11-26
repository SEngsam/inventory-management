<?php

use App\Livewire\Suppliers\SupplierForm;
use App\Livewire\Suppliers\SupplierList;
use App\Models\Supplier;
use Livewire\Livewire;

it('creates a supplier', function () {
    Livewire::test(SupplierForm::class)
        ->set('name', 'Acme Trading')
        ->set('email', 'orders@acme.test')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('suppliers.index'));

    expect(Supplier::sole()->name)->toBe('Acme Trading');
});

it('updates an existing supplier', function () {
    $supplier = Supplier::factory()->create(['name' => 'Old Supplier']);

    Livewire::test(SupplierForm::class, ['supplier' => $supplier->id])
        ->assertSet('name', 'Old Supplier')
        ->set('name', 'New Supplier')
        ->call('save')
        ->assertHasNoErrors();

    expect($supplier->fresh()->name)->toBe('New Supplier')
        ->and(Supplier::count())->toBe(1);
});

it('validates the supplier form', function (string $field, mixed $value, string $rule) {
    Livewire::test(SupplierForm::class)
        ->set('name', 'Valid Name')
        ->set($field, $value)
        ->call('save')
        ->assertHasErrors([$field => $rule]);
})->with([
    'missing name' => ['name', '', 'required'],
    'invalid email' => ['email', 'not-an-email', 'email'],
]);

it('lists suppliers', function () {
    $supplier = Supplier::factory()->create();

    Livewire::test(SupplierList::class)
        ->assertSee($supplier->name)
        ->assertSee(route('suppliers.edit', $supplier->id), false);
});
