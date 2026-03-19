<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Member;
use App\Services\SmsService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Transaction::with(['member', 'order'])->orderBy('created_at', 'desc');

        // Counter specific filtering
        if ($user->role === 'counter') {
            $counter = \App\Models\Counter::where('assigned_user_id', $user->id)->first();
            if ($counter) {
                $query->where('reference_type', 'order')
                      ->whereHas('order', function($q) use ($counter) {
                          $q->where('counter_id', $counter->id);
                      });
            } else {
                // If counter role but no counter assigned, show nothing
                $query->whereRaw('1 = 0');
            }
        }

        // Date range filter
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        } else {
            // Default to last 30 days if no date specified
            $query->whereDate('created_at', '>=', now()->subDays(30));
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Type filter
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Category filter
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Payment method filter
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Amount range filter
        if ($request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Member search
        if ($request->member_search) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->member_search . '%')
                  ->orWhere('transaction_id', 'like', '%' . $request->member_search . '%')
                  ->orWhereHas('member', function($memberQuery) use ($request) {
                      $memberQuery->where('name', 'like', '%' . $request->member_search . '%')
                                  ->orWhere('card_number', 'like', '%' . $request->member_search . '%')
                                  ->orWhere('member_id', 'like', '%' . $request->member_search . '%');
                  });
            });
        }

        // Export functionality
        if ($request->export) {
            $exportTransactions = $query->get();
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="transactions_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($exportTransactions) {
                $file = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($file, [
                    'Transaction ID', 'Date', 'Customer Name', 'Member Card', 'Type', 'Category',
                    'Amount', 'Balance Before', 'Balance After', 'Payment Method', 'Status', 'Notes'
                ]);

                // CSV Data
                foreach ($exportTransactions as $txn) {
                    fputcsv($file, [
                        $txn->transaction_id,
                        $txn->created_at->format('Y-m-d H:i:s'),
                        $txn->customer_name,
                        $txn->member->card_number ?? '-',
                        ucfirst($txn->type),
                        ucfirst(str_replace('_', ' ', $txn->category)),
                        $txn->amount,
                        $txn->balance_before,
                        $txn->balance_after,
                        strtoupper($txn->payment_method),
                        ucfirst($txn->status),
                        $txn->notes ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // PDF export - return JSON data
        if ($request->export_pdf) {
            $exportTransactions = $query->get();
            
            return response()->json([
                'success' => true,
                'transactions' => $exportTransactions->map(function($txn) {
                    return [
                        'transaction_id' => $txn->transaction_id,
                        'customer_name' => $txn->customer_name,
                        'card_number' => $txn->member->card_number ?? '-',
                        'type' => $txn->type,
                        'category' => $txn->category,
                        'amount' => (float) $txn->amount,
                        'balance_before' => (float) $txn->balance_before,
                        'balance_after' => (float) $txn->balance_after,
                        'payment_method' => $txn->payment_method,
                        'status' => $txn->status,
                        'notes' => $txn->notes ?? '',
                        'created_at' => $txn->created_at->toISOString(),
                    ];
                }),
                'filters' => [
                    'from_date' => $request->from_date ?? now()->subDays(30)->format('Y-m-d'),
                    'to_date' => $request->to_date ?? now()->format('Y-m-d'),
                    'type' => $request->type ?? 'All Types',
                    'category' => $request->category ?? 'All Categories',
                ],
                'total' => $exportTransactions->count(),
            ]);
        }

        $transactions = $query->paginate(50)->withQueryString();
        
        // Statistics
        $fromDate = $request->from_date ?? now()->subDays(30)->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');
        
        $statsQuery = Transaction::whereBetween('created_at', [
            $fromDate . ' 00:00:00',
            $toDate . ' 23:59:59'
        ]);

        if ($user->role === 'counter') {
            $counter = \App\Models\Counter::where('assigned_user_id', $user->id)->first();
            if ($counter) {
                $statsQuery->where('reference_type', 'order')
                           ->whereHas('order', function($q) use ($counter) {
                               $q->where('counter_id', $counter->id);
                           });
            } else {
                $statsQuery->whereRaw('1 = 0');
            }
        }

        if ($request->type) {
            $statsQuery->where('type', $request->type);
        }
        if ($request->category) {
            $statsQuery->where('category', $request->category);
        }
        if ($request->status) {
            $statsQuery->where('status', $request->status);
        }
        if ($request->min_amount) {
            $statsQuery->where('amount', '>=', $request->min_amount);
        }
        if ($request->max_amount) {
            $statsQuery->where('amount', '<=', $request->max_amount);
        }
        if ($request->payment_method) {
            $statsQuery->where('payment_method', $request->payment_method);
        }

        $stats = [
            'total' => $statsQuery->count(),
            'total_amount' => $statsQuery->sum('amount'),
            'payments' => (clone $statsQuery)->where('type', 'payment')->where('status', 'completed')->sum('amount'),
            'topups' => (clone $statsQuery)->where('type', 'topup')->sum('amount'),
            'refunds' => (clone $statsQuery)->where('type', 'refund')->sum('amount'),
            'today_total' => Transaction::today()->count(),
            'today_revenue' => Transaction::today()->where('type', 'payment')->where('status', 'completed')->sum('amount'),
        ];

        return view('payments.transactions', compact('transactions', 'stats', 'fromDate', 'toDate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'type' => 'required|in:topup,payment,refund,transfer',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $member = null;
        $balanceBefore = 0;
        $balanceAfter = 0;

        if ($request->member_id) {
            $member = Member::find($request->member_id);
            if ($member) {
                $balanceBefore = $member->balance;
                
                if ($request->type === 'topup') {
                    $member->increment('balance', $request->amount);
                } elseif ($request->type === 'payment') {
                    if (!$member->safeDeduct($request->amount)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient member balance. Required: TZS ' . number_format($request->amount) . ', Available: TZS ' . number_format($member->balance)
                        ], 400);
                    }
                } elseif ($request->type === 'refund') {
                    $member->increment('balance', $request->amount);
                }
                
                $balanceAfter = $member->fresh()->balance;
            }
        }

        $transaction = Transaction::create([
            'transaction_id' => Transaction::generateTransactionId(),
            'member_id' => $request->member_id,
            'customer_name' => $request->customer_name,
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method' => $request->payment_method ?? 'upi',
            'reference_type' => $request->reference_type,
            'reference_id' => $request->reference_id,
            'notes' => $request->notes,
        ]);

        // Send SMS notification if member exists
        $smsSent = false;
        if ($member && ($request->send_sms ?? true)) {
            $smsService = new SmsService();
            if ($request->type === 'topup') {
                $smsResult = $smsService->sendTopupNotification($member, $request->amount, $balanceAfter);
            } elseif ($request->type === 'payment') {
                $categoryName = ucfirst(str_replace('_', ' ', $request->category));
                $smsResult = $smsService->sendPaymentNotification($member, $request->amount, $balanceAfter, $categoryName);
            } elseif ($request->type === 'refund') {
                $smsResult = $smsService->sendRefundNotification($member, $request->amount, $balanceAfter, $request->notes ?? 'Refund');
            }
            $smsSent = isset($smsResult) && ($smsResult['success'] ?? false);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Transaction recorded', 
            'transaction' => $transaction,
            'sms_sent' => $smsSent
        ]);
    }

    public function show($id)
    {
        $transaction = Transaction::with('member')->findOrFail($id);
        
        if (request()->wantsJson() || request()->has('action')) {
            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        }
        
        return view('payments.transaction-details', compact('transaction'));
    }

    public function receipt($id)
    {
        $transaction = Transaction::with('member')->findOrFail($id);
        return view('payments.transaction-receipt', compact('transaction'));
    }
}


