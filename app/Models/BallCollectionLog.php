<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BallCollectionLog extends Model
{
    protected $fillable = [
        'collector_id', 
        'ball_transaction_id',
        'target_quantity',
        'quantity_collected', 
        'status', 
        'assigned_by', 
        'collected_at', 
        'verified_at'
    ];

    protected $casts = [
        'collected_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function ballTransaction()
    {
        return $this->belongsTo(BallTransaction::class, 'ball_transaction_id');
    }

    public function collector()
    {
        return $this->belongsTo(BallCollector::class, 'collector_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
