<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\MenuCategory;
use App\Models\Counter;
use App\Models\User;

ob_start();

$targetUserId = 19; // Ally Hamidu
$user = User::find($targetUserId);

if (!$user) {
    echo "User 19 not found\n";
} else {
    echo "USER: {$user->name} (Role: {$user->role})\n";

    $counter = Counter::where('assigned_user_id', $targetUserId)->first();

    if (!$counter) {
        echo "NO COUNTER ASSIGNED TO USER 19\n";
    } else {
        echo "ASSIGNED COUNTER: {$counter->name} (ID: {$counter->id})\n";
        echo "COUNTER IS_ALCOHOL: " . ($counter->is_alcohol ? 'YES' : 'NO') . "\n";
        
        $categories = MenuCategory::where('is_alcohol', $counter->is_alcohol)
            ->active()
            ->get();
        
        echo "\nMATCHING CATEGORIES FOR THIS COUNTER:\n";
        foreach ($categories as $cat) {
            echo "ID: {$cat->id} | Name: {$cat->name} | Alcohol: " . ($cat->is_alcohol ? 'YES' : 'NO') . "\n";
        }
    }
}

echo "\n--- ALL COUNTERS ---\n";
foreach (Counter::all() as $c) {
    echo "ID: {$c->id} | Name: {$c->name} | Alcohol: " . ($c->is_alcohol ? 'YES' : 'NO') . " | User ID: " . ($c->assigned_user_id ?? 'NULL') . "\n";
}

echo "\n--- ALL MENU CATEGORIES ---\n";
foreach (MenuCategory::all() as $cat) {
    echo "ID: {$cat->id} | Name: {$cat->name} | Alcohol: " . ($cat->is_alcohol ? 'YES' : 'NO') . " | Active: " . ($cat->is_active ? 'YES' : 'NO') . "\n";
}

$output = ob_get_clean();
file_put_contents('c:/xampp/htdocs/golf_app/debug_output.txt', $output);
echo "Debug output written to debug_output.txt\n";
