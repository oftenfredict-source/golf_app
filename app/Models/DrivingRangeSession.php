<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUlidRouteKey;

class DrivingRangeSession extends Model
{
    use HasFactory, HasUlidRouteKey;

    protected $fillable = [
        'member_id',
        'customer_name',
        'customer_phone',
        'customer_upi',
        'bay_number',
        'session_type',
        'buckets_count',
        'balls_limit_allowed',
        'balls_used',
        'start_time',
        'end_time',
        'duration_minutes',
        'amount',
        'payment_method',
        'status',
        'notes',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}


