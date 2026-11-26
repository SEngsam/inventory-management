<?php

use App\Models\Customer;
use App\Services\CustomerService;

beforeEach(function () {
    $this->service = app(CustomerService::class);
});

it('creates a customer', function () {
    $customer = $this->service->create('Lina A.', 'lina@example.com', '0771234567');

    expect($customer)
        ->toBeInstanceOf(Customer::class)
        ->name->toBe('Lina A.')
        ->email->toBe('lina@example.com')
        ->phone->toBe('0771234567');
});

it('rejects an invalid email', function () {
    $this->service->create('Test', 'invalid-email', '0771234567');
})->throws(InvalidArgumentException::class, 'Invalid email format');

it('rejects an email that is already used', function () {
    Customer::factory()->create(['email' => 'used@example.com']);

    $this->service->create('Duplicate', 'used@example.com', '0770000000');
})->throws(InvalidArgumentException::class, 'Email already exists');

it('rejects a phone number that is already used', function () {
    Customer::factory()->create(['phone' => '077888999']);

    $this->service->create('Another', 'unique@example.com', '077888999');
})->throws(InvalidArgumentException::class, 'Phone number already exists');
