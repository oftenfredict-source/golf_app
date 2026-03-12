<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class Topup extends Model
{
    use HasUlidRouteKey;
    protected $fillable = [
        'member_id', 'amount', 'balance_before', 'balance_after',
        'payment_method', 'reference_number', 'sms_sent', 'processed_by'
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



