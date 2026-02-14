<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'type', 'quantity', 'performed_at', 'customer_id'];

    public $timestamps = false;
}
