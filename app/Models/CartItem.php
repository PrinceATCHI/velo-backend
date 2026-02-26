<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id', 'product_id', 'product_variant_id',
        'quantity', 'price', 'configuration'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'configuration' => 'array',
    ];

    // Relations
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}