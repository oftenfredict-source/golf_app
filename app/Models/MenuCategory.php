<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class MenuCategory extends Model
{
    use HasUlidRouteKey;
    protected $fillable = ['name', 'description', 'sort_order', 'status', 'is_active'];

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

