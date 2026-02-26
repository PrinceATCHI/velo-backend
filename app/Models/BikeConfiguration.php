<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BikeConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'components', 'total_price',
        'preview_image', 'is_saved'
    ];

    protected $casts = [
        'components' => 'array',
        'total_price' => 'decimal:2',
        'is_saved' => 'boolean',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes
    public function getComponentsList()
    {
        $componentIds = $this->components;
        return BikeComponent::whereIn('id', $componentIds)->get();
    }
}