<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class Counter extends Model
{
    use HasFactory, HasUlidRouteKey;

    protected $fillable = [
        'name', 'location', 'type', 'is_alcohol', 'is_food', 'is_active', 'assigned_user_id', 'tier'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_alcohol' => 'boolean',
        'is_food' => 'boolean',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
