<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessControlConfig extends Model
{
    protected $table = 'access_control_config';

    protected $fillable = [
        'members_only',
        'require_valid_card',
        'check_balance',
        'allow_guests',
        'operating_hours_only',
        'opening_time',
        'closing_time',
        'min_balance',
        'guest_fee',
        'blocked_cards',
        'global_mode',
        'global_mode_expires_at',
    ];

    protected $casts = [
        'members_only' => 'boolean',
        'require_valid_card' => 'boolean',
        'check_balance' => 'boolean',
        'allow_guests' => 'boolean',
        'operating_hours_only' => 'boolean',
        'min_balance' => 'decimal:2',
        'guest_fee' => 'decimal:2',
        'global_mode_expires_at' => 'datetime',
    ];

    public static function getConfig()
    {
        return self::first() ?? self::create([
            'members_only' => true,
            'require_valid_card' => true,
            'check_balance' => false,
            'allow_guests' => true,
            'operating_hours_only' => true,
            'opening_time' => '06:00',
            'closing_time' => '22:00',
            'min_balance' => 0,
            'guest_fee' => 50000,
            'blocked_cards' => null,
        ]);
    }
}
