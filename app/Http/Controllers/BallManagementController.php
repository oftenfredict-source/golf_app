<?php

namespace App\Http\Controllers;

use App\Models\BallInventory;
use App\Models\BallTransaction;
use App\Models\BallCollector;
use App\Models\BallCollectionLog;
use App\Models\Member;
use App\Models\Transaction;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BallManagementController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : Carbon::today();
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : Carbon::today();

        $inventory = BallInventory::first() ?? BallInventory::create([
            'ball_type' => 'standard',
            'total_quantity' => 5000,
            'available_quantity' => 5000,
            'in_use' => 0,
            'damaged' => 0,
            'cost_per_ball' => 500
        ]);
        
        $transactions = BallTransaction::inDateRange($fromDate, $toDate)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Group transactions into "sessions" based on issuance cycles
        $sessionsData = [];
        $activeSessions = []; // Track the latest session index for each customer
        
        foreach ($transactions as $txn) {
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
                    'payment_method' => $txn->payment_method,
                    'designated_collector' => $txn->collector ? $txn->collector->name : ($txn->notes ? (str_contains($txn->notes, 'Collector:') ? trim(explode(':', $txn->notes)[1]) : null) : null)
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
                    if ($txn->collector_id) {
                        $sessionsData[$idx]->returned_by = $txn->collector->name;
                    }
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
                        'payment_method' => null,
                        'returned_by' => $txn->collector ? $txn->collector->name : null
                    ];
                    $sessionsData[] = $session;
                }
            }
        }
        
        // Reverse for display (most recent issuance cycle at top)
        $ballSessions = array_reverse($sessionsData);

        $members = Member::active()->orderBy('name')->get(); 
        $collectors = BallCollector::where('status', 'active')->orderBy('name')->get();
        
        $pendingCollections = BallCollectionLog::with('collector')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $stats = [
            'total' => $inventory->total_quantity,
            'available' => $inventory->available_quantity,
            'in_use' => $inventory->in_use,
            'damaged' => $inventory->damaged,
            'issued_today' => $transactions->where('type', 'issued')->sum('quantity'),
            'returned_today' => $transactions->where('type', 'returned')->sum('quantity'),
        ];

        return view('golf-services.ball-management', compact(
            'inventory', 
            'transactions', 
            'stats', 
            'members', 
            'collectors',
            'ballSessions',
            'pendingCollections',
            'fromDate',
            'toDate'
        ));
    }

    public function issue(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string',
            'payment_method' => 'required|in:cash,card,mobile_money,balance',
            'collector_id' => 'required|exists:ball_collectors,id',
        ]);

        $inventory = BallInventory::first();
        
        if ($inventory->available_quantity < $request->quantity) {
            return response()->json(['success' => false, 'message' => 'Not enough balls available'], 400);
        }

        // Calculate amount based on quantity and cost per ball
        $amount = $request->quantity * $inventory->cost_per_ball;

        // User Rule: Cardholders (has_full_access=1) MUST pay by balance.
        // Custom/Walk-ins can pay by cash.
        if ($request->member_id) {
            $member = Member::find($request->member_id);
            if ($member && $member->requiresBalancePayment() && $request->payment_method !== 'balance') {
                return response()->json([
                    'success' => false,
                    'message' => 'Members with issued cards must pay using their card balance. Please select "Balance" as the payment method.'
                ], 400);
            }
        }

        $member = null;
        $balanceBefore = 0;
        $balanceAfter = 0;

        if ($request->payment_method === 'balance' && $request->member_id) {
            $member = Member::find($request->member_id);
            if (!$member) {
                return response()->json(['success' => false, 'message' => 'Member not found'], 400);
            }
            
            // Strictly enforce balance check using central method
            $balanceBefore = $member->balance;
            if (!$member->safeDeduct($amount)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance. Required: TZS ' . number_format($amount) . ', Available: TZS ' . number_format($member->balance)
                ], 400);
            }
            $balanceAfter = $member->balance;
        }

        $inventory->decrement('available_quantity', $request->quantity);
        $inventory->increment('in_use', $request->quantity);

        // Create ball transaction record
        $memberId = $request->member_id ?: null;

        $transaction = BallTransaction::create([
            'type' => 'issued',
            'quantity' => $request->quantity,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'member_id' => $memberId,
            'session_id' => $request->session_id,
            'notes' => $request->notes,
            'amount' => $amount,
            'payment_method' => $request->payment_method,
        ]);

        // Create Ball Collection Log automatically
        BallCollectionLog::create([
            'collector_id' => $request->collector_id,
            'ball_transaction_id' => $transaction->id,
            'target_quantity' => $request->quantity,
            'quantity_collected' => 0,
            'status' => 'pending',
            'assigned_by' => Auth::id(),
            'collected_at' => null, // Will be set during actual field return
        ]);

        // Create general ledger transaction record for all payment methods
        Transaction::create([
            'transaction_id' => Transaction::generateTransactionId(),
            'member_id' => $memberId,
            'customer_name' => $request->customer_name,
            'type' => 'payment',
            'category' => 'ball_management',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method' => $request->payment_method,
            'reference_type' => 'ball_transaction',
            'reference_id' => $transaction->id,
            'status' => 'completed',
        ]);
        
        $smsSent = false;
        if ($request->payment_method === 'balance' && $member) {
            
            // Send SMS notification for deduction if member
            if ($request->member_id) {
                $member = Member::find($request->member_id);
                if ($member) {
                    $smsService = new SmsService();
                    $smsResult = $smsService->sendPaymentNotification($member, $amount, $balanceAfter, 'Ball Purchase');
                    $smsSent = $smsResult['success'] ?? false;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Balls issued successfully. Amount: TZS ' . number_format((float)$amount) . '. New balance: TZS ' . number_format((float)$balanceAfter),
                'sms_sent' => $smsSent
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Balls issued successfully. Amount: TZS ' . number_format((float)$amount)
        ]);
    }

    public function return(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'collector_id' => 'nullable|exists:ball_collectors,id',
        ]);

        $inventory = BallInventory::first();
        
        $inventory->increment('available_quantity', $request->quantity);
        $inventory->decrement('in_use', min($inventory->in_use, $request->quantity));

        // Find the member if possible for better tracking
        $member = Member::where('name', $request->customer_name)->first();

        BallTransaction::create([
            'type' => 'returned',
            'quantity' => $request->quantity,
            'customer_name' => $request->customer_name,
            'member_id' => $member ? $member->id : null,
            'collector_id' => $request->collector_id,
            'notes' => $request->notes,
        ]);

        // Find and close any pending collection logs for this customer/collector
        $query = BallCollectionLog::where('status', 'pending');
        
        // Match by customer name through the original transaction
        $query->whereHas('ballTransaction', function($q) use ($request) {
            $q->where('customer_name', $request->customer_name);
        });
        
        // If a collector was specified in the return, prioritize matching that collector
        if ($request->collector_id) {
            $query->where('collector_id', $request->collector_id);
        }
        
        $pendingLogs = $query->get();
        
        foreach ($pendingLogs as $log) {
            $log->update([
                'status' => 'verified',
                'quantity_collected' => $request->quantity,
                'verified_at' => now(),
            ]);
        }

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
                $inventory->decrement('in_use', min($inventory->in_use, $quantityDiff));
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


