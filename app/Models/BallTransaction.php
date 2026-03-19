<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BallTransaction extends Model
{
    protected $fillable = [
        'type', 'quantity', 'customer_name', 'customer_phone',
        'session_id', 'notes', 'user_id', 'amount', 'payment_method', 'member_id', 'collector_id'
    ];
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function collector()
    {
        return $this->belongsTo(BallCollector::class, 'collector_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeInDateRange($query, $fromDate, $toDate)
    {
        return $query->whereBetween('created_at', [
            \Carbon\Carbon::parse($fromDate)->startOfDay(),
            \Carbon\Carbon::parse($toDate)->endOfDay()
        ]);
    }
}


