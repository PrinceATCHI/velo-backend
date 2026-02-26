<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSettings extends Model
{
    protected $table = 'payment_settings';

    protected $fillable = [
        'beneficiary_name',
        'iban',
        'bic',
        'bank_name',
        'instructions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Récupérer les paramètres actifs
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first() ?? self::first();
    }
}