<?php

use App\Livewire\Users\UserManager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('admin');
    Role::findOrCreate('cashier');
});

it('creates a user with a role and a hashed password', function () {
    Livewire::test(UserManager::class)
        ->set('name', 'Sam Cashier')
        ->set('email', 'sam@example.com')
        ->set('password', 'secret123')
        ->set('role', 'cashier')
        ->call('save')
        ->assertHasNoErrors();

    $user = User::where('email', 'sam@example.com')->sole();

    expect($user->hasRole('cashier'))->toBeTrue()
        ->and(Hash::check('secret123', $user->password))->toBeTrue();
});

it('keeps the password when editing without a new one', function () {
    $user = User::factory()->create(['password' => Hash::make('original')]);
    $user->assignRole('cashier');

    Livewire::test(UserManager::class)
        ->call('edit', $user->id)
        ->set('name', 'Renamed')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->fresh())
        ->name->toBe('Renamed')
        ->and(Hash::check('original', $user->fresh()->password))->toBeTrue();
});

it('validates the user form', function (string $field, mixed $value, string $rule) {
    Livewire::test(UserManager::class)
        ->set('name', 'Valid')
        ->set('email', 'valid@example.com')
        ->set('password', 'secret123')
        ->set('role', 'cashier')
        ->set($field, $value)
        ->call('save')
        ->assertHasErrors([$field => $rule]);
})->with([
    'missing email' => ['email', '', 'required'],
    'short password' => ['password', '123', 'min'],
    'unknown role' => ['role', 'owner', 'in'],
]);

it('rejects an email that belongs to another user', function () {
    $existing = User::factory()->create();

    Livewire::test(UserManager::class)
        ->set('name', 'Duplicate')
        ->set('email', $existing->email)
        ->set('password', 'secret123')
        ->set('role', 'cashier')
        ->call('save')
        ->assertHasErrors(['email' => 'unique']);
});
