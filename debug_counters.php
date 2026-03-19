<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Counter;

echo "--- USERS WITH ROLE 'counter' ---\n";
$counterUsers = User::where('role', 'counter')->get();
foreach ($counterUsers as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
    $counter = Counter::where('assigned_user_id', $user->id)->first();
    if ($counter) {
        echo "   -> ASSIGNED TO: {$counter->name} (ID: {$counter->id}) | Active: " . ($counter->is_active ? 'Yes' : 'No') . "\n";
    } else {
        echo "   -> NO COUNTER ASSIGNED\n";
    }
}

echo "\n--- ALL COUNTERS ---\n";
foreach (Counter::all() as $counter) {
    echo "ID: {$counter->id} | Name: {$counter->name} | Assigned UID: {$counter->assigned_user_id}\n";
}
