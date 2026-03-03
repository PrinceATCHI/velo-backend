<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'name_de', 'slug',
        'description', 'description_de',
        'price', 'sale_price', 'stock',
        'is_featured', 'is_new', 'assembly_required',
        'brand', 'model_name', 'year',
        'bike_type', 'age_range', 'color', 'colors', 'sizes',
        'wheel_size', 'wheel_width', 'wheel_material',
        'frame_material', 'number_of_speeds',
        'suspension_type', 'brake_style',
        'power_source', 'motor_power',
        'battery_capacity', 'battery_energy_content',
        'max_speed', 'max_range', 'charging_time',
        'weight', 'max_load', 'dimensions',
        'certification', 'waterproof_rating',
        'warranty_type', 'warranty_description',
        'included_components', 'special_features', 'special_features_de',
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'sale_price'        => 'decimal:2',
        'is_featured'       => 'boolean',
        'is_new'            => 'boolean',
        'assembly_required' => 'boolean',
    ];

    /* ── Relations ── */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    /* ── Accesseurs utiles ── */
    public function getAverageRatingAttribute(): float
    {
        if ($this->reviews()->count() === 0) return 0;
        return round($this->reviews()->avg('rating'), 1);
    }

    public function getTotalReviewsAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getDiscountPercentAttribute(): int
    {
        if (!$this->sale_price || $this->price == 0) return 0;
        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }
}
