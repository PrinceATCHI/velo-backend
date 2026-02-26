<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComponentCompatibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_id', 'compatible_with_id'
    ];

    // Relations
    public function component()
    {
        return $this->belongsTo(BikeComponent::class, 'component_id');
    }

    public function compatibleComponent()
    {
        return $this->belongsTo(BikeComponent::class, 'compatible_with_id');
    }
}