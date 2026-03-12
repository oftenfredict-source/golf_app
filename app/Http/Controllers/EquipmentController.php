<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentRental;
use App\Models\EquipmentSale;
use App\Models\EquipmentSaleItem;
use App\Models\Member;
use App\Models\RentalConfig;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{
    // Equipment Rental
    public function rentalIndex()
    {
        $equipment = Equipment::where('status', 'active')->where('is_rentable', true)->get();
        $activeRentals = EquipmentRental::with('equipment', 'member')->where('status', 'active')->orderBy('start_time', 'desc')->get();
        $rentalHistory = EquipmentRental::with('equipment', 'member')
            ->where('status', '!=', 'active')
            ->orderBy('start_time', 'desc')
            ->limit(100)
            ->get();
        $members = Member::where('status', 'active')->orderBy('name')->get();
        
        $stats = [
            'active_rentals' => EquipmentRental::where('status', 'active')->count(),
            'available_items' => Equipment::where('status', 'active')->sum('available_quantity'),
            'under_maintenance' => Equipment::where('status', 'active')->sum('maintenance_quantity'),
            'revenue_today' => EquipmentRental::whereDate('start_time', today())->where('status', 'returned')->sum('total_amount'),
        ];

        return view('golf-services.equipment-rental', compact('equipment', 'activeRentals', 'rentalHistory', 'members', 'stats'));
    }

    public function createRental(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'equipment_id' => 'required|exists:equipment,id',
            'quantity' => 'required|integer|min:1',
            'rental_type' => 'required|in:hourly,daily',
            'expected_return' => 'required|date',
        ]);

        $member = Member::findOrFail($request->member_id);
        $equipment = Equipment::findOrFail($request->equipment_id);
        
        if ($equipment->available_quantity < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Not enough equipment available'], 400);
        }

        $rate = $request->rental_type === 'hourly' ? $equipment->rental_hourly_rate : $equipment->rental_daily_rate;
        $rentalAmount = $rate * $request->quantity;

        // Check member balance
        if ($member->balance < $rentalAmount) {
            return response()->json(['success' => false, 'message' => 'Insufficient member balance. Required: TZS ' . number_format($rentalAmount)], 400);
        }

        // Deduct from member balance
        $balanceBefore = $member->balance;
        $member->decrement('balance', $rentalAmount);
        $balanceAfter = $member->fresh()->balance;

        $rental = EquipmentRental::create([
            'equipment_id' => $request->equipment_id,
            'member_id' => $request->member_id,
            'customer_name' => $member->name,
            'customer_phone' => $member->phone,
            'customer_upi' => $member->card_number,
            'quantity' => $request->quantity,
            'rental_type' => $request->rental_type,
            'start_time' => now(),
            'expected_return' => $request->expected_return,
            'deposit_paid' => $equipment->deposit_amount * $request->quantity,
            'rental_amount' => $rentalAmount,
            'total_amount' => $rentalAmount,
            'payment_method' => 'balance', // Changed from 'card' to 'balance'
            'notes' => $request->notes,
            'status' => 'active',
        ]);

        $equipment->decrement('available_quantity', $request->quantity);
        $equipment->increment('rented_quantity', $request->quantity);

        // Record transaction
        Transaction::create([
            'transaction_id' => Transaction::generateTransactionId(),
            'member_id' => $member->id,
            'customer_name' => $member->name,
            'type' => 'payment',
            'category' => 'equipment_rental',
            'amount' => $rentalAmount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method' => 'balance',
            'reference_type' => 'equipment_rental',
            'reference_id' => $rental->id,
            'notes' => 'Equipment Rental: ' . $equipment->name . ' (' . $request->quantity . 'x)',
            'status' => 'completed',
        ]);

        // Log activity
        ActivityLog::log('golf-services', 'created', "Equipment rental created for {$member->name}: {$equipment->name} ({$request->quantity}x)", 'EquipmentRental', $rental->id, [
            'equipment' => $equipment->name,
            'quantity' => $request->quantity,
            'rental_type' => $request->rental_type,
            'amount' => $rentalAmount,
            'balance_after' => $balanceAfter,
        ]);

        // Send SMS notification for deduction
        $smsSent = false;
        if ($request->send_sms ?? true) {
            $smsService = new \App\Services\SmsService();
            $smsResult = $smsService->sendPaymentNotification($member, $rentalAmount, $balanceAfter, 'Equipment Rental');
            $smsSent = $smsResult['success'] ?? false;
        }

        return response()->json([
            'success' => true, 
            'message' => 'Rental created. TZS ' . number_format($rentalAmount) . ' deducted from member card.', 
            'rental' => $rental,
            'new_balance' => $balanceAfter,
            'sms_sent' => $smsSent
        ]);
    }

    public function returnRental(Request $request, $id)
    {
        $rental = EquipmentRental::with('equipment', 'member')->findOrFail($id);
        
        if ($rental->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Rental is not active'], 400);
        }

        $config = RentalConfig::getConfig();
        $lateFee = 0;
        
        $returnTime = now();
        if (now()->gt($rental->expected_return)) {
            $expectedReturn = $rental->expected_return;
            $gracePeriod = $config->grace_period_minutes ?? 15;
            
            // Add grace period to expected return time
            $expectedReturnWithGrace = $expectedReturn->copy()->addMinutes($gracePeriod);
            
            // Only charge late fee if return is after grace period
            if ($returnTime->gt($expectedReturnWithGrace)) {
                $hoursLate = $returnTime->diffInHours($expectedReturnWithGrace);
                $lateFeePerHour = $config->late_fee_per_hour ?? 5000;
                $lateFee = $hoursLate * $lateFeePerHour;
            }
            
            // Charge late fee if applicable
            if ($lateFee > 0 && $rental->member) {
                $member = $rental->member;
                if ($member->balance >= $lateFee) {
                    $balanceBefore = $member->balance;
                    $member->decrement('balance', $lateFee);
                    $balanceAfter = $member->fresh()->balance;
                    
                    // Record late fee transaction
                    Transaction::create([
                        'transaction_id' => Transaction::generateTransactionId(),
                        'member_id' => $member->id,
                        'customer_name' => $member->name,
                        'type' => 'payment',
                        'category' => 'equipment_rental',
                        'amount' => $lateFee,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'payment_method' => 'balance',
                        'reference_type' => 'equipment_rental',
                        'reference_id' => $rental->id,
                        'notes' => 'Late Return Fee: ' . $rental->equipment->name,
                        'status' => 'completed',
                    ]);
                    
                    // Send SMS notification for late fee
                    $smsService = new \App\Services\SmsService();
                    $smsService->sendPaymentNotification($member, $lateFee, $balanceAfter, 'Equipment Rental Late Fee');
                    
                    // Log activity for late fee
                    ActivityLog::log('golf-services', 'updated', "Late fee charged for equipment return: TZS " . number_format($lateFee), 'EquipmentRental', $rental->id, [
                        'late_fee' => $lateFee,
                        'balance_after' => $balanceAfter,
                    ]);
                }
            }
        }

        $rental->update([
            'actual_return' => now(),
            'late_fee' => $lateFee,
            'total_amount' => $rental->rental_amount + $lateFee,
            'status' => 'returned',
        ]);

        $rental->equipment->increment('available_quantity', $rental->quantity);
        $rental->equipment->decrement('rented_quantity', $rental->quantity);

        // Log activity for return
        ActivityLog::log('golf-services', 'updated', "Equipment returned: {$rental->equipment->name}", 'EquipmentRental', $rental->id, [
            'late_fee' => $lateFee,
        ]);

        return response()->json([
            'success' => true, 
            'message' => $lateFee > 0 ? 'Equipment returned. Late fee of TZS ' . number_format($lateFee) . ' charged.' : 'Equipment returned successfully',
            'rental' => $rental
        ]);
    }

    // Equipment Sales
    public function salesIndex()
    {
        $equipment = Equipment::where('status', 'active')->where('is_sellable', true)->get();
        $todaySales = EquipmentSale::with('items.equipment', 'member')->whereDate('created_at', today())->orderBy('created_at', 'desc')->get();
        $members = Member::where('status', 'active')->orderBy('name')->get();
        
        $stats = [
            'sales_today' => EquipmentSale::whereDate('created_at', today())->where('status', 'completed')->count(),
            'revenue_today' => EquipmentSale::whereDate('created_at', today())->where('status', 'completed')->sum('total_amount'),
            'items_in_stock' => Equipment::where('status', 'active')->where('is_sellable', true)->sum('available_quantity'),
            'low_stock' => Equipment::where('status', 'active')->where('is_sellable', true)->whereColumn('available_quantity', '<=', 'low_stock_threshold')->count(),
        ];

        return view('golf-services.equipment-sales', compact('equipment', 'todaySales', 'members', 'stats'));
    }

    public function createSale(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'items' => 'required|array|min:1',
            'items.*.equipment_id' => 'required|exists:equipment,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $member = Member::findOrFail($request->member_id);
        $subtotal = 0;
        $saleItems = [];

        foreach ($request->items as $item) {
            $equipment = Equipment::findOrFail($item['equipment_id']);
            
            if ($equipment->available_quantity < $item['quantity']) {
                return response()->json([
                    'success' => false, 
                    'message' => "Not enough {$equipment->name} in stock"
                ], 400);
            }

            $itemSubtotal = $equipment->sale_price * $item['quantity'];
            $subtotal += $itemSubtotal;
            
            $saleItems[] = [
                'equipment_id' => $item['equipment_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $equipment->sale_price,
                'subtotal' => $itemSubtotal,
            ];
        }

        $discount = $request->discount ?? 0;
        $total = $subtotal - $discount;

        // Check member balance
        if ($member->balance < $total) {
            return response()->json([
                'success' => false, 
                'message' => 'Insufficient member balance. Required: TZS ' . number_format($total) . ', Available: TZS ' . number_format($member->balance)
            ], 400);
        }

        // Deduct from member balance
        $balanceBefore = $member->balance;
        $member->decrement('balance', $total);
        $balanceAfter = $member->fresh()->balance;

        $sale = EquipmentSale::create([
            'member_id' => $request->member_id,
            'customer_name' => $member->name,
            'customer_phone' => $member->phone,
            'customer_upi' => $member->card_number,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total_amount' => $total,
            'payment_method' => 'balance', // Changed from 'card' to 'balance'
            'sms_sent' => $request->send_sms ?? false,
            'notes' => $request->notes,
            'status' => 'completed',
        ]);

        foreach ($saleItems as $item) {
            $sale->items()->create($item);
            Equipment::find($item['equipment_id'])->decrement('available_quantity', $item['quantity']);
        }

        // Record transaction
        Transaction::create([
            'transaction_id' => Transaction::generateTransactionId(),
            'member_id' => $member->id,
            'customer_name' => $member->name,
            'type' => 'payment',
            'category' => 'equipment_sale',
            'amount' => $total,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method' => 'balance',
            'reference_type' => 'equipment_sale',
            'reference_id' => $sale->id,
            'notes' => 'Equipment Sale: ' . count($saleItems) . ' item(s)',
            'status' => 'completed',
        ]);

        // Log activity
        ActivityLog::log('golf-services', 'created', "Equipment sale for {$member->name}: " . count($saleItems) . " item(s)", 'EquipmentSale', $sale->id, [
            'items_count' => count($saleItems),
            'total_amount' => $total,
            'balance_after' => $balanceAfter,
        ]);

        // Send SMS notification for deduction
        $smsSent = false;
        if ($request->send_sms ?? true) {
            $smsService = new \App\Services\SmsService();
            $smsResult = $smsService->sendPaymentNotification($member, $total, $balanceAfter, 'Equipment Purchase');
            $smsSent = $smsResult['success'] ?? false;
            $sale->update(['sms_sent' => $smsSent]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Sale completed. TZS ' . number_format($total) . ' deducted. New balance: TZS ' . number_format($balanceAfter), 
            'sale' => $sale->load('items'),
            'total' => $total,
            'new_balance' => $balanceAfter,
            'sms_sent' => $smsSent
        ]);
    }

    public function showSale($id)
    {
        $sale = EquipmentSale::with('items.equipment', 'member')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'sale' => $sale
        ]);
    }

    public function showReceipt($id)
    {
        $sale = EquipmentSale::with('items.equipment', 'member')->findOrFail($id);
        return view('golf-services.equipment-sale-receipt', compact('sale'));
    }

    public function storeEquipment(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'category' => 'required|string',
            'total_quantity' => 'required|integer|min:0',
        ];

        // Only validate SKU uniqueness if it's provided manually
        if ($request->filled('sku')) {
            $rules['sku'] = 'required|string|unique:equipment,sku';
        }

        $request->validate($rules);

        // Generate SKU if empty
        $sku = $request->sku;
        if (empty($sku)) {
            $prefix = strtoupper(substr($request->category, 0, 3));
            $namePart = strtoupper(substr(str_replace(' ', '', $request->name), 0, 3));
            $sku = $prefix . '-' . $namePart . '-' . strtoupper(Str::random(4));
            
            // Final check to ensure uniqueness
            while (Equipment::where('sku', $sku)->exists()) {
                $sku = $prefix . '-' . $namePart . '-' . strtoupper(Str::random(4));
            }
        }

        $equipment = Equipment::create([
            'name' => $request->name,
            'sku' => $sku,
            'category' => $request->category,
            'description' => $request->description,
            'rental_hourly_rate' => $request->rental_hourly_rate ?? 0,
            'rental_daily_rate' => $request->rental_daily_rate ?? 0,
            'sale_price' => $request->sale_price ?? 0,
            'deposit_amount' => $request->deposit_amount ?? 0,
            'total_quantity' => $request->total_quantity ?? 0,
            'available_quantity' => $request->total_quantity ?? 0,
            'low_stock_threshold' => $request->low_stock_threshold ?? 5,
            'is_rentable' => $request->has('is_rentable') ? (bool)$request->is_rentable : true,
            'is_sellable' => $request->has('is_sellable') ? (bool)$request->is_sellable : false,
            'status' => 'active',
        ]);

        // Log activity
        ActivityLog::log('golf-services', 'created', "Equipment added: {$equipment->name}", 'Equipment', $equipment->id, [
            'sku' => $equipment->sku,
            'category' => $equipment->category,
            'quantity' => $equipment->total_quantity,
        ]);

        return response()->json(['success' => true, 'message' => 'Equipment added successfully', 'equipment' => $equipment]);
    }

    public function updateEquipment(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        
        $rules = [];
        if ($request->has('name') && $request->name !== 'price_update_bypass') $rules['name'] = 'required|string';
        if ($request->has('sku') && $request->sku !== 'price_update_bypass') $rules['sku'] = 'required|string|unique:equipment,sku,' . $id;
        if ($request->has('category') && $request->category !== 'price_update_bypass') $rules['category'] = 'required|string';
        
        if (!empty($rules)) {
            $request->validate($rules);
        }

        $updateData = [];
        if ($request->has('name') && $request->name !== 'price_update_bypass') $updateData['name'] = $request->name;
        if ($request->has('sku') && $request->sku !== 'price_update_bypass') $updateData['sku'] = $request->sku;
        if ($request->has('category') && $request->category !== 'price_update_bypass') $updateData['category'] = $request->category;
        
        if ($request->has('description')) $updateData['description'] = $request->description;
        if ($request->has('rental_hourly_rate')) $updateData['rental_hourly_rate'] = $request->rental_hourly_rate;
        if ($request->has('rental_daily_rate')) $updateData['rental_daily_rate'] = $request->rental_daily_rate;
        if ($request->has('sale_price')) $updateData['sale_price'] = $request->sale_price;
        if ($request->has('deposit_amount')) $updateData['deposit_amount'] = $request->deposit_amount;
        if ($request->has('low_stock_threshold')) $updateData['low_stock_threshold'] = $request->low_stock_threshold;
        if ($request->has('status')) $updateData['status'] = $request->status;
        
        if ($request->has('is_rentable')) $updateData['is_rentable'] = (bool)$request->is_rentable;
        if ($request->has('is_sellable')) $updateData['is_sellable'] = (bool)$request->is_sellable;

        $equipment->update($updateData);

        // Update quantities if provided
        if ($request->has('total_quantity')) {
            $diff = $request->total_quantity - $equipment->total_quantity;
            $equipment->increment('total_quantity', $diff);
            $equipment->increment('available_quantity', $diff);
        }

        // Log activity
        ActivityLog::log('golf-services', 'updated', "Equipment updated: {$equipment->name}", 'Equipment', $equipment->id);

        return response()->json(['success' => true, 'message' => 'Equipment updated successfully', 'equipment' => $equipment]);
    }

    public function deleteEquipment($id)
    {
        $equipment = Equipment::findOrFail($id);
        
        // Check if equipment has active rentals
        $activeRentals = EquipmentRental::where('equipment_id', $id)->where('status', 'active')->count();
        if ($activeRentals > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot delete equipment with active rentals'
            ], 400);
        }

        $equipment->update(['status' => 'inactive']);
        
        // Log activity
        ActivityLog::log('golf-services', 'deleted', "Equipment deactivated: {$equipment->name}", 'Equipment', $equipment->id);
        
        return response()->json(['success' => true, 'message' => 'Equipment deactivated successfully']);
    }

    public function getEquipment($id)
    {
        $equipment = Equipment::findOrFail($id);
        return response()->json(['success' => true, 'equipment' => $equipment]);
    }

    // Rental Configuration
    public function rentalConfig()
    {
        $config = RentalConfig::getConfig();
        return view('golf-services.rental-configuration', compact('config'));
    }

    public function updateRentalConfig(Request $request)
    {
        $config = RentalConfig::getConfig();
        
        $config->update($request->only([
            'security_deposit',
            'max_rental_hours',
            'late_fee_per_hour',
            'require_deposit',
            'allow_extensions',
            'auto_charge_late',
            'extension_fee_per_hour',
            'damage_fee_percentage',
            'grace_period_minutes',
        ]));

        // Log activity
        ActivityLog::log('golf-services', 'updated', "Rental configuration updated", 'RentalConfig', $config->id);

        return response()->json([
            'success' => true,
            'message' => 'Rental configuration updated successfully',
            'config' => $config,
        ]);
    }
}

