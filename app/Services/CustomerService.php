<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CustomerService
{
    public function create(string $name, string $email, string $phone): Customer
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Customer name is required");
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }

        if (Customer::where('email', $email)->exists()) {
            throw new InvalidArgumentException("Email already exists");
        }

        if (Customer::where('phone', $phone)->exists()) {
            throw new InvalidArgumentException("Phone number already exists");
        }

        return DB::transaction(function () use ($name, $email, $phone) {
            return Customer::create([
                'name'  => $name,
                'email' => $email,
                'phone' => $phone,
            ]);
        });
    }
}
