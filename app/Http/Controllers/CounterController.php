<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounterController extends Controller
{
    public function index()
    {
        $counters = Counter::with('assignedUser')->orderBy('name')->get();
        $activeCounters = $counters->where('is_active', true);
        return view('services.counter-management', compact('counters', 'activeCounters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:food,beverage,equipment,general,chakula,kahawa,jikoni',
            'tier' => 'nullable|in:normal,vip',
            'location' => 'nullable|string|max:255',
            'is_alcohol' => 'boolean',
            'is_active' => 'boolean',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $counter = Counter::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Counter created successfully',
            'counter' => $counter
        ]);
    }

    public function update(Request $request, $id)
    {
        $counter = Counter::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:food,beverage,equipment,general,chakula,kahawa,jikoni',
            'tier' => 'nullable|in:normal,vip',
            'location' => 'nullable|string|max:255',
            'is_alcohol' => 'boolean',
            'is_active' => 'boolean',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $counter->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Counter updated successfully',
            'counter' => $counter
        ]);
    }

    public function destroy($id)
    {
        $counter = Counter::findOrFail($id);
        $counter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Counter deleted successfully'
        ]);
    }

    public function assign(Request $request, $id)
    {
        $counter = Counter::findOrFail($id);
        
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        $counter->update([
            'assigned_user_id' => $request->user_id ?: null
        ]);

        $message = $request->user_id 
            ? 'User assigned successfully' 
            : 'User assignment removed successfully';

        return response()->json([
            'success' => true,
            'message' => $message,
            'counter' => $counter->load('assignedUser')
        ]);
    }

    public function dashboard()
    {
        $user = Auth::user();
        $counter = Counter::where('assigned_user_id', $user->id)->first();

        if (!$counter) {
            // Fallback: If no counter is assigned to this exact user, maybe they are admin?
            // For now, let's just show a "Not Assigned" state in the view if $counter is null.
        }

        $activeOrders = [];
        if ($counter) {
            $activeOrders = Order::with(['items.menuItem', 'member'])
                ->where('counter_id', $counter->id)
                ->whereIn('status', ['saved', 'pending', 'preparing', 'ready'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $stats = [
            'orders_today' => $counter ? Order::where('counter_id', $counter->id)->today()->count() : 0,
            'revenue_today' => $counter ? Transaction::today()->where('type', 'payment')->where('category', 'food_beverage')->where('reference_type', 'order')->whereIn('reference_id', Order::where('counter_id', $counter->id)->pluck('id'))->sum('amount') : 0,
            'pending_count' => count($activeOrders),
        ];

        // Fetch Menu Categories and Items for POS
        $categories = collect([]);
        if ($counter) {
            $categories = MenuCategory::with(['items' => function($q) {
                $q->where('is_available', true);
            }])
            ->where(function($q) use ($counter) {
                if ($counter->is_alcohol) {
                    $q->where('is_alcohol', true);
                } elseif ($counter->is_food) {
                    $q->where('is_food', true);
                } else {
                    $q->where('is_alcohol', false)->where('is_food', false);
                }
            });

            $categories = $categories->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();

            // If no category exists for this specialization, create a default one for this counter
            if ($categories->isEmpty()) {
                $defaultCategory = MenuCategory::create([
                    'name' => $counter->name . ' Items',
                    'is_alcohol' => $counter->is_alcohol,
                    'status' => 'active',
                    'is_active' => true,
                    'sort_order' => 0
                ]);
                
                // Re-fetch to include the new category
                $categories = MenuCategory::where('id', $defaultCategory->id)
                    ->with('items')
                    ->get();
            }
        }

        return view('services.counter-dashboard', compact('counter', 'activeOrders', 'stats', 'categories'));
    }
}
