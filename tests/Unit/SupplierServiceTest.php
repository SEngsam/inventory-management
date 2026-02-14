<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\SupplierService;
use App\Models\Supplier;
use InvalidArgumentException;

class SupplierServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SupplierService $supplierService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->supplierService = $this->app->make(SupplierService::class);
    }

    /** @test */
    public function it_creates_supplier_successfully()
    {
        $supplier = $this->supplierService->create('Acme Inc.', 'acme@example.com', '123456789');

        $this->assertInstanceOf(Supplier::class, $supplier);
        $this->assertEquals('Acme Inc.', $supplier->name);
        $this->assertEquals('acme@example.com', $supplier->email);
        $this->assertEquals('123456789', $supplier->phone);
    }

    /** @test */
    public function it_throws_exception_if_email_is_invalid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid email format");

        $this->supplierService->create('Test', 'invalid-email', '777888999');
    }

    /** @test */
    public function it_throws_exception_if_email_already_exists()
    {
        Supplier::factory()->create(['email' => 'existing@example.com']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Email already exists");

        $this->supplierService->create('Another', 'existing@example.com', '777888000');
    }

    /** @test */
    public function it_throws_exception_if_phone_already_exists()
    {
        Supplier::factory()->create(['phone' => '555444333']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Phone number already exists");

        $this->supplierService->create('Another', 'unique@example.com', '555444333');
    }
}
