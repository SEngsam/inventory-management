<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = ['reference_no', 'sale_date', 'status', 'note'];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
