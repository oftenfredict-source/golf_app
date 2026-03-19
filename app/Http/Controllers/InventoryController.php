<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isCounter = $user->role === 'counter';
        
        $categories = InventoryCategory::all();
        $itemsQuery = InventoryItem::with('category');
        
        // General inventory is usually for storekeepers/admins, 
        // but we'll show it or hide it based on preference.
        // For now, let's keep general inventory for admins/storekeepers only.
        $items = $isCounter ? collect() : $itemsQuery->get();
        
        $menuItemsQuery = \App\Models\MenuItem::with('category');
        
        if ($isCounter) {
            // Get the user's assigned counter to check specialization
            $counter = \App\Models\Counter::where('assigned_user_id', $user->id)->first();
            if ($counter) {
                $menuItemsQuery->whereHas('category', function($q) use ($counter) {
                    $q->where('is_alcohol', $counter->is_alcohol);
                });
            } else {
                // If no counter assigned, show nothing for safety
                $menuItemsQuery->whereRaw('1 = 0');
            }
        }
        
        $menuItems = $menuItemsQuery->get();
        
        if (request()->wantsJson()) {
            return response()->json([
                'items' => $items,
                'categories' => $categories,
                'menuItems' => $menuItems,
                'userRole' => $user->role
            ]);
        }
        
        return view('inventory.index', compact('items', 'categories', 'menuItems'));
    }

    public function adjustMenuItemStock(Request $request, \App\Models\MenuItem $item)
    {
        $user = auth()->user();
        
        $request->validate([
            'type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:0'
        ]);

        // Security check for counter role
        if ($user->role === 'counter') {
            $counter = \App\Models\Counter::where('assigned_user_id', $user->id)->first();
            if (!$counter || ($item->category && $item->category->is_alcohol != $counter->is_alcohol)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Unauthorized. You can only manage stock for your assigned category (Alcohol/Non-Alcohol).'
                ], 403);
            }
        }

        $qty = $request->quantity;
        
        if ($request->type === 'add') {
            $item->increment('stock_quantity', $qty);
        } elseif ($request->type === 'remove') {
            $item->decrement('stock_quantity', min($item->stock_quantity, $qty));
        } else {
            $item->update(['stock_quantity' => $qty]);
        }

        return response()->json(['success' => true, 'new_quantity' => $item->stock_quantity]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|unique:inventory_items',
            'name' => 'required',
            'category_id' => 'required|exists:inventory_categories,id',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'unit' => 'nullable|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $item = InventoryItem::create($validated);

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:inventory_categories',
            'description' => 'nullable|string',
        ]);

        $category = InventoryCategory::create($validated);

        return response()->json(['success' => true, 'category' => $category]);
    }

    public function update(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'item_code' => 'required|unique:inventory_items,item_code,' . $item->id,
            'name' => 'required',
            'category_id' => 'required|exists:inventory_categories,id',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'unit' => 'nullable|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $item->update($validated);

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function adjustStock(Request $request, InventoryItem $item)
    {
        $request->validate([
            'type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string'
        ]);

        $qty = $request->quantity;
        
        if ($request->type === 'add') {
            $item->increment('quantity', $qty);
        } elseif ($request->type === 'remove') {
            $item->decrement('quantity', min($item->quantity, $qty));
        } else {
            $item->update(['quantity' => $qty]);
        }

        return response()->json(['success' => true, 'new_quantity' => $item->quantity]);
    }

    public function destroy(InventoryItem $item)
    {
        $item->delete();
        return response()->json(['success' => true]);
    }
}




