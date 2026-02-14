<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'customer_id',
        'reference_no',
        'return_date',
        'note',
        'total',
    ];
    protected $casts = [
        'return_date' => 'datetime',
    ];
    protected $with = ['items.product', 'customer', 'sale'];

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
