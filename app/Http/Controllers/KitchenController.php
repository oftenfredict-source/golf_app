<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KitchenController extends Controller
{
    /**
     * Display the Kitchen Dashboard (KDS)
     */
    public function dashboard()
    {
        // Get active orders that are not served/completed
        // We filter for orders that have non-alcoholic items (food)
        // In this system, we'll assume anything that is NOT alcohol and belongs to 
        // a "kitchen" or "food" related category/counter needs chef attention.
        // For now, let's show all orders that are 'saved' or 'pending' and have food items.
        
        $activeOrders = Order::with(['items.menuItem.category', 'member'])
            ->whereIn('status', ['saved', 'pending', 'preparing', 'ready'])
            ->whereHas('items.menuItem.category', function($q) {
                $q->where('is_food', true);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $stats = [
            'pending_count' => $activeOrders->whereIn('status', ['saved', 'pending'])->count(),
            'preparing_count' => $activeOrders->where('status', 'preparing')->count(),
            'ready_count' => $activeOrders->where('status', 'ready')->count(),
            'completed_today' => Order::whereDate('created_at', Carbon::today())
                ->whereIn('status', ['served', 'complete', 'completed'])
                ->count(),
        ];

        return view('kitchen.dashboard', compact('activeOrders', 'stats'));
    }

    /**
     * Update order status from the kitchen
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:preparing,ready,served'
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        $order->update(['status' => $newStatus]);

        // Log the status change
        ActivityLog::log(
            'kitchen', 
            'updated', 
            "Order #{$order->order_number} status changed from {$oldStatus} to {$newStatus} by Chef", 
            'Order', 
            $order->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Order status updated to ' . ucfirst($newStatus),
            'order' => $order
        ]);
    }
}
