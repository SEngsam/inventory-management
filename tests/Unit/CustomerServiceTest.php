<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\CustomerService;
use App\Models\Customer;
use InvalidArgumentException;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CustomerService $customerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerService = $this->app->make(CustomerService::class);
    }

    /** @test */
    public function it_creates_customer_successfully()
    {
        $customer = $this->customerService->create('Lina A.', 'lina@example.com', '0771234567');

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('Lina A.', $customer->name);
        $this->assertEquals('lina@example.com', $customer->email);
        $this->assertEquals('0771234567', $customer->phone);
    }

    /** @test */
    public function it_throws_exception_if_email_is_invalid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid email format");

        $this->customerService->create('Test', 'invalid-email', '0771234567');
    }

    /** @test */
    public function it_throws_exception_if_email_already_exists()
    {
        Customer::factory()->create(['email' => 'used@example.com']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Email already exists");

        $this->customerService->create('Duplicate', 'used@example.com', '0770000000');
    }

    /** @test */
    public function it_throws_exception_if_phone_already_exists()
    {
        Customer::factory()->create(['phone' => '077888999']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Phone number already exists");

        $this->customerService->create('Another', 'unique@example.com', '077888999');
    }
}
