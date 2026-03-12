<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class Table extends Model
{
    use HasUlidRouteKey;

    protected $fillable = [
        'table_number', 'type', 'status', 'notes'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeVip($query)
    {
        return $query->where('type', 'vip');
    }

    public function scopeNormal($query)
    {
        return $query->where('type', 'normal');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
