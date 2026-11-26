<?php

namespace Tests\Unit;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    private function movement(): StockMovement
    {
        return StockMovement::create([
            'product_id' => Product::factory()->create()->id,
            'type' => StockMovementType::Adjustment,
            'quantity' => 5,
            'balance_after' => 5,
        ]);
    }

    public function test_movements_cannot_be_updated(): void
    {
        $movement = $this->movement();

        $this->expectException(LogicException::class);
        $movement->update(['quantity' => 10]);
    }

    public function test_movements_cannot_be_deleted(): void
    {
        $movement = $this->movement();

        $this->expectException(LogicException::class);
        $movement->delete();
    }
}
