<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'item_code', 'name', 'category_id', 'quantity', 
        'unit', 'unit_price', 'reorder_level', 
        'location', 'description'
    ];

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }
}
