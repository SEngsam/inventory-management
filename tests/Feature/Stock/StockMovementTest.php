<?php

use App\Models\StockMovement;

it('cannot be updated', function () {
    StockMovement::factory()->create()->update(['quantity' => 10]);
})->throws(LogicException::class, 'immutable');

it('cannot be deleted', function () {
    StockMovement::factory()->create()->delete();
})->throws(LogicException::class, 'immutable');
