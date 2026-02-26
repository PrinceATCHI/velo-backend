<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'image_path', 'is_primary', 'order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}