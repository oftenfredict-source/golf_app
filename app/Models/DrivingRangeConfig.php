<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrivingRangeConfig extends Model
{
    protected $table = 'driving_range_config';
    
    protected $fillable = [
        'total_bays',
        'balls_per_bucket',
        'range_distance',
        'has_roof',
        'has_lighting',
        'has_tracking',
        'hourly_rate',
        'ball_limit_price',
        'balls_limit_per_session',
        'bucket_price',
        'unlimited_price',
        'member_discount',
        'premium_rate',
        'regular_rate',
    ];

    protected $casts = [
        'has_roof' => 'boolean',
        'has_lighting' => 'boolean',
        'has_tracking' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'ball_limit_price' => 'decimal:2',
        'bucket_price' => 'decimal:2',
        'unlimited_price' => 'decimal:2',
        'member_discount' => 'decimal:2',
        'premium_rate' => 'decimal:2',
        'regular_rate' => 'decimal:2',
    ];

    public static function getConfig()
    {
        return self::first() ?? self::create([
            'total_bays' => 20,
            'balls_per_bucket' => 50,
            'range_distance' => 250,
            'has_roof' => true,
            'has_lighting' => true,
            'has_tracking' => false,
            'hourly_rate' => 5000,
            'ball_limit_price' => 5000,
            'balls_limit_per_session' => 50,
            'bucket_price' => 2000,
            'unlimited_price' => 8000,
            'member_discount' => 10,
            'premium_rate' => 7500,
            'regular_rate' => 5000,
        ]);
    }
}



