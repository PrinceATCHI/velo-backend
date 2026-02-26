<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BikeComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'name', 'brand', 'description', 'price',
        'image', 'specifications', 'stock', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'specifications' => 'array',
        'is_active' => 'boolean',
    ];

    // Relations
    public function compatibilities()
    {
        return $this->hasMany(ComponentCompatibility::class, 'component_id');
    }

    public function compatibleWith()
    {
        return $this->belongsToMany(
            BikeComponent::class,
            'component_compatibilities',
            'component_id',
            'compatible_with_id'
        );
    }

    public function configurations()
    {
        return $this->hasMany(BikeConfiguration::class);
    }
}