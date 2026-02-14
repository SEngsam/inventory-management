<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'image',
        'category_id',
        'brand_id',
        'order_tax',
        'tax_type',
        'description',
        'price',
        'warranty_period',
        'guarantee',
        'stock_quantity',
        'guarantee_period',
        'has_imei',
        'type',
        'code',
        'unit_id',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }


    // 🔁 Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // 🧠 Optional: Access full image URL
    public function getImageUrlAttribute()
    {
        return $this->product_image
            ? asset('storage/' . $this->product_image)
            : asset('images/no-image.png');
    }

    public function reduceStock($quantity)
    {
        $this->decrement('stock_quantity', $quantity);
    }
}
