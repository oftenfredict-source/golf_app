<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalConfig extends Model
{
    protected $table = 'rental_config';
    
    protected $fillable = [
        'security_deposit',
        'max_rental_hours',
        'late_fee_per_hour',
        'require_deposit',
        'allow_extensions',
        'auto_charge_late',
        'extension_fee_per_hour',
        'damage_fee_percentage',
        'grace_period_minutes',
    ];

    protected $casts = [
        'require_deposit' => 'boolean',
        'allow_extensions' => 'boolean',
        'auto_charge_late' => 'boolean',
        'security_deposit' => 'decimal:2',
        'late_fee_per_hour' => 'decimal:2',
        'extension_fee_per_hour' => 'decimal:2',
        'damage_fee_percentage' => 'decimal:2',
    ];

    public static function getConfig()
    {
        return self::first() ?? self::create([
            'security_deposit' => 50000,
            'max_rental_hours' => 4,
            'late_fee_per_hour' => 5000,
            'require_deposit' => true,
            'allow_extensions' => true,
            'auto_charge_late' => true,
            'extension_fee_per_hour' => 3000,
            'damage_fee_percentage' => 10,
            'grace_period_minutes' => 15,
        ]);
    }
}


