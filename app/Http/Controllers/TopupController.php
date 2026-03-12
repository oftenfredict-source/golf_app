<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopupController extends Controller
{
    public function index()
    {
        $members = Member::active()->orderBy('name')->get();
        $topups = Topup::with('member')->orderBy('created_at', 'desc')->paginate(100);
        
        $stats = [
            'topups_today' => Topup::today()->count(),
            'amount_today' => Topup::today()->sum('amount'),
            'total_balance' => Member::active()->sum('balance'),
            'active_members' => Member::active()->count(),
        ];

        return view('payments.top-ups', compact('members', 'topups', 'stats'));
    }
    
    public function getTopups(Request $request)
    {
        $query = Topup::with('member')->orderBy('created_at', 'desc');
        
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
        if ($request->has('method')) {
            $query->where('payment_method', $request->method);
        }
        
        $topups = $query->limit(100)->get();
        
        return response()->json($topups->map(function($topup) {
            return [
                'id' => $topup->id,
                'member_id' => $topup->member_id,
                'member_name' => $topup->member->name ?? 'N/A',
                'card_number' => $topup->member->card_number ?? 'N/A',
                'amount' => (float) $topup->amount,
                'balance_before' => (float) $topup->balance_before,
                'balance_after' => (float) $topup->balance_after,
                'payment_method' => $topup->payment_method === 'mobile' ? 'mobile_money' : $topup->payment_method,
                'reference' => $topup->reference_number,
                'created_at' => $topup->created_at->toISOString(),
                'sms_sent' => (bool) $topup->sms_sent,
            ];
        }));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:1000',
            'payment_method' => 'required|in:cash,card,mobile_money,bank_transfer',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $member = Member::findOrFail($request->member_id);
        $balanceBefore = $member->balance;
        
        $member->increment('balance', $request->amount);
        $balanceAfter = $member->fresh()->balance;

        // Map mobile_money to mobile for database compatibility (SQLite enum constraint)
        $paymentMethod = $request->payment_method === 'mobile_money' ? 'mobile' : $request->payment_method;
        
        $topup = Topup::create([
            'member_id' => $member->id,
            'amount' => $request->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method' => $paymentMethod,
            'reference_number' => $request->reference_number,
            'sms_sent' => $request->send_sms ?? false,
            'processed_by' => Auth::id(),
        ]);

        // Create transaction record (transactions table enum: ['upi', 'cash', 'card', 'mobile', 'balance'])
        // Map mobile back to mobile for transactions table (it already accepts 'mobile')
        $transactionPaymentMethod = $paymentMethod; // Already mapped to 'mobile' if it was 'mobile_money'
        Transaction::create([
            'transaction_id' => Transaction::generateTransactionId(),
            'member_id' => $member->id,
            'customer_name' => $member->name,
            'type' => 'topup',
            'category' => 'membership',
            'amount' => $request->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method' => $transactionPaymentMethod,
            'reference_type' => 'topup',
            'reference_id' => $topup->id,
        ]);

        // Log activity
        ActivityLog::log('payments', 'topup', "Top-up for {$member->name}: TZS " . number_format($request->amount), 'Topup', $topup->id, [
            'amount' => $request->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'payment_method' => $paymentMethod,
        ]);

        // Send SMS notification if requested or enabled by default
        $smsSent = false;
        if ($request->send_sms ?? true) {
            $smsService = new SmsService();
            $smsResult = $smsService->sendTopupNotification($member, $request->amount, $balanceAfter);
            $smsSent = $smsResult['success'] ?? false;
            
            // Update topup record with SMS status
            $topup->update(['sms_sent' => $smsSent]);
        }

        return response()->json([
            'success' => true, 
            'message' => "Top-up successful. New balance: TZS " . number_format($balanceAfter),
            'topup' => $topup->load('member'),
            'new_balance' => $balanceAfter,
            'sms_sent' => $smsSent
        ]);
    }

    public function receipt($id)
    {
        $topup = Topup::with('member')->findOrFail($id);
        return view('payments.topup-receipt', compact('topup'));
    }
}


