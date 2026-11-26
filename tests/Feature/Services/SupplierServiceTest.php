<?php

use App\Models\Supplier;
use App\Services\SupplierService;

beforeEach(function () {
    $this->service = app(SupplierService::class);
});

it('creates a supplier', function () {
    $supplier = $this->service->create('Acme Inc.', 'acme@example.com', '123456789');

    expect($supplier)
        ->toBeInstanceOf(Supplier::class)
        ->name->toBe('Acme Inc.')
        ->email->toBe('acme@example.com')
        ->phone->toBe('123456789');
});

it('rejects an invalid email', function () {
    $this->service->create('Test', 'invalid-email', '777888999');
})->throws(InvalidArgumentException::class, 'Invalid email format');

it('rejects an email that is already used', function () {
    Supplier::factory()->create(['email' => 'existing@example.com']);

    $this->service->create('Another', 'existing@example.com', '777888000');
})->throws(InvalidArgumentException::class, 'Email already exists');

it('rejects a phone number that is already used', function () {
    Supplier::factory()->create(['phone' => '555444333']);

    $this->service->create('Another', 'unique@example.com', '555444333');
})->throws(InvalidArgumentException::class, 'Phone number already exists');
