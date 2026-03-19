<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class Transaction extends Model
{
    use HasUlidRouteKey;
    protected $fillable = [
        'transaction_id', 'member_id', 'customer_name', 'type', 'category',
        'amount', 'balance_before', 'balance_after', 'payment_method',
        'reference_type', 'reference_id', 'status', 'sms_sent', 'notes'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'reference_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public static function generateTransactionId()
    {
        return 'TXN-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
    }
}



