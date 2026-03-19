<?php

namespace App\Http\Controllers;

use App\Models\BallCollector;
use App\Models\BallCollectionLog;
use App\Models\BallInventory;
use App\Models\BallTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BallCollectionController extends Controller
{
    public function index()
    {
        $collectors = BallCollector::withCount(['logs' => function($q) {
            $q->whereDate('created_at', now());
        }])->get();
        
        $todaysLogs = BallCollectionLog::with('collector', 'assigner')
            ->whereDate('created_at', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('golf-services.ball-collection.index', compact('collectors', 'todaysLogs'));
    }

    public function storeCollector(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        BallCollector::create($request->all());

        return redirect()->back()->with('success', 'Collector added successfully');
    }

    public function updateCollector(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $collector = BallCollector::findOrFail($id);
        $collector->update($request->all());

        return redirect()->back()->with('success', 'Collector updated successfully');
    }

    public function destroyCollector($id)
    {
        $collector = BallCollector::findOrFail($id);
        
        // Check if has logs
        if ($collector->logs()->exists()) {
            $collector->update(['status' => 'inactive']);
            return redirect()->back()->with('success', 'Collector deactivated (has existing logs)');
        }

        $collector->delete();
        return redirect()->back()->with('success', 'Collector removed successfully');
    }

    public function assign(Request $request)
    {
        $request->validate([
            'collector_id' => 'required|exists:ball_collectors,id',
            'target_quantity' => 'nullable|integer|min:0',
        ]);

        $log = BallCollectionLog::create([
            'collector_id' => $request->collector_id,
            'quantity_collected' => 0,
            'status' => 'pending',
            'assigned_by' => Auth::id(),
            'collected_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Collector assigned successfully',
            'log' => $log
        ]);
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'quantity_collected' => 'required|integer|min:1',
        ]);

        $log = BallCollectionLog::findOrFail($id);
        
        if ($log->status === 'verified') {
            return response()->json(['success' => false, 'message' => 'Collection already verified'], 400);
        }

        $quantity = $request->quantity_collected;

        // Update log
        $log->update([
            'quantity_collected' => $quantity,
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        // Update Inventory
        $inventory = BallInventory::first();
        if ($inventory) {
            $inventory->increment('available_quantity', $quantity);
            $inventory->decrement('in_use', min($inventory->in_use, $quantity));
        }

        // Create a ball transaction record for the audit trail
        BallTransaction::create([
            'type' => 'returned',
            'quantity' => $quantity,
            'customer_name' => $log->ballTransaction ? $log->ballTransaction->customer_name : 'Field Collection',
            'member_id' => $log->ballTransaction ? $log->ballTransaction->member_id : null,
            'collector_id' => $log->collector_id,
            'notes' => 'Verified field collection by ' . $log->collector->name . ' for log #' . $log->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Collection verified and balls returned to inventory'
        ]);
    }
}
