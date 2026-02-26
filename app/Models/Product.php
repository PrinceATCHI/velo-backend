<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Product extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'technical_specs',
        'price', 'sale_price', 'stock', 'sku', 'availability',
        'delivery_days', 'is_active', 'is_featured', 'views',
    
    // Nouvelles caractéristiques
    'bike_type','age_range','number_of_speeds','wheel_size','frame_material','suspension_type','special_features','brake_style','wheel_width', 'model_name','power_source','wheel_material', 'year','battery_energy_content','battery_capacity','max_speed','max_range','motor_power','charging_time','weight', 'max_load','dimensions','package_weight','waterproof_rating','certification','assembly_required','warranty_type','warranty_description','included_components',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
         'price' => 'decimal:2',
    'sale_price' => 'decimal:2',
    'is_active' => 'boolean',
    'is_featured' => 'boolean',
    'assembly_required' => 'boolean',
    'year' => 'integer',
    'number_of_speeds' => 'integer',
    ];

    // Sluggable
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // Accessors
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

}