<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BallCollector extends Model
{
    protected $fillable = ['name', 'phone', 'status'];

    public function logs()
    {
        return $this->hasMany(BallCollectionLog::class, 'collector_id');
    }
}
