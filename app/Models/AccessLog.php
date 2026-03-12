<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $fillable = [
        'gate_id', 'member_id', 'card_number', 'member_name', 
        'access_type', 'status', 'denial_reason', 'member_balance', 
        'notes', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'member_balance' => 'decimal:2',
    ];

    public function gate()
    {
        return $this->belongsTo(EntryGate::class, 'gate_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeDenied($query)
    {
        return $query->where('status', 'denied');
    }

    public function scopeEntries($query)
    {
        return $query->where('access_type', 'entry');
    }

    public function scopeExits($query)
    {
        return $query->where('access_type', 'exit');
    }
}
