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
        'tax_value',
        'tax_type',
        'description',
        'price',
        'warranty_period',
        'has_warranty',
        'stock_quantity',
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
    public function sales()
    {
        return $this->hasMany(SaleItem::class);
    }

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
