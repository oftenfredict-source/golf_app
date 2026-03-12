<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentSaleItem extends Model
{
    protected $fillable = [
        'equipment_sale_id', 'equipment_id', 'quantity', 'unit_price', 'subtotal'
    ];

    public function sale()
    {
        return $this->belongsTo(EquipmentSale::class, 'equipment_sale_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}



