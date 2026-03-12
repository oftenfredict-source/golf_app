<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasUlidRouteKey;

class ActivityLog extends Model
{
    use HasUlidRouteKey;
    protected $fillable = [
        'user_id', 'module', 'action', 'entity_type', 'entity_id',
        'description', 'data', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Log an activity
     */
    public static function log($module, $action, $description, $entityType = null, $entityId = null, $data = null)
    {
        return self::create([
            'user_id' => Auth::id(),
            'module' => $module,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
