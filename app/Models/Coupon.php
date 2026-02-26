<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'description', 'type', 'value', 'min_purchase',
        'max_discount', 'usage_limit', 'usage_count',
        'starts_at', 'expires_at', 'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relations
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Méthodes
    public function isValid()
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;
        
        return true;
    }
}