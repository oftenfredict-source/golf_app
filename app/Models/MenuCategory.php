<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class MenuCategory extends Model
{
    use HasUlidRouteKey;
    protected $fillable = ['name', 'description', 'sort_order', 'is_alcohol', 'is_food', 'status', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_alcohol' => 'boolean',
        'is_food' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'active')->orWhere('is_active', true);
        });
    }
}

