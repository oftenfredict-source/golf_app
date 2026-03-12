<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BallTransaction extends Model
{
    protected $fillable = [
        'type', 'quantity', 'customer_name', 'customer_phone',
        'session_id', 'notes', 'user_id', 'amount', 'payment_method', 'member_id'
    ];
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}


