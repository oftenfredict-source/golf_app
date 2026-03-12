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
            ->whereIn('status', ['saved', 'pending'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'open_orders' => Order::whereDate('created_at', today())->whereIn('status', ['saved', 'pending'])->count(),
            'in_prep' => 0, // Removed
            'ready' => 0, // Removed
            'revenue_today' => Order::whereDate('created_at', today())->whereIn('status', ['complete', 'completed'])->sum('total_amount'),
        ];

        return view('services.food-beverage', compact('categories', 'menuItems', 'activeOrders', 'stats'));
    }

    public function orders()
    {
        // Only show orders that have member_id (paid with member balance) - all expenses must be deducted from member balances
        $activeOrders = Order::with(['items.menuItem', 'member'])
            ->whereNotNull('member_id')
            ->where('payment_method', 'balance')
            ->active()
            ->orderBy('created_at', 'desc')
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
        
        // Handle VIP Table Pricing & Counter Management
        $isVip = false;
        $counterId = null;
        $tableId = null;
        
        if ($request->table_number) {
            $table = \App\Models\Table::where('table_number', $request->table_number)->first();
            if ($table) {
                $tableId = $table->id;
                if ($table->type === 'vip') {
                    $isVip = true;
                    // Apply VIP surcharge (10%) ONLY for Standard members. 
                    // VIP/Premier members are exempt as a tiered benefit.
                    if ($member->membership_type === 'standard') {
                        $surcharge = $subtotal * 0.10;
                        $subtotal += $surcharge;
                    }
                }
                
                // Assign to appropriate counter based on tier/type
                $tier = $isVip ? 'vip' : 'normal';
                $counter = \App\Models\Counter::where('tier', $tier)->active()->first();
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
                'message' => 'Insufficient balance. Required: TZS ' . number_format($total) . ', Available: TZS ' . number_format($member->balance),
                'required' => $total,
                'balance' => $member->balance,
            ], 400);
        }

        // Deduct from member balance (always - no cash accepted)
        $balanceBefore = $member->balance;
        $member->decrement('balance', $total);
        $balanceAfter = $member->fresh()->balance;

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

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        // Log activity
        ActivityLog::log('services', 'created', "Order created for {$member->name}: Order #{$order->order_number}", 'Order', $order->id, [
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

        // Send SMS notification for deduction
        $smsSent = false;
        if ($request->send_sms ?? true) {
            $smsService = new \App\Services\SmsService();
            $smsResult = $smsService->sendPaymentNotification($member, $total, $balanceAfter, 'Food & Beverage');
            $smsSent = $smsResult['success'] ?? false;
            $order->update(['sms_sent' => $smsSent]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order saved successfully. Balance deducted: TZS ' . number_format($total) . '. New balance: TZS ' . number_format($balanceAfter),
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
        
        if ($order->payment_method !== 'balance') {
            return response()->json([
                'success' => false,
                'message' => 'This order was not paid with member balance. All orders must be paid with member balance.'
            ], 400);
        }
        
        // Simplified: Only allow changing to 'complete' from 'saved' or 'pending'
        if ($request->status === 'complete' && in_array($order->status, ['saved', 'pending'])) {
            $order->update(['status' => 'complete']);
            
            // Log activity
            ActivityLog::log('services', 'completed', "Order completed: Order #{$order->order_number}", 'Order', $order->id, [
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Order marked as complete', 'order' => $order]);
        }
        
        // Allow cancel - refund balance if order was saved or pending
        if ($request->status === 'cancelled') {
            // Only refund if order was not already completed (already paid)
            if (in_array($order->status, ['saved', 'pending']) && $order->member_id) {
                $member = Member::find($order->member_id);
                if ($member) {
                    $balanceBefore = $member->balance;
                    $member->increment('balance', $order->total_amount);
                    $balanceAfter = $member->fresh()->balance;
                    
                    // Record refund transaction
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
                    
                    // Send SMS notification for refund
                    $smsService = new \App\Services\SmsService();
                    $smsService->sendRefundNotification($member, $order->total_amount, $balanceAfter, 'Order cancellation');
                }
            }
            $order->update(['status' => 'cancelled']);
            
            // Log activity
            ActivityLog::log('services', 'cancelled', "Order cancelled: Order #{$order->order_number} (Balance refunded)", 'Order', $order->id, [
                'order_number' => $order->order_number,
                'refund_amount' => $order->total_amount,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Order cancelled and balance refunded', 'order' => $order]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid status change'], 400);
    }

    public function storeMenuItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:menu_categories,id',
            'price' => 'required|numeric|min:0',
        ]);

        $item = MenuItem::create($request->all());

        // Log activity
        ActivityLog::log('services', 'created', "Menu item added: {$item->name}", 'MenuItem', $item->id, [
            'name' => $item->name,
            'price' => $item->price,
            'category_id' => $item->category_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Menu item added', 'item' => $item]);
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
