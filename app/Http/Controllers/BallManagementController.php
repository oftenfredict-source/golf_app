<?php

namespace App\Http\Controllers;

use App\Models\BallInventory;
use App\Models\BallTransaction;
use App\Services\SmsService;
use Illuminate\Http\Request;

class BallManagementController extends Controller
{
    public function index()
    {
        $inventory = BallInventory::first() ?? BallInventory::create([
            'ball_type' => 'standard',
            'total_quantity' => 5000,
            'available_quantity' => 5000,
            'in_use' => 0,
            'damaged' => 0,
            'cost_per_ball' => 500
        ]);
        
        $todayTransactions = BallTransaction::today()->orderBy('created_at', 'asc')->get();
        
        // Group transactions into "sessions" based on issuance cycles
        $sessionsData = [];
        $activeSessions = []; // Track the latest session index for each customer
        
        foreach ($todayTransactions as $txn) {
            $customerKey = $txn->customer_name ?: 'System';
            
            // Start a new session on issued/purchased
            if (in_array($txn->type, ['issued', 'purchased'])) {
                $session = (object)[
                    'customer_name' => $customerKey,
                    'issued' => $txn->quantity,
                    'returned' => 0,
                    'remaining' => $txn->quantity,
                    'time_issued' => $txn->created_at->format('H:i'),
                    'time_returned' => '-',
                    'amount' => $txn->amount ?: 0,
                    'last_txn_id' => $txn->id,
                    'type' => $txn->type,
                    'member_id' => $txn->member_id,
                    'payment_method' => $txn->payment_method
                ];
                $sessionsData[] = $session;
                $activeSessions[$customerKey] = count($sessionsData) - 1;
            } 
            // Append to existing session on returned/damaged
            else if (in_array($txn->type, ['returned', 'damaged'])) {
                if (isset($activeSessions[$customerKey])) {
                    $idx = $activeSessions[$customerKey];
                    $sessionsData[$idx]->returned += $txn->quantity;
                    $sessionsData[$idx]->remaining = max(0, $sessionsData[$idx]->issued - $sessionsData[$idx]->returned);
                    $sessionsData[$idx]->time_returned = $txn->created_at->format('H:i');
                    $sessionsData[$idx]->last_txn_id = $txn->id;
                } else {
                    // Return without an issuance record today
                    $session = (object)[
                        'customer_name' => $customerKey,
                        'issued' => 0,
                        'returned' => $txn->quantity,
                        'remaining' => 0,
                        'time_issued' => '-',
                        'time_returned' => $txn->created_at->format('H:i'),
                        'amount' => 0,
                        'last_txn_id' => $txn->id,
                        'type' => $txn->type,
                        'member_id' => $txn->member_id,
                        'payment_method' => null
                    ];
                    $sessionsData[] = $session;
                    // We don't track this as an "active" session because it's already fulfilled/legacy
                }
            }
        }
        
        // Reverse for display (most recent issuance cycle at top)
        $ballSessions = array_reverse($sessionsData);

        $members = \App\Models\Member::active()->orderBy('name')->get(); 
        
        $stats = [
            'total' => $inventory->total_quantity,
            'available' => $inventory->available_quantity,
            'in_use' => $inventory->in_use,
            'damaged' => $inventory->damaged,
            'issued_today' => BallTransaction::today()->where('type', 'issued')->sum('quantity'),
            'returned_today' => BallTransaction::today()->where('type', 'returned')->sum('quantity'),
        ];

        return view('golf-services.ball-management', compact('inventory', 'todayTransactions', 'stats', 'members', 'ballSessions'));
    }

    public function issue(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string',
            'payment_method' => 'required|in:cash,card,mobile_money,balance',
        ]);

        $inventory = BallInventory::first();
        
        if ($inventory->available_quantity < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Not enough balls available'], 400);
        }

        // Calculate amount based on quantity and cost per ball
        $amount = $request->quantity * $inventory->cost_per_ball;

        // Handle member balance payment if applicable
        if ($request->payment_method === 'balance' && $request->member_id) {
            $member = \App\Models\Member::find($request->member_id);
            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member not found'], 400);
            }
            
            if ($member->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance. Required: TZS ' . number_format($amount) . ', Available: TZS ' . number_format($member->balance)
                ], 400);
            }
            
            $balanceBefore = $member->balance;
            $member->decrement('balance', $amount);
            $balanceAfter = $member->fresh()->balance;
            
            // Create transaction record
            \App\Models\Transaction::create([
                'transaction_id' => \App\Models\Transaction::generateTransactionId(),
                'member_id' => $member->id,
                'customer_name' => $member->name,
                'type' => 'payment',
                'category' => 'ball_management',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'payment_method' => 'balance',
                'reference_type' => 'ball_transaction',
                'status' => 'completed',
            ]);
        }

        $inventory->decrement('available_quantity', $request->quantity);
        $inventory->increment('in_use', $request->quantity);

        $transaction = BallTransaction::create([
            'type' => 'issued',
            'quantity' => $request->quantity,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'member_id' => $request->member_id,
            'session_id' => $request->session_id,
            'notes' => $request->notes,
            'amount' => $amount,
            'payment_method' => $request->payment_method,
        ]);
        
        $smsSent = false;
        if ($request->payment_method === 'balance' && isset($balanceAfter)) {
            \App\Models\Transaction::where('reference_type', 'ball_transaction')
                ->whereNull('reference_id')
                ->latest()
                ->first()
                ->update(['reference_id' => $transaction->id]);
            
            // Send SMS notification for deduction if member
            if ($request->member_id) {
                $member = \App\Models\Member::find($request->member_id);
                if ($member) {
                    $smsService = new SmsService();
                    $smsResult = $smsService->sendPaymentNotification($member, $amount, $balanceAfter, 'Ball Purchase');
                    $smsSent = $smsResult['success'] ?? false;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Balls issued successfully. Amount: TZS ' . number_format($amount) . '. New balance: TZS ' . number_format($balanceAfter),
                'sms_sent' => $smsSent
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Balls issued successfully. Amount: TZS ' . number_format($amount)
        ]);
    }

    public function return(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory = BallInventory::first();
        
        $inventory->increment('available_quantity', $request->quantity);
        $inventory->decrement('in_use', $request->quantity);

        BallTransaction::create([
            'type' => 'returned',
            'quantity' => $request->quantity,
            'customer_name' => $request->customer_name,
            'notes' => $request->notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Balls returned successfully']);
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory = BallInventory::first();
        $inventory->increment('total_quantity', $request->quantity);
        $inventory->increment('available_quantity', $request->quantity);

        BallTransaction::create([
            'type' => 'purchased',
            'quantity' => $request->quantity,
            'notes' => $request->notes ?? 'New stock added',
        ]);

        return response()->json(['success' => true, 'message' => 'Stock added successfully']);
    }

    public function markDamaged(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory = BallInventory::first();
        $inventory->decrement('available_quantity', $request->quantity);
        $inventory->increment('damaged', $request->quantity);

        BallTransaction::create([
            'type' => 'damaged',
            'quantity' => $request->quantity,
            'notes' => $request->notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Balls marked as damaged']);
    }
    
    public function updateTransaction(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:ball_transactions,id',
            'new_quantity' => 'required|integer|min:1',
        ]);
        
        $transaction = BallTransaction::findOrFail($request->transaction_id);
        $inventory = BallInventory::first();
        $oldQuantity = $transaction->quantity;
        $newQuantity = $request->new_quantity;
        $quantityDiff = $newQuantity - $oldQuantity;
        $costPerBall = $inventory->cost_per_ball;
        
        // Only allow editing issued and returned transactions
        if (!in_array($transaction->type, ['issued', 'returned'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only issued and returned transactions can be edited'
            ], 400);
        }
        
        // Calculate new amount
        $newAmount = $newQuantity * $costPerBall;
        $oldAmount = $transaction->amount ?? ($oldQuantity * $costPerBall);
        $amountDiff = $newAmount - $oldAmount;
        
        if ($transaction->type === 'issued') {
            // For issued transactions
            if ($quantityDiff > 0) {
                // Increasing quantity - need more balls available
                if ($inventory->available_quantity < $quantityDiff) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough balls available. Available: ' . $inventory->available_quantity . ', Needed: ' . $quantityDiff
                    ], 400);
                }
                $inventory->decrement('available_quantity', $quantityDiff);
                $inventory->increment('in_use', $quantityDiff);
            } elseif ($quantityDiff < 0) {
                // Decreasing quantity - return balls to inventory
                $inventory->increment('available_quantity', abs($quantityDiff));
                $inventory->decrement('in_use', abs($quantityDiff));
            }
            
            // Handle member balance adjustment if payment method was balance
            if ($transaction->payment_method === 'balance' && $transaction->member_id && $amountDiff != 0) {
                $member = \App\Models\Member::find($transaction->member_id);
                if ($member) {
                    if ($amountDiff > 0) {
                        // Need to deduct more
                        if ($member->balance < $amountDiff) {
                            // Rollback inventory changes
                            if ($quantityDiff > 0) {
                                $inventory->increment('available_quantity', $quantityDiff);
                                $inventory->decrement('in_use', $quantityDiff);
                            } else {
                                $inventory->decrement('available_quantity', abs($quantityDiff));
                                $inventory->increment('in_use', abs($quantityDiff));
                            }
                            return response()->json([
                                'success' => false,
                                'message' => 'Member has insufficient balance. Required: TZS ' . number_format($amountDiff) . ', Available: TZS ' . number_format($member->balance)
                            ], 400);
                        }
                        $balanceBefore = $member->balance;
                        $member->decrement('balance', $amountDiff);
                        $balanceAfter = $member->fresh()->balance;
                    } else {
                        // Refund the difference
                        $balanceBefore = $member->balance;
                        $member->increment('balance', abs($amountDiff));
                        $balanceAfter = $member->fresh()->balance;
                        
                        // Send SMS notification for refund
                        $smsService = new SmsService();
                        $smsService->sendRefundNotification($member, abs($amountDiff), $balanceAfter, 'Ball transaction adjustment');
                    }
                    
                    // Send SMS notification for additional charge if amount increased
                    if ($amountDiff > 0) {
                        $smsService = new SmsService();
                        $smsService->sendPaymentNotification($member, $amountDiff, $balanceAfter, 'Ball transaction adjustment');
                    }
                    
                    // Update or create transaction record
                    $existingTransaction = \App\Models\Transaction::where('reference_type', 'ball_transaction')
                        ->where('reference_id', $transaction->id)
                        ->first();
                    
                    if ($existingTransaction) {
                        $existingTransaction->update([
                            'amount' => $newAmount,
                            'balance_before' => $existingTransaction->balance_before,
                            'balance_after' => $balanceAfter,
                        ]);
                    } else {
                        \App\Models\Transaction::create([
                            'transaction_id' => \App\Models\Transaction::generateTransactionId(),
                            'member_id' => $member->id,
                            'customer_name' => $member->name,
                            'type' => $amountDiff > 0 ? 'payment' : 'refund',
                            'category' => 'ball_management',
                            'amount' => abs($amountDiff),
                            'balance_before' => $balanceBefore,
                            'balance_after' => $balanceAfter,
                            'payment_method' => 'balance',
                            'reference_type' => 'ball_transaction',
                            'reference_id' => $transaction->id,
                            'status' => 'completed',
                            'notes' => 'Quantity adjustment for ball transaction #' . $transaction->id,
                        ]);
                    }
                }
            }
        } elseif ($transaction->type === 'returned') {
            // For returned transactions
            if ($quantityDiff > 0) {
                // Increasing return quantity - need more in_use
                if ($inventory->in_use < $quantityDiff) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough balls in use. In use: ' . $inventory->in_use . ', Needed: ' . $quantityDiff
                    ], 400);
                }
                $inventory->increment('available_quantity', $quantityDiff);
                $inventory->decrement('in_use', $quantityDiff);
            } elseif ($quantityDiff < 0) {
                // Decreasing return quantity - move back to in_use
                $inventory->decrement('available_quantity', abs($quantityDiff));
                $inventory->increment('in_use', abs($quantityDiff));
            }
        }
        
        // Update the transaction
        $transaction->update([
            'quantity' => $newQuantity,
            'amount' => $newAmount,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully. New quantity: ' . $newQuantity . ' balls. New amount: TZS ' . number_format($newAmount)
        ]);
    }
}


