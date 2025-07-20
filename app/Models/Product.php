<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'unique_key',
        'product_title',
        'product_description',
        'style_number',
        'sanmar_mainframe_color',
        'size',
        'color_name',
        'piece_price'
    ];

    protected $casts = [
        'piece_price' => 'decimal:2'
    ];

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->piece_price, 2);
    }

    /**
     * Scope to search by unique key
     */
    public function scopeByUniqueKey($query, string $uniqueKey)
    {
        return $query->where('unique_key', $uniqueKey);
    }

    /**
     * Scope to search products
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('unique_key', 'like', "%{$search}%")
              ->orWhere('product_title', 'like', "%{$search}%")
              ->orWhere('style_number', 'like', "%{$search}%")
              ->orWhere('color_name', 'like', "%{$search}%");
        });
    }
}
