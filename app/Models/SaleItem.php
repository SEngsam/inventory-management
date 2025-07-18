<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'product_id', 'quantity', 'unit_price', 'total'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->total = $item->unit_price * $item->quantity;
        });
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }
}
