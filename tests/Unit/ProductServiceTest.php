<?php

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ProductService;
use App\Models\Product;
use InvalidArgumentException;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;


    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = $this->app->make(ProductService::class);
    }

    /** @test */
    public function it_creates_product_successfully()
    {
        $category = Category::factory()->create();

        $product = $this->productService->create('iPhone', 799,$category->id,'SKU-' . Str::random(6));

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('iPhone', $product->name);
        $this->assertEquals(799, $product->price);
    }

    /** @test */
    public function it_throws_exception_if_name_is_empty()
    {
        $category = Category::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name is required');

        $this->productService->create('', 100,$category->id,'SKU-' . Str::random(6));
    }

    /** @test */
    public function it_throws_exception_if_price_is_too_low()
    {
        $category = Category::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product price must be greater than 1');

        $this->productService->create('Headphones', 1,$category->id,'SKU-' . Str::random(6));
    }

    /** @test */
    public function it_throws_exception_if_name_is_not_unique()
    {
        $category = Category::factory()->create();

        Product::factory()->create(['name' => 'Speaker', 'price' => 50]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name must be unique');

        $this->productService->create('Speaker', 55,$category->id,'SKU-' . Str::random(6));
    }
}
