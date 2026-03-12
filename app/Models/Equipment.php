<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class Equipment extends Model
{
    use HasUlidRouteKey;
    
    protected $table = 'equipment';
    
    protected $fillable = [
        'name', 'sku', 'category', 'description',
        'rental_hourly_rate', 'rental_daily_rate', 'sale_price', 'deposit_amount',
        'total_quantity', 'available_quantity', 'rented_quantity', 'maintenance_quantity',
        'low_stock_threshold', 'is_rentable', 'is_sellable', 'status'
    ];

    protected $casts = [
        'is_rentable' => 'boolean',
        'is_sellable' => 'boolean',
    ];

    public function rentals()
    {
        return $this->hasMany(EquipmentRental::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRentable($query)
    {
        return $query->where('is_rentable', true);
    }

    public function scopeSellable($query)
    {
        return $query->where('is_sellable', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('available_quantity', '<=', 'low_stock_threshold');
    }
}



