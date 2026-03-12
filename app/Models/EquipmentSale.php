<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class EquipmentSale extends Model
{
    use HasUlidRouteKey;
    protected $fillable = [
        'member_id', 'customer_name', 'customer_phone', 'customer_upi',
        'subtotal', 'discount', 'total_amount',
        'payment_method', 'sms_sent', 'status', 'notes'
    ];

    public function items()
    {
        return $this->hasMany(EquipmentSaleItem::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}

