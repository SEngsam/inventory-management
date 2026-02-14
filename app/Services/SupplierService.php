<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SupplierService
{
    public function create(string $name, string $email, string $phone): Supplier
    {
        if (empty($name)) {
            throw new InvalidArgumentException("Supplier name is required");
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }

        if (Supplier::where('email', $email)->exists()) {
            throw new InvalidArgumentException("Email already exists");
        }

        if (Supplier::where('phone', $phone)->exists()) {
            throw new InvalidArgumentException("Phone number already exists");
        }

        return DB::transaction(function () use ($name, $email, $phone) {
            return Supplier::create([
                'name'  => $name,
                'email' => $email,
                'phone' => $phone,
            ]);
        });
    }
}
