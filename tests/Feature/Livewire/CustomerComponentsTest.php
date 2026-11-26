<?php

use App\Livewire\Customers\CustomerForm;
use App\Livewire\Customers\CustomerList;
use App\Models\Customer;
use Livewire\Livewire;

it('creates a customer', function () {
    Livewire::test(CustomerForm::class)
        ->set('name', 'Lina Haddad')
        ->set('email', 'lina@example.com')
        ->set('phone', '0771234567')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('customers.index'));

    expect(Customer::sole())
        ->name->toBe('Lina Haddad')
        ->email->toBe('lina@example.com');
});

it('updates an existing customer', function () {
    $customer = Customer::factory()->create(['name' => 'Old Name']);

    Livewire::test(CustomerForm::class, ['customer' => $customer])
        ->assertSet('name', 'Old Name')
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors();

    expect($customer->fresh()->name)->toBe('New Name')
        ->and(Customer::count())->toBe(1);
});

it('validates the customer form', function (string $field, mixed $value, string $rule) {
    Livewire::test(CustomerForm::class)
        ->set('name', 'Valid Name')
        ->set($field, $value)
        ->call('save')
        ->assertHasErrors([$field => $rule]);
})->with([
    'missing name' => ['name', '', 'required'],
    'invalid email' => ['email', 'not-an-email', 'email'],
    'long phone' => ['phone', str_repeat('1', 21), 'max'],
]);

it('lists and deletes customers', function () {
    $customers = Customer::factory()->count(2)->create();

    Livewire::test(CustomerList::class)
        ->assertSee($customers[0]->name)
        ->assertSee($customers[1]->name)
        ->call('delete', $customers[0]->id)
        ->assertDontSee($customers[0]->name);

    $this->assertModelMissing($customers[0]);
});

it('deletes selected customers in bulk', function () {
    $customers = Customer::factory()->count(3)->create();

    Livewire::test(CustomerList::class)
        ->set('selectedCustomers', $customers->take(2)->pluck('id')->all())
        ->call('deleteCustomer');

    expect(Customer::pluck('id')->all())->toBe([$customers[2]->id]);
});
