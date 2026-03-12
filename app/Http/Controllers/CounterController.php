<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use Illuminate\Http\Request;

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
}
