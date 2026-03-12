<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class EquipmentRental extends Model
{
    use HasUlidRouteKey;
    protected $fillable = [
        'equipment_id', 'member_id', 'customer_name', 'customer_phone', 'customer_upi',
        'quantity', 'rental_type', 'start_time', 'expected_return', 'actual_return',
        'deposit_paid', 'rental_amount', 'late_fee', 'total_amount',
        'payment_method', 'status', 'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'expected_return' => 'datetime',
        'actual_return' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
            ->where('expected_return', '<', now());
    }
}

