<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\MenuCategory;
use App\Models\Counter;
use App\Models\User;

echo "--- ALL MENU CATEGORIES ---\n";
$cats = MenuCategory::all();
if ($cats->isEmpty()) {
    echo "No categories found in database.\n";
} else {
    foreach ($cats as $cat) {
        echo "ID: {$cat->id} | Name: {$cat->name} | Alcohol: " . ($cat->is_alcohol ? "YES" : "NO") . " | Status: {$cat->status} | Active: " . ($cat->is_active ? "YES" : "NO") . "\n";
    }
}

echo "\n--- COUNTERS & ASSIGNMENTS ---\n";
$counters = Counter::all();
if ($counters->isEmpty()) {
    echo "No counters found in database.\n";
} else {
    foreach ($counters as $counter) {
        $userName = $counter->assigned_user_id ? (User::find($counter->assigned_user_id)->name ?? 'Unknown') : 'Unassigned';
        echo "ID: {$counter->id} | Name: {$counter->name} | Alcohol: " . ($counter->is_alcohol ? "YES" : "NO") . " | User: $userName (ID: {$counter->assigned_user_id})\n";
    }
}
