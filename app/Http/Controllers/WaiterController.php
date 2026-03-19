<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WaiterController extends Controller
{
    /**
     * Display the Waiter Dashboard
     */
    public function dashboard()
    {
        // Get all tables
        $tables = Table::orderBy('table_number', 'asc')->get();

        // Get orders that are 'ready' to be served
        $readyOrders = Order::with(['items.menuItem', 'member'])
            ->where('status', 'ready')
            ->orderBy('updated_at', 'asc')
            ->get();

        // For now, let's show all active table orders
        $activeTableOrders = Order::with(['items.menuItem', 'member'])
            ->active()
            ->whereNotNull('table_number')
            ->get();

        // Get today's revenue (F&B only)
        $revenueToday = Transaction::today()
            ->where('type', 'payment')
            ->where('category', 'food_beverage')
            ->sum('amount');

        // Get today's detailed sales log (F&B only)
        $salesToday = Transaction::today()
            ->where('type', 'payment')
            ->where('category', 'food_beverage')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
            
        // Get menu categories with items for ordering
        $menuCategories = \App\Models\MenuCategory::with(['items' => function($q) {
                $q->where('is_available', true);
            }])
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->get();
            
        return view('waiter.dashboard', compact('tables', 'readyOrders', 'activeTableOrders', 'revenueToday', 'salesToday', 'menuCategories'));
    }

    /**
     * Mark an order as served
     */
    public function serveOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $oldStatus = $order->status;

        $order->update(['status' => 'served']);

        // Check if this was a table order and if we should update table status
        // Usually 'served' means the table is now 'occupied' if it wasn't already
        if ($order->table_number) {
            $table = Table::where('table_number', $order->table_number)->first();
            if ($table && $table->status === 'available') {
                $table->update(['status' => 'occupied']);
            }
        }

        ActivityLog::log(
            'service',
            'updated',
            "Order #{$order->order_number} marked as SERVED by Waiter",
            'Order',
            $order->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Order marked as served.',
            'order' => $order
        ]);
    }

    /**
     * Clear a table - mark it as available again when guests leave
     */
    public function clearTable(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        $oldStatus = $table->status;

        // Auto-complete any active orders for this table session
        $activeOrders = Order::where('table_number', $table->table_number)
            ->active()
            ->get();
            
        foreach ($activeOrders as $order) {
            $order->update(['status' => 'complete']);
            
            ActivityLog::log('service', 'completed', "Order #{$order->order_number} auto-completed as Table #{$table->table_number} was cleared", 'Order', $order->id);
        }

        $table->update(['status' => 'available']);

        ActivityLog::log(
            'service',
            'updated',
            "Table #{$table->table_number} cleared (was: {$oldStatus}) by Waiter",
            'Table',
            $table->id
        );

        return response()->json([
            'success' => true,
            'message' => "Table #{$table->table_number} is now Available.",
            'table' => $table
        ]);
    }
}
