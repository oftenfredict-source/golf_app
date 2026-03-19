<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrivingRangeController;
use App\Http\Controllers\BallManagementController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\FoodBeverageController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EntryGateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MembershipController;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- */

// Root - Redirect to Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', function () {
    return response()->view('auth.login')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
    'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Password Reset via OTP (SMS)
use App\Http\Controllers\PasswordResetController;
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password/send-otp', [PasswordResetController::class, 'sendOtp'])->name('password.otp.send');
Route::get('/forgot-password/reset', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/forgot-password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset.update');


// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/categories', [InventoryController::class, 'storeCategory'])->name('inventory.categories.store');
    Route::post('/inventory/items', [InventoryController::class, 'store'])->name('inventory.items.store');
    Route::put('/inventory/items/{item}', [InventoryController::class, 'update'])->name('inventory.items.update');
    Route::delete('/inventory/items/{item}', [InventoryController::class, 'destroy'])->name('inventory.items.destroy');
    // PHP notation for route parameters in put/patch usually uses {item}
    Route::post('/inventory/items/{item}/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.items.adjust');
    Route::post('/inventory/menu-items/{item}/adjust', [InventoryController::class, 'adjustMenuItemStock'])->name('inventory.menu-items.adjust');

    // --- SHARED STAFF UTILITIES ---

    // Member Search & Utility API
    Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/members/search', [MemberController::class , 'search'])->name('members.search');
            Route::get('/members/{id}/balance', [MemberController::class , 'getBalance'])->name('members.balance');
            Route::get('/members/{id}', [MemberController::class , 'show'])->name('members.show');
        }
        );

        // Ball Management (Accessible to Ball Managers)
        Route::prefix('golf-services')->name('golf-services.')->group(function () {
            Route::get('/ball-management', [BallManagementController::class , 'index'])->name('ball-management');
            Route::post('/ball-management/issue', [BallManagementController::class , 'issue'])->name('ball-management.issue');
            Route::post('/ball-management/return', [BallManagementController::class , 'return'])->name('ball-management.return');
            Route::post('/ball-management/add-stock', [BallManagementController::class , 'addStock'])->name('ball-management.add-stock');
            Route::post('/ball-management/damaged', [BallManagementController::class , 'markDamaged'])->name('ball-management.damaged');
            Route::post('/ball-management/update-transaction', [BallManagementController::class , 'updateTransaction'])->name('ball-management.update-transaction');
            
            // Ball Collection (Storekeeper & Admin)
            Route::get('/ball-collection', [\App\Http\Controllers\BallCollectionController::class, 'index'])->name('ball-collection.index');
            Route::post('/ball-collection/assign', [\App\Http\Controllers\BallCollectionController::class, 'assign'])->name('ball-collection.assign');
            Route::post('/ball-collection/{id}/verify', [\App\Http\Controllers\BallCollectionController::class, 'verify'])->name('ball-collection.verify');
            
            // Collector CRUD
            Route::post('/ball-collection/collectors', [\App\Http\Controllers\BallCollectionController::class, 'storeCollector'])->name('ball-collection.collectors.store');
            Route::put('/ball-collection/collectors/{id}', [\App\Http\Controllers\BallCollectionController::class, 'updateCollector'])->name('ball-collection.collectors.update');
            Route::delete('/ball-collection/collectors/{id}', [\App\Http\Controllers\BallCollectionController::class, 'destroyCollector'])->name('ball-collection.collectors.destroy');
        });

        // --- ADMIN, RECEPTION, STOREKEEPER & COUNTER PROTECTED BLOCK ---
        Route::middleware('role:admin,reception,storekeeper,counter,manager,chef,waiter')->group(function () {

            // Golf Services - Admin Only
            Route::prefix('golf-services')->name('golf-services.')->group(function () {
                    // Driving Range
                    Route::get('/driving-range', [DrivingRangeController::class , 'index'])->name('driving-range');
                    Route::post('/driving-range/sessions', [DrivingRangeController::class , 'store'])->name('driving-range.store');
                    Route::post('/driving-range/sessions/{id}/end', [DrivingRangeController::class , 'endSession'])->name('driving-range.end');
                    Route::post('/driving-range/sessions/{id}/cancel', [DrivingRangeController::class , 'cancelSession'])->name('driving-range.cancel');
                    Route::post('/driving-range/config', [DrivingRangeController::class , 'updateConfig'])->name('driving-range.config');

                    Route::get('/range-configuration', function () {
                            return redirect()->route('settings.configuration') . '#driving-range';
                        }
                        )->name('range-configuration');

                        Route::get('/pricing-configuration', function () {
                            return redirect()->route('settings.configuration') . '#driving-range';
                        }
                        )->name('pricing-configuration');

                        // Equipment Rental
                        Route::get('/equipment-rental', [EquipmentController::class , 'rentalIndex'])->name('equipment-rental');
                        Route::post('/equipment-rental', [EquipmentController::class , 'createRental'])->name('equipment-rental.store');
                        Route::post('/equipment-rental/{id}/return', [EquipmentController::class , 'returnRental'])->name('equipment-rental.return');

                        Route::get('/rental-configuration', function () {
                            return redirect()->route('settings.configuration') . '#equipment-rental';
                        }
                        )->name('rental-configuration');

                        Route::post('/rental-configuration', [EquipmentController::class , 'updateRentalConfig'])->name('rental-configuration.update');

                        // Equipment Sales
                        Route::get('/equipment-sales', [EquipmentController::class , 'salesIndex'])->name('equipment-sales');
                        Route::post('/equipment-sales', [EquipmentController::class , 'createSale'])->name('equipment-sales.store');
                        Route::get('/equipment-sales/{id}', [EquipmentController::class , 'showSale'])->name('equipment-sales.show');
                        Route::get('/equipment-sales/{id}/receipt', [EquipmentController::class , 'showReceipt'])->name('equipment-sales.receipt');

                        // Equipment Management
                        Route::post('/equipment', [EquipmentController::class , 'storeEquipment'])->name('equipment.store');
                        Route::get('/equipment/{id}', [EquipmentController::class , 'getEquipment'])->name('equipment.show');
                        Route::put('/equipment/{id}', [EquipmentController::class , 'updateEquipment'])->name('equipment.update');
                        Route::delete('/equipment/{id}', [EquipmentController::class , 'deleteEquipment'])->name('equipment.delete');
                    }
                    );

                    // Services (F&B and Counters)
                    Route::prefix('services')->name('services.')->group(function () {
                    Route::get('/food-beverage', [FoodBeverageController::class , 'index'])->name('food-beverage');
                    Route::get('/counter/dashboard', [CounterController::class , 'dashboard'])->name('counter.dashboard');
                    Route::post('/menu-items', [FoodBeverageController::class , 'storeMenuItem'])->name('menu-items.store');
                    Route::put('/menu-items/{id}', [FoodBeverageController::class , 'updateMenuItem'])->name('menu-items.update');
                    Route::post('/categories', [FoodBeverageController::class , 'storeCategory'])->name('categories.store');
                    Route::put('/categories/{id}', [FoodBeverageController::class , 'updateCategory'])->name('categories.update');
                    Route::delete('/categories/{id}', [FoodBeverageController::class , 'deleteCategory'])->name('categories.delete');
                    Route::get('/orders', [FoodBeverageController::class , 'orders'])->name('orders');
                    Route::get('/orders/{id}', [FoodBeverageController::class , 'show'])->name('orders.show');
                    Route::post('/orders', [FoodBeverageController::class , 'createOrder'])->name('orders.store');
                    Route::post('/orders/{id}/status', [FoodBeverageController::class , 'updateOrderStatus'])->name('orders.status');

                    Route::get('/counter-management', [CounterController::class , 'index'])->name('counter-management');
                    Route::post('/counters', [CounterController::class , 'store'])->name('counters.store');
                    Route::put('/counters/{id}', [CounterController::class , 'update'])->name('counters.update');
                    Route::post('/counters/{id}/assign', [CounterController::class , 'assign'])->name('counters.assign');
                }
                );

                // Payments - Admin Restricted Actions
                Route::prefix('payments')->name('payments.')->group(function () {
                    Route::get('/transactions', [TransactionController::class , 'index'])->name('transactions');
                    Route::get('/transactions/{id}', [TransactionController::class , 'show'])->name('transactions.show');
                    Route::get('/transactions/{id}/receipt', [TransactionController::class , 'receipt'])->name('transactions.receipt');
                    Route::post('/transactions', [TransactionController::class , 'store'])->name('transactions.store');

                    Route::get('/top-ups', [TopupController::class , 'index'])->name('top-ups');
                    Route::get('/top-ups/data', [TopupController::class , 'getTopups'])->name('top-ups.data');
                    Route::post('/top-ups', [TopupController::class , 'store'])->name('top-ups.store');
                    Route::get('/top-ups/{id}/receipt', [TopupController::class , 'receipt'])->name('top-ups.receipt');

                    Route::get('/upi-management', [MemberController::class , 'index'])->name('upi-management');
                    Route::get('/generate-card/{id?}', [MemberController::class , 'generateCard'])->name('generate-card');
                    Route::match (['get', 'post'], '/members', [MemberController::class , 'store'])->name('members.store');
                    Route::put('/members/{id}', [MemberController::class , 'update'])->name('members.update');
                    Route::get('/members/{id}/transactions', [MemberController::class , 'getTransactions'])->name('members.transactions');
                    Route::get('/members/{id}/transactions/pdf', [MemberController::class , 'transactionsPdf'])->name('members.transactions.pdf');
                    Route::get('/generate-card/{id}/pdf', [MemberController::class , 'generateCardPdf'])->name('generate-card.pdf');
                    Route::post('/members/check-card', [MemberController::class , 'checkCard'])->name('members.check-card');
                    Route::post('/members/charge', [MemberController::class , 'chargeService'])->name('members.charge');
                    Route::post('/members/{id}/adjust-balance', [MemberController::class , 'adjustBalance'])->name('members.adjust-balance');
                    Route::post('/members/{id}/toggle-issuance', [MemberController::class , 'toggleCardIssued'])->name('members.toggle-issuance');
                    Route::post('/members/{id}/card-status', [MemberController::class , 'updateCardStatus'])->name('members.update-card-status');
                }
                );

                // Reports
                Route::prefix('reports')->name('reports.')->group(function () {
                    Route::get('/', [ReportController::class , 'index'])->name('index');
                    Route::get('/revenue', [ReportController::class , 'revenue'])->name('revenue');
                    Route::get('/revenue-reports', [ReportController::class , 'revenue'])->name('revenue-reports');
                    Route::get('/revenue/pdf', [ReportController::class , 'revenuePdf'])->name('revenue.pdf');
                    Route::get('/members', [ReportController::class , 'members'])->name('members');
                    Route::get('/members/pdf', [ReportController::class , 'membersPdf'])->name('members.pdf');
                    Route::get('/transactions', [ReportController::class , 'transactions'])->name('transactions');
                    Route::get('/transactions/pdf', [ReportController::class , 'transactionsPdf'])->name('transactions.pdf');
                    Route::get('/daily-summary', [ReportController::class , 'dailySummary'])->name('daily-summary');
                    Route::get('/daily-summary/pdf', [ReportController::class , 'dailySummaryPdf'])->name('daily-summary.pdf');
                }
                );

                // Access Control
                Route::prefix('access-control')->name('access-control.')->group(function () {
                    Route::get('/entry-gates', [EntryGateController::class , 'index'])->name('entry-gates');
                    Route::post('/entry-gates', [EntryGateController::class , 'store'])->name('entry-gates.store');
                    Route::put('/entry-gates/{id}', [EntryGateController::class , 'update'])->name('entry-gates.update');
                    Route::delete('/entry-gates/{id}', [EntryGateController::class , 'destroy'])->name('entry-gates.destroy');
                    Route::post('/entry-gates/{id}/toggle', [EntryGateController::class , 'toggleStatus'])->name('entry-gates.toggle');
                    Route::post('/entry-gates/scan', [EntryGateController::class , 'scanCard'])->name('entry-gates.scan');
                    Route::post('/entry-gates/global-mode', [EntryGateController::class , 'updateGlobalMode'])->name('entry-gates.global-mode');
                    Route::get('/entry-gates/members/search', [EntryGateController::class , 'searchMembers'])->name('entry-gates.members.search');
                    Route::get('/entry-gates/logs', [EntryGateController::class , 'getLogs'])->name('entry-gates.logs');
                    Route::get('/entry-gates/logs/export', [EntryGateController::class , 'exportLogs'])->name('entry-gates.logs.export');
                }
                );

                // Inventory
                Route::prefix('inventory')->name('inventory.')->group(function () {
                    Route::get('/', function () {
                            return view('inventory.index'); }
                        )->name('index');
                    }
                    );

                    // Admin Activity & Logs
                    Route::prefix('logs')->name('logs.')->group(function () {
                    Route::get('/activity-logs', [LogsController::class , 'activityLogs'])->name('activity-logs');
                    Route::get('/activity-logs/{id}', [LogsController::class , 'show'])->name('activity-logs.show');
                    Route::get('/activity-logs/export', [LogsController::class , 'exportLogs'])->name('activity-logs.export');
                }
                );

                // Counters and Tables
                Route::prefix('counters')->name('counters.')->group(function () {
                    Route::get('/', [CounterController::class , 'index'])->name('index');
                    Route::post('/', [CounterController::class , 'store'])->name('store');
                    Route::put('/{id}', [CounterController::class , 'update'])->name('update');
                    Route::delete('/{id}', [CounterController::class , 'destroy'])->name('destroy');
                    
                    // Table Management Section
                    Route::prefix('tables')->name('tables.')->group(function () {
                        Route::get('/', [\App\Http\Controllers\TableController::class, 'index'])->name('index');
                        Route::post('/', [\App\Http\Controllers\TableController::class, 'store'])->name('store');
                        Route::put('/{id}', [\App\Http\Controllers\TableController::class, 'update'])->name('update');
                        Route::delete('/{id}', [\App\Http\Controllers\TableController::class, 'destroy'])->name('destroy');
                    });
                }
                );

                // Kitchen / Chef Dashboard
                Route::prefix('kitchen')->name('kitchen.')->group(function () {
                    Route::get('/dashboard', [\App\Http\Controllers\KitchenController::class, 'dashboard'])->name('dashboard');
                    Route::post('/order/{id}/status', [\App\Http\Controllers\KitchenController::class, 'updateStatus'])->name('order.status');
                });

                // Waiter Dashboard
                Route::prefix('waiter')->name('waiter.')->group(function () {
                    Route::get('/dashboard', [\App\Http\Controllers\WaiterController::class, 'dashboard'])->name('dashboard');
                    Route::post('/order/{id}/serve', [\App\Http\Controllers\WaiterController::class, 'serveOrder'])->name('order.serve');
                    Route::post('/table/{id}/clear', [\App\Http\Controllers\WaiterController::class, 'clearTable'])->name('table.clear');
                });

                // Notifications
                Route::prefix('notifications')->name('notifications.')->group(function () {
                    Route::get('/', [NotificationController::class , 'index'])->name('index');
                    Route::get('/fetch', [NotificationController::class , 'fetch'])->name('fetch');
                    Route::post('/{id}/read', [NotificationController::class , 'markAsRead'])->name('read');
                    Route::post('/read-all', [NotificationController::class , 'markAllAsRead'])->name('read-all');
                }
                );

                // System Settings & Communication
                Route::prefix('settings')->name('settings.')->group(function () {
                    // User Management
                    Route::get('/users', [UserController::class, 'index'])->name('users');
                    Route::post('/users', [UserController::class, 'store'])->name('users.store');
                    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
                    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
                    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.delete');

                    Route::get('/configuration', [SettingsController::class , 'configuration'])->name('configuration');
                    Route::post('/access-control-config', [SettingsController::class , 'saveAccessControlConfig'])->name('access-control-config.save');

                    Route::get('/organization', function () {
                            return view('settings.organization');
                        }
                        )->name('organization');

                        Route::put('/organization', function (\Illuminate\Http\Request $request) {
                            $settings = [];
                            $settingsFile = storage_path('app/organization_settings.json');
                            if (file_exists($settingsFile)) {
                                $settings = json_decode(file_get_contents($settingsFile), true) ?? [];
                            }
                            if ($request->hasFile('logo')) {
                                $file = $request->file('logo');
                                $filename = 'organization/logo_' . time() . '.' . $file->getClientOriginalExtension();
                                $file->storeAs('', $filename, 'public');
                                if (isset($settings['logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['logo'])) {
                                    \Illuminate\Support\Facades\Storage::disk('public')->delete($settings['logo']);
                                }
                                $settings['logo'] = $filename;
                            }
                            elseif ($request->has('remove_logo')) {
                                if (isset($settings['logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['logo'])) {
                                    \Illuminate\Support\Facades\Storage::disk('public')->delete($settings['logo']);
                                }
                                unset($settings['logo']);
                            }
                            if ($request->hasFile('favicon')) {
                                $file = $request->file('favicon');
                                $filename = 'organization/favicon_' . time() . '.' . $file->getClientOriginalExtension();
                                $file->storeAs('', $filename, 'public');
                                if (isset($settings['favicon']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['favicon'])) {
                                    \Illuminate\Support\Facades\Storage::disk('public')->delete($settings['favicon']);
                                }
                                $settings['favicon'] = $filename;
                            }
                            $fields = [
                                'org_name', 'org_short_name', 'org_email', 'org_phone', 'org_mobile', 'org_website',
                                'org_address', 'org_city', 'org_region', 'org_country', 'org_postal_code', 'org_tin',
                                'primary_color', 'secondary_color', 'timezone', 'currency', 'date_format', 'time_format',
                                'language', 'session_timeout', 'membership_fee', 'guest_fee', 'opening_time', 'closing_time',
                                'driving_range_bays', 'course_holes', 'ball_bucket_size', 'receipt_prefix', 'invoice_prefix',
                                'receipt_footer', 'terms_conditions'
                            ];
                            foreach ($fields as $field) {
                                if ($request->has($field))
                                    $settings[$field] = $request->input($field);
                            }
                            $settings['allow_guest_bookings'] = $request->has('allow_guest_bookings');
                            $settings['require_member_card'] = $request->has('require_member_card');
                            $settings['send_sms_notifications'] = $request->has('send_sms_notifications');
                            if (!is_dir(dirname($settingsFile)))
                                mkdir(dirname($settingsFile), 0755, true);
                            file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
                            return back()->with('success', 'Organization settings saved successfully!');
                        }
                        )->name('organization.update');

                        Route::get('/communication', [SettingsController::class , 'communication'])->name('communication');
                        Route::post('/communication/save', [SettingsController::class , 'saveCommunicationSettings'])->name('communication.save');
                        Route::post('/communication/test-sms', [SettingsController::class , 'testSms'])->name('communication.test-sms');
                        Route::get('/system-health', function () {
                            return view('settings.system-health');
                        }
                        )->name('system-health');
                    }
                    );
                }
                );

                // --- SHARED PROFILE ROUTES ---
                Route::get('/profile', function () {
            return view('profile.index');
        }
        )->name('profile');

        Route::put('/profile', function (\Illuminate\Http\Request $request) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user)
                $user->update($request->only(['name', 'email', 'phone']));
            return back()->with('success', 'Profile updated successfully.');
        }
        )->name('profile.update');

        Route::post('/profile/avatar', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user && $request->hasFile('avatar')) {
                if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
                $file = $request->file('avatar');
                $uniqueName = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('avatars', $uniqueName, 'public');
                $user->avatar = 'avatars/' . $uniqueName;
                $user->save();
            }
            return back()->with('success', 'Profile photo updated successfully.');
        }
        )->name('profile.avatar');

        Route::delete('/profile/avatar', function () {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user && $user->avatar) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
                $user->update(['avatar' => null]);
            }
            return back()->with('success', 'Profile photo removed.');
        }
        )->name('profile.avatar.delete');

        Route::put('/profile/password', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user && \Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password)]);
                return back()->with('success', 'Password updated successfully.');
            }
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        )->name('profile.password');
    });
