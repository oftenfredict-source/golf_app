<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class Order extends Model
{
    use HasUlidRouteKey;
    protected $fillable = [
        'order_number', 'member_id', 'customer_name', 'customer_phone', 'customer_upi',
        'table_number', 'table_id', 'subtotal', 'discount', 'total_amount',
        'is_vip', 'counter_id', 'payment_method', 'status', 'sms_sent', 'notes'
    ];

    public function table()
    {
        return $this->belongsTo(\App\Models\Table::class);
    }

    public function counter()
    {
        return $this->belongsTo(\App\Models\Counter::class);
    }

    public function member()
    {
        return $this->belongsTo(\App\Models\Member::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'saved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'complete');
    }

    public static function generateOrderNumber()
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $lastOrder = self::where('order_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastOrder) {
            $lastNum = (int) substr($lastOrder->order_number, -4);
            return $prefix . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        }
        
        return $prefix . '0001';
    }
}


