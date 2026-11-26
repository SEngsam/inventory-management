<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

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
