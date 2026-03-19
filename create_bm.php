<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::firstOrCreate(
['email' => 'ballmanager@golf.com'],
[
    'name' => 'Ball Manager',
    'password' => \Illuminate\Support\Facades\Hash::make('password')
]
);
$user->role = 'ball_manager';
$user->save();

echo "User created: " . $user->email . " with role: " . $user->role . "\n";
