<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\MenuCategory;
use App\Models\Counter;

echo "--- MENU CATEGORIES ---\n";
foreach (MenuCategory::all() as $cat) {
    echo "ID: {$cat->id} | Name: {$cat->name} | Alcohol: " . ($cat->is_alcohol ? "YES" : "NO") . " | Status: {$cat->status} | Active: " . ($cat->is_active ? "YES" : "NO") . "\n";
}

echo "\n--- COUNTERS ---\n";
foreach (Counter::all() as $counter) {
    echo "ID: {$counter->id} | Name: {$counter->name} | Alcohol: " . ($counter->is_alcohol ? "YES" : "NO") . " | Assigned User ID: {$counter->assigned_user_id}\n";
}
