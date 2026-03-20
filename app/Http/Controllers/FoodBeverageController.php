<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class FoodBeverageController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::withCount('items')->where('status', 'active')->orderBy('sort_order')->get();
        $menuItems = MenuItem::with('category')->where('is_available', true)->get();
        // Simplified: Show only saved orders (not completed)
        // Also show pending orders as saved for backward compatibility
        $activeOrders = Order::with('items.menuItem')
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'open_orders' => Order::whereDate('created_at', today())->active()->count(),
            'in_prep' => 0, // Removed
            'ready' => 0, // Removed
            'revenue_today' => Transaction::today()->where('type', 'payment')->where('category', 'food_beverage')->sum('amount'),
        ];

        return view('services.food-beverage', compact('categories', 'menuItems', 'activeOrders', 'stats'));
    }

    public function orders()
    {
        // For now, let's show all active table orders
        $activeOrders = Order::with(['items.menuItem', 'member'])
            ->active()
            ->whereNotNull('table_number')
            ->get();
        
        $todayOrders = Order::with(['items.menuItem', 'member'])
            ->whereNotNull('member_id')
            ->where('payment_method', 'balance')
            ->today()
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('services.orders', compact('activeOrders', 'todayOrders'));
    }

    public function show($id)
    {
        $order = Order::with(['items.menuItem', 'member'])->findOrFail($id);
        
        if (request()->wantsJson() || request()->has('action')) {
            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        }
        
        return view('services.order-details', compact('order'));
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'member_id' => 'required|exists:members,id',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Get member and verify (required - no cash accepted)
        $member = Member::findOrFail($request->member_id);
        
        if ($member->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Member account is not active. Status: ' . $member->status
            ], 400);
        }

        if (!$member->has_full_access) {
            return response()->json([
                'success' => false,
                'message' => 'Custom Members (Golf Only) are not permitted to use Food & Beverage services. This service is reserved for Full Access Cardholders.'
            ], 403);
        }

        $subtotal = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $menuItem = MenuItem::findOrFail($item['menu_item_id']);
            $itemSubtotal = $menuItem->price * $item['quantity'];
            $subtotal += $itemSubtotal;
            
            $orderItems[] = [
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $menuItem->price,
                'subtotal' => $itemSubtotal,
                'special_instructions' => $item['special_instructions'] ?? null,
            ];
        }

        $discount = $request->discount ?? 0;
        
        // Handle VIP Table Pricing, Counter Management & Order Expansion
        $isVip = false;
        $counterId = $request->counter_id;
        $tableId = null;
        $existingOrder = null;
        
        if ($request->table_number) {
            $table = \App\Models\Table::where('table_number', $request->table_number)->first();
            if ($table) {
                $tableId = $table->id;
                
                // Find existing active order for this table to append to
                $existingOrder = Order::where('table_number', $request->table_number)
                    ->whereIn('status', ['saved', 'pending', 'preparing', 'ready'])
                    ->latest()
                    ->first();

                if ($table->type === 'vip') {
                    $isVip = true;
                    // Apply VIP surcharge (10%) ONLY for Standard members. 
                    // VIP/Premier members are exempt as a tiered benefit.
                    if ($member->membership_type === 'standard') {
                        $surcharge = $subtotal * 0.10;
                        $subtotal += $surcharge;
                    }
                }
                
                // Optimize item types check with eager loading
                $itemIds = collect($request->items)->pluck('menu_item_id')->toArray();
                $menuItems = MenuItem::with('category')->whereIn('id', $itemIds)->get()->keyBy('id');

                $hasAlcohol = false;
                $hasFood = false;
                $hasNonAlcohol = false;
                foreach ($request->items as $item) {
                    $mItemId = $item['menu_item_id'];
                    $mItem = $menuItems->get($mItemId);
                    if ($mItem) {
                        if ($mItem->category) {
                            if ($mItem->category->is_alcohol) $hasAlcohol = true;
                            elseif ($mItem->category->is_food) $hasFood = true;
                            else $hasNonAlcohol = true;
                        } else {
                            $hasNonAlcohol = true; // Fallback for items without category
                        }
                    } else {
                        $hasNonAlcohol = true; // Fallback for invalid items
                    }
                }

                // Assign to appropriate counter based on tier/type/alcohol
                $tier = $isVip ? 'vip' : 'normal';
                
                // 1. Try exact match (Tier + Type)
                if ($hasAlcohol) {
                    $counter = \App\Models\Counter::where('tier', $tier)->where('is_alcohol', true)->active()->first();
                } elseif ($hasFood) {
                    $counter = \App\Models\Counter::where('tier', $tier)->where('is_food', true)->active()->first();
                } else {
                    $counter = \App\Models\Counter::where('tier', $tier)->where('is_alcohol', false)->where('is_food', false)->active()->first();
                }
                
                // 2. Fallback to any active counter of that specialization (Ignoring tier)
                if (!$counter) {
                    if ($hasAlcohol) {
                        $counter = \App\Models\Counter::where('is_alcohol', true)->active()->first();
                    } elseif ($hasFood) {
                        $counter = \App\Models\Counter::where('is_food', true)->active()->first();
                    } else {
                        $counter = \App\Models\Counter::where('is_alcohol', false)->where('is_food', false)->active()->first();
                    }
                }
                
                // 3. Absolute fallback to ANY active counter
                if (!$counter) {
                    $counter = \App\Models\Counter::active()->first();
                }

                if ($counter) {
                    $counterId = $counter->id;
                }
            }
        }

        $total = $subtotal - $discount;

        // Check if member has sufficient balance (always required - no cash accepted)
        if ($member->balance < $total) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. Required: TZS ' . number_format((float)$total) . ', Available: TZS ' . number_format((float)$member->balance),
                'required' => $total,
                'balance' => $member->balance,
            ], 400);
        }

        // Deduct from member balance (always - no cash accepted)
        $balanceBefore = $member->balance;
        $member->decrement('balance', $total);
        $balanceAfter = $member->fresh()->balance;
        if ($balanceAfter !== $member->balance) {
             // Successful deduction
        }

        // Mark table as occupied ONLY after successful balance check/deduction
        if ($request->table_number) {
            $table = \App\Models\Table::where('table_number', $request->table_number)->first();
            if ($table && $table->status !== 'occupied') {
                $table->update(['status' => 'occupied']);
            }
        }

        if ($existingOrder) {
            // APPEND TO EXISTING ORDER
            foreach ($orderItems as $item) {
                $existingOrder->items()->create($item);
            }
            
            // Update existing order totals and counter
            $existingOrder->increment('subtotal', $subtotal);
            $existingOrder->increment('total_amount', $total);
            if ($counterId) {
                $existingOrder->update(['counter_id' => $counterId]);
            }
            
            // If it was 'ready' or 'served', set back to 'pending' for kitchen attention
            if (in_array($existingOrder->status, ['ready', 'served'])) {
                $existingOrder->update(['status' => 'pending']);
            }

            $order = $existingOrder;
            $msgPart = "added to Order #{$order->order_number}";
            $actionWord = "updated";
        } else {
            // CREATE NEW ORDER
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'member_id' => $member->id,
                'customer_name' => $member->name,
                'customer_phone' => $member->phone,
                'customer_upi' => $member->card_number,
                'table_number' => $request->table_number,
                'table_id' => $tableId,
                'is_vip' => $isVip,
                'counter_id' => $counterId,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $total,
                'payment_method' => 'balance', // Always use balance - no cash accepted
                'status' => 'saved', // Simplified: orders start as "saved"
                'sms_sent' => $request->send_sms ?? false,
                'notes' => $request->notes,
            ]);

            $msgPart = "created for {$member->name}: Order #{$order->order_number}";
            $actionWord = "created";
        }

        // Deduct stock for each item immediately
        foreach (($existingOrder ? $orderItems : $order->items) as $item) {
            $menuItem = \App\Models\MenuItem::find($item['menu_item_id'] ?? $item->menu_item_id);
            if ($menuItem) {
                $menuItem->decrement('stock_quantity', $item['quantity'] ?? $item->quantity);
            }
        }

        // Log activity
        ActivityLog::log('services', $actionWord, "Order {$msgPart}", 'Order', $order->id, [
            'order_number' => $order->order_number,
            'total_amount' => $total,
            'items_count' => count($orderItems),
            'balance_after' => $balanceAfter,
        ]);

        // Create transaction record
        Transaction::create([
            'transaction_id' => Transaction::generateTransactionId(),
            'member_id' => $member->id,
            'customer_name' => $member->name,
            'type' => 'payment',
            'category' => 'food_beverage',
            'amount' => $total,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method' => 'balance',
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'status' => 'completed',
        ]);

        // Send SMS notification for deduction (Skip if from waiter dashboard to speed up processing)
        $smsSent = false;
        if (($request->send_sms ?? true) && $member && ($request->source !== 'waiter') && ($request->source !== 'waiter_dashboard')) {
            $smsService = new \App\Services\SmsService();
            $smsResult = $smsService->sendPaymentNotification($member, $total, $balanceAfter, $order->items->first()->menuItem->name ?? 'F&B Order');
            $smsSent = $smsResult['success'] ?? false;
            $order->update(['sms_sent' => $smsSent]);
        }

        // Trigger Notifications for Kitchen / Counter
        try {
            $notifiableUsers = [];
            if ($hasFood) {
                // Find users who should be notified for Food (Kitchen)
                // Since there is no 'chef' role, we notify anyone with 'manager' or 'operations' role
                // or a specific designated kitchen user if one existed.
                $kitchenUsers = \App\Models\User::whereIn('role', ['admin', 'manager', 'operations'])->get();
                foreach($kitchenUsers as $u) $notifiableUsers[$u->id] = $u;
            }

            if ($counterId) {
                $targetCounter = \App\Models\Counter::find($counterId);
                if ($targetCounter && $targetCounter->assigned_user_id) {
                    $u = \App\Models\User::find($targetCounter->assigned_user_id);
                    if ($u) $notifiableUsers[$u->id] = $u;
                }
            }

            foreach ($notifiableUsers as $user) {
                $user->notify(new class($order) extends \Illuminate\Notifications\Notification {
                    private $order;
                    public function __construct($order) { $this->order = $order; }
                    public function via($notifiable) { return ['database']; }
                    public function toArray($notifiable) {
                        return [
                            'title' => 'New Order #' . $this->order->order_number,
                            'message' => 'Table ' . $this->order->table_number . ' ordered items.',
                            'order_id' => $this->order->id,
                            'type' => 'new_order'
                        ];
                    }
                });
            }
        } catch (\Exception $e) {
            // Silently fail notifications if something goes wrong to not break order flow
        }

        return response()->json([
            'success' => true,
            'message' => 'Order saved successfully. Balance deducted: TZS ' . number_format((float)$total) . '. New balance: TZS ' . number_format((float)$balanceAfter),
            'order' => $order->load('items'),
            'new_balance' => $balanceAfter,
            'sms_sent' => $smsSent
        ]);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Ensure all orders have member_id and payment_method = 'balance'
        if (!$order->member_id) {
            return response()->json([
                'success' => false,
                'message' => 'This order does not have a member associated. All orders must be paid with member balance.'
            ], 400);
        }
        
        // Allow status updates
        if (in_array($request->status, ['preparing', 'ready', 'served', 'complete'])) {
            $order->update(['status' => $request->status]);
            return response()->json(['success' => true, 'message' => 'Order status updated to ' . $request->status, 'order' => $order]);
        }
        
        // Allow cancel - refund balance and stock
        if ($request->status === 'cancelled') {
            if ($order->status !== 'cancelled' && $order->member_id) {
                // Refund balance
                $member = Member::find($order->member_id);
                if ($member) {
                    $balanceBefore = $member->balance;
                    $member->increment('balance', $order->total_amount);
                    $balanceAfter = $member->fresh()->balance;
                    
                    Transaction::create([
                        'transaction_id' => Transaction::generateTransactionId(),
                        'member_id' => $member->id,
                        'customer_name' => $member->name,
                        'type' => 'refund',
                        'category' => 'food_beverage',
                        'amount' => $order->total_amount,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'payment_method' => 'balance',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'status' => 'completed',
                        'notes' => 'Order cancellation refund',
                    ]);
                    
                    $smsService = new \App\Services\SmsService();
                    $smsService->sendRefundNotification($member, $order->total_amount, $balanceAfter, 'Order cancellation');
                }

                // Refund stock
                foreach ($order->items as $item) {
                    if ($item->menuItem) {
                        $item->menuItem->increment('stock_quantity', $item->quantity);
                    }
                }
            }
            $order->update(['status' => 'cancelled']);
            
            return response()->json(['success' => true, 'message' => 'Order cancelled, balance and stock refunded', 'order' => $order]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid status change'], 400);
    }

    public function storeMenuItem(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:menu_categories,id',
            'price' => 'required|numeric|min:0',
        ]);

        $category = MenuCategory::findOrFail($request->category_id);

        // Security check for counter role
        if ($user->role === 'counter') {
            $counter = \App\Models\Counter::where('assigned_user_id', $user->id)->first();
            if (!$counter || $category->is_alcohol != $counter->is_alcohol) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Unauthorized. You can only create items in categories matching your specialization (' . ($counter && $counter->is_alcohol ? 'Alcohol' : 'Non-Alcohol') . ').'
                ], 403);
            }
        }

        $item = MenuItem::create($request->all());

        // Log activity
        ActivityLog::log('services', 'created', "Menu item added: {$item->name} by {$user->name}", 'MenuItem', $item->id, [
            'name' => $item->name,
            'price' => $item->price,
            'category_id' => $item->category_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Menu item added', 'item' => $item]);
    }

    public function updateMenuItem(Request $request, $id)
    {
        $user = auth()->user();
        $item = MenuItem::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'is_available' => 'sometimes|required|boolean',
        ]);

        // Security check for counter role
        if ($user->role === 'counter') {
            $counter = \App\Models\Counter::where('assigned_user_id', $user->id)->first();
            if (!$counter || ($item->category && $item->category->is_alcohol != $counter->is_alcohol)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Unauthorized. You can only update items in your assigned category specialization.'
                ], 403);
            }
        }

        $oldPrice = $item->price;
        $item->update($request->all());

        // Log activity if price changed
        if ($request->has('price') && $oldPrice != $item->price) {
            ActivityLog::log('services', 'updated', "Menu item price updated: {$item->name} (TZS " . number_format($oldPrice) . " -> TZS " . number_format($item->price) . ") by {$user->name}", 'MenuItem', $item->id, [
                'name' => $item->name,
                'old_price' => $oldPrice,
                'new_price' => $item->price,
            ]);
        } else {
            ActivityLog::log('services', 'updated', "Menu item updated: {$item->name} by {$user->name}", 'MenuItem', $item->id);
        }

        return response()->json(['success' => true, 'message' => 'Menu item updated', 'item' => $item]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $status = $request->status ?? 'active';
        $category = MenuCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_alcohol' => $request->boolean('is_alcohol'),
            'is_food' => $request->boolean('is_food'),
            'status' => $status,
            'is_active' => $status === 'active',
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Log activity
        ActivityLog::log('services', 'created', "Menu category added: {$category->name}", 'MenuCategory', $category->id);

        return response()->json(['success' => true, 'message' => 'Category added', 'category' => $category]);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = MenuCategory::findOrFail($id);
        $updateData = $request->only(['name', 'description', 'status', 'is_active', 'sort_order']);
        
        // Handle boolean flags correctly from checkboxes
        if ($request->has('is_alcohol_toggle')) { // Use a specific flag if coming from a toggle/checkbox
            $updateData['is_alcohol'] = $request->boolean('is_alcohol');
        } elseif ($request->has('is_alcohol')) {
            $updateData['is_alcohol'] = $request->boolean('is_alcohol');
        }

        if ($request->has('is_food')) {
            $updateData['is_food'] = $request->boolean('is_food');
        }
        
        // Sync status with is_active if status is provided
        if ($request->has('status')) {
            $updateData['is_active'] = $request->status === 'active';
        } elseif ($request->has('is_active')) {
            $updateData['status'] = $request->is_active ? 'active' : 'inactive';
        }
        
        $category->update($updateData);

        // Log activity
        ActivityLog::log('services', 'updated', "Menu category updated: {$category->name}", 'MenuCategory', $category->id);

        return response()->json(['success' => true, 'message' => 'Category updated', 'category' => $category]);
    }

    public function deleteCategory($id)
    {
        $category = MenuCategory::findOrFail($id);
        
        // Set items in this category to null
        MenuItem::where('category_id', $id)->update(['category_id' => null]);
        
        $categoryName = $category->name;
        $category->delete();

        // Log activity
        ActivityLog::log('services', 'deleted', "Menu category deleted: {$categoryName}", 'MenuCategory', $id);

        return response()->json(['success' => true, 'message' => 'Category deleted']);
    }
}
