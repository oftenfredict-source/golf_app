<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\MenuCategory;
use App\Models\Counter;
use App\Models\User;

$targetUserId = 19; // Hamidu
$user = User::find($targetUserId);

if (!$user) {
    die("User 19 not found\n");
}

echo "USER: {$user->name} (Role: {$user->role})\n";

$counter = Counter::where('assigned_user_id', $targetUserId)->first();

if (!$counter) {
    echo "NO COUNTER ASSIGNED TO USER 19\n";
    // Check all counters
    echo "\nALL COUNTERS:\n";
    foreach (Counter::all() as $c) {
        echo "ID: {$c->id} | Name: {$c->name} | Alcohol: " . ($c->is_alcohol ? 'YES' : 'NO') . " | User ID: " . ($c->assigned_user_id ?? 'NULL') . "\n";
    }
} else {
    echo "ASSIGNED COUNTER: {$counter->name} (ID: {$counter->id})\n";
    echo "COUNTER IS_ALCOHOL: " . ($counter->is_alcohol ? 'YES' : 'NO') . "\n";
    
    // Check categories that would be shown to this counter
    $categories = MenuCategory::where('is_alcohol', $counter->is_alcohol)
        ->where('is_active', true)
        ->get();
    
    echo "\nMATCHING CATEGORIES FOR THIS COUNTER:\n";
    if ($categories->isEmpty()) {
        echo "None found.\n";
    } else {
        foreach ($categories as $cat) {
            echo "ID: {$cat->id} | Name: {$cat->name} | Alcohol: " . ($cat->is_alcohol ? 'YES' : 'NO') . "\n";
        }
    }
}

echo "\nALL MENU CATEGORIES:\n";
foreach (MenuCategory::all() as $cat) {
    echo "ID: {$cat->id} | Name: {$cat->name} | Alcohol: " . ($cat->is_alcohol ? 'YES' : 'NO') . "\n";
}
