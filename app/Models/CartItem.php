<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{

    use HasFactory;
    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id', 'product_id', 'product_variant_id',
        'quantity', 'price', 'configuration'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'configuration' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        // ✅ Charge automatiquement les images avec chaque produit
        return $this->belongsTo(Product::class)->with('images');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}