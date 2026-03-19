<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableController extends Controller
{
    /**
     * Display a listing of the tables.
     */
    public function index()
    {
        $tables = Table::orderBy('table_number', 'asc')->get();
        return view('services.table-management', compact('tables'));
    }

    /**
     * Store a newly created table in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number',
            'type' => 'required|in:normal,vip',
            'status' => 'required|in:available,occupied,reserved',
            'notes' => 'nullable|string'
        ]);

        $table = Table::create([
            'ulid' => (string) Str::ulid(),
            'table_number' => $request->table_number,
            'type' => $request->type,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        ActivityLog::log(
            'management',
            'created',
            "New Table #{$table->table_number} created by " . auth()->user()->name,
            'Table',
            $table->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Table created successfully',
            'table' => $table
        ]);
    }

    /**
     * Update the specified table in storage.
     */
    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number,' . $id,
            'type' => 'required|in:normal,vip',
            'status' => 'required|in:available,occupied,reserved',
            'notes' => 'nullable|string'
        ]);

        $oldTableNumber = $table->table_number;
        $table->update($request->all());

        ActivityLog::log(
            'management',
            'updated',
            "Table #{$oldTableNumber} updated by " . auth()->user()->name,
            'Table',
            $table->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully',
            'table' => $table
        ]);
    }

    /**
     * Remove the specified table from storage.
     */
    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $tableNumber = $table->table_number;
        
        $table->delete();

        ActivityLog::log(
            'management',
            'deleted',
            "Table #{$tableNumber} deleted by " . auth()->user()->name,
            'Table',
            $id
        );

        return response()->json([
            'success' => true,
            'message' => 'Table deleted successfully'
        ]);
    }
}
