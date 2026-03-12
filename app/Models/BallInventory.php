<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BallInventory extends Model
{
    protected $table = 'ball_inventory';
    
    protected $fillable = [
        'ball_type', 'total_quantity', 'available_quantity', 
        'in_use', 'damaged', 'cost_per_ball'
    ];
}



