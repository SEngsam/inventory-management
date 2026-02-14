<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['name', 'symbol', 'rate', 'is_default', 'is_active'];

    public static function default()
    {
        return static::where('is_default', true)->first();
    }

    public function scopeActive($query)
    {
        $query->where('is_active', true);
    }
}
