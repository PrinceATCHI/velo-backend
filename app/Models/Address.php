<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'label', 'first_name', 'last_name', 'phone',
        'address_line_1', 'address_line_2', 'city', 'state',
        'postal_code', 'country', 'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingOrders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function billingOrders()
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }
}