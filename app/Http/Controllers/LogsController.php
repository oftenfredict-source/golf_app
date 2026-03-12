<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }
        
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        $logs = $query->paginate(50);
        
        $modules = ActivityLog::distinct()->pluck('module')->sort();
        $actions = ActivityLog::distinct()->pluck('action')->sort();
        
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::today()->count(),
            'this_week' => ActivityLog::recent(7)->count(),
        ];
        
        return view('logs.activity-logs', compact('logs', 'modules', 'actions', 'stats'));
    }
    
    public function exportLogs(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');
        
        // Apply same filters as index
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }
        
        $logs = $query->get();
        
        $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Time', 'User', 'Module', 'Action', 'Description', 'Entity', 'IP Address']);
            
            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user->name ?? 'System',
                    $log->module,
                    $log->action,
                    $log->description,
                    $log->entity_type ? ($log->entity_type . ' #' . $log->entity_id) : '-',
                    $log->ip_address ?? '-',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
