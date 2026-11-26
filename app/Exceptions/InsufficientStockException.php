<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

/**
 * Thrown when a stock change would take a product below zero.
 *
 * It is a ValidationException so Livewire components and HTTP requests show
 * it as a regular form error on the "items" field.
 */
class InsufficientStockException extends ValidationException
{
    /**
     * @var list<array{product_id: int, product: string, available: int, requested: int}>
     */
    public array $shortages = [];

    /**
     * @param  list<array{product_id: int, product: string, available: int, requested: int}>  $shortages
     */
    public static function forShortages(array $shortages): self
    {
        $messages = array_map(
            fn (array $shortage) => __('messages.stock.insufficient_detail', [
                'product' => $shortage['product'],
                'available' => $shortage['available'],
                'requested' => $shortage['requested'],
            ]),
            $shortages,
        );

        $exception = static::withMessages(['items' => $messages]);
        $exception->shortages = $shortages;

        return $exception;
    }
}
