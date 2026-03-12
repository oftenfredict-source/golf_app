<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class EntryGate extends Model
{
    use HasUlidRouteKey;
    protected $fillable = [
        'name', 'type', 'location', 'device_id', 'is_active', 
        'status', 'notes', 'requires_card'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class, 'gate_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }
}
