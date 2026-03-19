<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query();
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                /** @var \Illuminate\Database\Eloquent\Builder $q */
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('card_number', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('upi_id', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Membership type filter
        if ($request->has('membership_type') && $request->membership_type) {
            $query->where('membership_type', $request->membership_type);
        }
        
        // Tier filter (Full Access vs Golf Only)
        if ($request->has('tier') && $request->tier !== null && $request->tier !== '') {
            $query->where('has_full_access', $request->tier);
        }
        
        // Balance range filter
        if ($request->has('balance_min') && $request->balance_min !== null) {
            $query->where('balance', '>=', $request->balance_min);
        }
        if ($request->has('balance_max') && $request->balance_max !== null) {
            $query->where('balance', '<=', $request->balance_max);
        }
        
        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['name', 'card_number', 'balance', 'created_at', 'member_id'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $members = $query->paginate(50)->withQueryString();
        
        // Calculate stats with proper decimal handling
        $stats = [
            'total_accounts' => Member::count(),
            'active_accounts' => Member::where('status', 'active')->count(),
            'pending_accounts' => Member::where('status', 'inactive')->count(),
            'total_balance' => round(Member::active()->sum('balance'), 2),
            'standard_count' => Member::where('membership_type', 'standard')->count(),
            'vip_count' => Member::where('membership_type', 'vip')->count(),
            'premier_count' => Member::where('membership_type', 'premier')->count(),
            'low_balance_count' => Member::where('balance', '<', 10000)->where('status', 'active')->count(),
        ];
        
        // Handle export
        if ($request->has('export') && $request->export == '1') {
            $members = $query->get();
            
            $filename = 'members_export_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($members) {
                $file = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($file, [
                    'Card Number', 'Member ID', 'Name', 'Phone', 'Email', 
                    'Membership Type', 'Balance', 'Status', 'Valid Until', 'Created Date'
                ]);
                
                // CSV Data
                foreach ($members as $member) {
                    fputcsv($file, [
                        $member->card_number,
                        $member->member_id ?? '',
                        $member->name,
                        $member->phone,
                        $member->email ?? '',
                        strtoupper($member->membership_type ?? 'standard'),
                        number_format($member->balance, 2),
                        ucfirst($member->status),
                        $member->valid_until ? $member->valid_until->format('Y-m-d') : '',
                        $member->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }

        return view('payments.upi-management', compact('members', 'stats'));
    }

    public function generateCard(Request $request, $id = null)
    {
        $member = null;
        $members = Member::where('has_full_access', true)->orderBy('created_at', 'desc')->get();
        
        if ($id) {
            $member = Member::where('ulid', $id)->orWhere('id', $id)->first();
        }
        
        return view('payments.generate-card', compact('member', 'members'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|unique:members,phone',
                'email' => 'nullable|email|max:255',
                'membership_type' => 'nullable|string|in:standard,vip,premier',
                'ball_limit' => 'nullable|integer|min:0',
                'show_balance' => 'nullable|boolean',
                'initial_balance' => 'nullable|numeric|min:0',
                'valid_until' => 'nullable|date',
                'notes' => 'nullable|string|max:1000',
                'has_full_access' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Auto-generate card number and UPI ID
        // Pass membership type to reserve 000001-000020 for VIP members only
        $hasFullAccess = $request->has_full_access == '1' || $request->has_full_access === true;
        $membershipType = $hasFullAccess ? ($request->membership_type ?? 'standard') : null;
        
        $cardNumber = null;
        if ($hasFullAccess) {
            $cardNumber = Member::generateCardNumber($membershipType);
            // Ensure unique card number
            while (Member::where('card_number', $cardNumber)->exists()) {
                $cardNumber = Member::generateCardNumber($membershipType);
            }
        }

        $upiId = Member::generateUpiId($request->name);
        $memberId = Member::generateMemberId();

        // Ensure unique member_id
        while (Member::where('member_id', $memberId)->exists()) {
            $memberId = Member::generateMemberId();
        }

        // Ensure unique UPI ID
        while (Member::where('upi_id', $upiId)->exists()) {
            $upiId = Member::generateUpiId($request->name);
        }

        // Parse valid_until date if provided
        $validUntil = $request->valid_until ? \Carbon\Carbon::parse($request->valid_until) : now()->addYear();

        // Map membership type to card color (only 3 types: STANDARD, VIP, PREMIER)
        $cardColors = [
            'standard' => 'silver',  // STANDARD - Silver Card
            'vip' => 'black',        // VIP - Black Card
            'premier' => 'gold',     // PREMIER - Gold Card
        ];
        $cardColor = $membershipType ? ($cardColors[$membershipType] ?? 'silver') : 'gray';

        // Create member record
        try {
            $member = Member::create([
                'member_id' => $memberId,
                'name' => $request->name,
                'email' => $request->email ?? null,
                'phone' => $request->phone,
                'upi_id' => $upiId,
                'card_number' => $cardNumber,
                'membership_type' => $membershipType,
                'card_color' => $cardColor,
                'balance' => $request->initial_balance ?? 0,
                'ball_limit' => $request->ball_limit ?? null,
                'show_balance' => $request->show_balance ?? true,
                'valid_until' => $validUntil,
                'status' => 'active',
                'notes' => $request->notes ?? null,
                'has_full_access' => $request->has_full_access ?? false,
                'card_status' => Member::CARD_STATUS_PENDING_DESIGN, // DB column is NOT NULL
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors (e.g., unique constraint violations, missing columns)
            $message = 'Error creating member. Please try again.';
            
            // Check if it's a unique constraint violation (duplicate phone)
            if (str_contains($e->getMessage(), 'UNIQUE constraint failed') || 
                str_contains($e->getMessage(), 'Duplicate entry') ||
                str_contains($e->getMessage(), 'members.phone')) {
                $message = 'This phone number is already registered. Please use a different phone number or contact support.';
            }
            // Check for missing column errors
            elseif (str_contains($e->getMessage(), 'column') && 
                    (str_contains($e->getMessage(), 'not found') || str_contains($e->getMessage(), 'doesn\'t exist'))) {
                $message = 'Database schema error. Please run migrations: php artisan migrate';
            }
            
            // Log the full error for debugging
            Log::error('Member creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 422);
            }
            throw $e;
        }

        // Log activity
        ActivityLog::log('payments', 'created', "Member registered: {$member->name} (Card: {$cardNumber})", 'Member', $member->id, [
            'card_number' => $cardNumber,
            'member_id' => $memberId,
            'initial_balance' => $request->initial_balance ?? 0,
        ]);

        // Send SMS notification for registration
        $smsSent = false;
        $smsMessage = '';
        try {
            $smsService = new SmsService();
            $smsResult = $smsService->sendRegistrationNotification($member, $cardNumber, $upiId, $request->initial_balance ?? 0);
            $smsSent = $smsResult['success'] ?? false;
            $smsMessage = $smsResult['message'] ?? '';
        } catch (\Exception $e) {
            Log::error('Failed to send registration SMS', [
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            $smsMessage = 'SMS notification failed: ' . $e->getMessage();
        }

        // If it's an AJAX/JSON request, return JSON response
        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Member registered successfully! Card Number: ' . $cardNumber,
                'member' => [
                    'id' => $member->id,
                    'ulid' => $member->ulid,
                    'member_id' => $member->member_id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'card_number' => $member->card_number,
                    'balance' => $member->balance,
                ],
                'member_id' => $member->member_id,
                'card_number' => $cardNumber,
                'upi_id' => $upiId,
                'redirect_url' => $cardNumber ? route('payments.generate-card', ['id' => $member->ulid ?? $member->id]) : null
            ]);
        }

        // For regular form submissions, redirect to card generation ONLY if cardholder
        if ($hasFullAccess) {
            return redirect()->route('payments.generate-card', ['id' => $member->ulid ?? $member->id])
                ->with('success', 'Member registered successfully! Card Number: ' . $cardNumber);
        }

        return redirect()->route('payments.upi-management')
            ->with('success', 'Custom Member registered successfully!');
    }

    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $updateData = $request->only(['name', 'email', 'phone', 'membership_type', 'valid_until', 'status', 'notes']);
        
        if ($request->has('has_full_access')) {
            $newHasFullAccess = $request->has_full_access == '1' || $request->has_full_access === true;
            $oldHasFullAccess = (bool) $member->has_full_access;
            
            $updateData['has_full_access'] = $newHasFullAccess;
            
            // If gaining full access and doesn't have a card, generate one
            if ($newHasFullAccess && !$member->card_number) {
                $updateData['card_number'] = Member::generateCardNumber($member->membership_type);
            } 
            // If losing full access, clear the card number and membership type
            elseif (!$newHasFullAccess) {
                $updateData['card_number'] = null;
                $updateData['membership_type'] = null;
            }
        }
        $member->update($updateData);

        // Log activity
        ActivityLog::log('payments', 'updated', "Member updated: {$member->name}", 'Member', $member->id);

        return response()->json(['success' => true, 'message' => 'Member updated', 'member' => $member]);
    }

    public function search(Request $request)
    {
        $query = trim($request->q);
        
        $queryBuilder = Member::active();
        
        if ($request->has('tier') && $request->tier !== null && $request->tier !== '') {
            $queryBuilder->where('has_full_access', $request->tier);
        }

        if (empty($query) || strlen($query) < 1) {
            // If no query, return all active members (limited to 100 for performance)
            $members = $queryBuilder
                ->orderBy('name')
                ->limit(100)
                ->get();
        } else {
            // Search by name, card number, or UPI ID
            $members = $queryBuilder
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('card_number', 'like', "%{$query}%")
                      ->orWhere('upi_id', 'like', "%{$query}%")
                      ->orWhere('member_id', 'like', "%{$query}%");
                })
                ->orderBy('name')
                ->get(); // Return all matching results
        }

        return response()->json($members);
    }

    public function getBalance($id)
    {
        $member = Member::findOrFail($id);
        return response()->json([
            'balance' => $member->balance,
            'can_use_services' => $member->balance > 0,
            'member' => $member
        ]);
    }

    public function checkCard(Request $request)
    {
        $cardNumber = $request->card_number;
        $member = Member::where('card_number', $cardNumber)
            ->orWhere('upi_id', $cardNumber)
            ->orWhere('phone', $cardNumber)
            ->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Card not found. Please register member first.'
            ], 404);
        }

        if ($member->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Card is ' . $member->status . '. Please contact admin.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'member' => $member,
            'balance' => $member->balance,
            'can_use_services' => $member->balance > 0,
        ]);
    }

    public function chargeService(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'service' => 'required|string',
        ]);

        $member = Member::findOrFail($request->member_id);

        if (!$member->canAfford($request->amount)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance. Current balance: TZS ' . number_format((float)$member->balance),
                'balance' => $member->balance,
            ], 400);
        }

        $balanceBefore = $member->balance;
        $success = $member->deductBalance($request->amount, $request->service);
        $balanceAfter = $member->fresh()->balance;

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment',
                'balance' => $member->balance,
            ], 400);
        }

        // Send SMS notification for deduction
        $smsSent = false;
        if ($request->send_sms ?? true) {
            $smsService = new \App\Services\SmsService();
            $smsResult = $smsService->sendPaymentNotification($member, $request->amount, $balanceAfter, $request->service ?? 'Service');
            $smsSent = $smsResult['success'] ?? false;
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment successful',
            'new_balance' => $balanceAfter,
            'sms_sent' => $smsSent
        ]);
    }

    public function show($id)
    {
        $member = Member::findOrFail($id);
        
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'member' => $member
            ]);
        }
        
        return view('payments.member-details', compact('member'));
    }

    public function adjustBalance(Request $request, $id)
    {
        $request->validate([
            'new_balance' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $member = Member::findOrFail($id);
        $balanceBefore = $member->balance;
        $newBalance = $request->new_balance;
        $difference = $newBalance - $balanceBefore;

        // Update balance
        $member->update(['balance' => $newBalance]);

        // Log activity
        ActivityLog::log('payments', 'updated', "Balance adjusted for {$member->name}: TZS " . number_format($newBalance) . " (Reason: {$request->reason})", 'Member', $member->id, [
            'balance_before' => $balanceBefore,
            'balance_after' => $newBalance,
            'reason' => $request->reason,
        ]);

        // Record transaction
        Transaction::create([
            'transaction_id' => Transaction::generateTransactionId(),
            'member_id' => $member->id,
            'customer_name' => $member->name,
            'type' => $difference > 0 ? 'topup' : ($difference < 0 ? 'payment' : 'transfer'),
            'category' => 'other',
            'amount' => abs($difference),
            'balance_before' => $balanceBefore,
            'balance_after' => $newBalance,
            'payment_method' => 'balance',
            'notes' => 'Balance Adjustment: ' . $request->reason,
            'status' => 'completed',
        ]);

        // Send SMS notification for balance adjustment
        $smsSent = false;
        if ($difference != 0) {
            $smsService = new \App\Services\SmsService();
            if ($difference > 0) {
                $smsResult = $smsService->sendTopupNotification($member, abs($difference), $newBalance);
            } else {
                $smsResult = $smsService->sendPaymentNotification($member, abs($difference), $newBalance, 'Balance Adjustment');
            }
            $smsSent = isset($smsResult) && ($smsResult['success'] ?? false);
        }

        return response()->json([
            'success' => true,
            'message' => 'Balance adjusted successfully. New balance: TZS ' . number_format($newBalance),
            'new_balance' => $newBalance,
            'sms_sent' => $smsSent
        ]);
    }

    public function getTransactions($id)
    {
        $member = Member::findOrFail($id);
        $transactions = Transaction::where('member_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
            'member' => $member
        ]);
    }

    public function transactionsPdf($id)
    {
        $member = Member::findOrFail($id);
        $transactions = Transaction::where('member_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate summary
        $summary = [
            'total_transactions' => $transactions->count(),
            'total_payments' => $transactions->where('type', 'payment')->sum('amount'),
            'total_topups' => $transactions->where('type', 'topup')->sum('amount'),
            'total_refunds' => $transactions->where('type', 'refund')->sum('amount'),
            'current_balance' => $member->balance,
        ];

        return view('payments.member-transactions-pdf', compact('member', 'transactions', 'summary'));
    }

    public function generateCardPdf($id)
    {
        // Support both ULID and numeric ID lookup
        $member = Member::where('ulid', $id)->orWhere('id', $id)->firstOrFail();
        
        // Generate QR code data (UPI payment URL format)
        $paymentUrl = 'golfclub://pay?card=' . urlencode($member->card_number) . '&name=' . urlencode($member->name) . '&id=' . urlencode($member->member_id);
        $upiUrl = $paymentUrl;
        
        return view('payments.member-card-pdf', compact('member', 'upiUrl'));
    }

    public function toggleCardIssued(Request $request, $id)
    {
        $member = Member::findOrFail($id);
        $member->is_card_issued = !$member->is_card_issued;
        
        if ($member->is_card_issued) {
            $member->card_status = Member::CARD_STATUS_ISSUED;
            $member->card_issued_at = now();
        } else {
            $member->card_status = Member::CARD_STATUS_READY;
            $member->card_issued_at = null;
        }
        
        $member->save();

        ActivityLog::log('payments', 'updated', "Member card issuance toggled for {$member->name}: " . ($member->is_card_issued ? 'Issued' : 'Not Issued'), 'Member', $member->id);

        return response()->json([
            'success' => true,
            'is_card_issued' => $member->is_card_issued,
            'card_status' => $member->card_status,
            'message' => 'Member card status updated.'
        ]);
    }

    public function updateCardStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending_design,printing,ready,issued'
        ]);

        $member = Member::findOrFail($id);
        $oldStatus = $member->card_status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return response()->json(['success' => true, 'message' => 'Status is already ' . $newStatus]);
        }

        $member->card_status = $newStatus;

        if ($newStatus === Member::CARD_STATUS_PENDING_DESIGN) {
            $member->card_design_at = null;
            $member->card_ready_at = null;
            $member->card_issued_at = null;
            $member->is_card_issued = false;
        } elseif ($newStatus === Member::CARD_STATUS_PRINTING) {
            $member->card_design_at = now();
        } elseif ($newStatus === Member::CARD_STATUS_READY) {
            $member->card_ready_at = now();
            // Send SMS notification
            try {
                $smsService = new SmsService();
                $smsService->sendCardReadyNotification($member);
            } catch (\Exception $e) {
                Log::error('Failed to send card ready SMS', ['member_id' => $member->id, 'error' => $e->getMessage()]);
            }
        } elseif ($newStatus === Member::CARD_STATUS_ISSUED) {
            $member->card_issued_at = now();
            $member->is_card_issued = true;
        }

        $member->save();

        ActivityLog::log('payments', 'updated', "Card status for {$member->name} updated from {$oldStatus} to {$newStatus}", 'Member', $member->id);

        return response()->json([
            'success' => true,
            'message' => 'Card status updated to ' . ucfirst(str_replace('_', ' ', $newStatus)),
            'card_status' => $newStatus,
            'is_card_issued' => $member->is_card_issued
        ]);
    }
}

