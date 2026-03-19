<?php

namespace App\Http\Controllers;

use App\Models\DrivingRangeSession;
use App\Models\DrivingRangeConfig;
use App\Models\Member;
use App\Models\Configuration;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DrivingRangeController extends Controller
{
    public function index()
    {
        $config = DrivingRangeConfig::getConfig();
        $activeSessions = DrivingRangeSession::active()->orderBy('start_time', 'desc')->get();
        $todaySessions = DrivingRangeSession::today()->orderBy('start_time', 'desc')->get();
        $members = Member::active()->orderBy('name')->get();
        
        $stats = [
            'active_sessions' => DrivingRangeSession::active()->count(),
            'balls_used_today' => DrivingRangeSession::today()->sum('balls_used'),
            'customers_today' => DrivingRangeSession::today()->count(),
            'revenue_today' => DrivingRangeSession::today()->completed()->sum('amount'),
        ];

        // Map bays with their current status and session info
        $occupiedSessions = DrivingRangeSession::active()->get()->keyBy('bay_number');
        $bays = [];
        for ($i = 1; $i <= $config->total_bays; $i++) {
            $session = $occupiedSessions->get($i);
            $bays[] = [
                'number' => $i,
                'status' => $session ? 'occupied' : 'available',
                'session' => $session
            ];
        }

        return view('golf-services.driving-range', compact('config', 'activeSessions', 'todaySessions', 'stats', 'bays', 'members'));
    }

    public function store(Request $request)
    {
        try {
            try {
                $request->validate([
                    'member_id' => 'nullable|exists:members,id',
                    'customer_name' => 'nullable|required_without:member_id|string',
                    'customer_phone' => 'nullable|string',
                    'payment_method' => 'required|in:balance,cash,card,mobile_money',
                    'bay_number' => 'required|integer|min:1',
                    'session_type' => 'required|in:ball_limit,bucket,unlimited',
                    'balls_limit_allowed' => 'nullable|integer|min:1|required_if:session_type,ball_limit',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            }

            $config = DrivingRangeConfig::getConfig();
        
        // Calculate amount based on session type
        $amount = match($request->session_type) {
            'ball_limit' => $config->ball_limit_price ?? $config->hourly_rate ?? 5000,
            'bucket' => $config->bucket_price * ($request->buckets_count ?? 1),
            'unlimited' => $config->unlimited_price,
            default => 0,
        };
        
        // Get balls limit for ball_limit sessions
        $ballsLimitAllowed = null;
        $initialBallsUsed = 0;
        if ($request->session_type === 'ball_limit') {
            $ballsLimitAllowed = $request->balls_limit_allowed ?? $config->balls_limit_per_session ?? 50;
            $initialBallsUsed = 0; // Start with 0, will be updated when session ends
        } elseif ($request->session_type === 'bucket') {
            $initialBallsUsed = ($request->buckets_count ?? 1) * $config->balls_per_bucket;
        }

        $member = null;
        $balanceBefore = 0;
        $balanceAfter = 0;
        $customerName = '';
        $customerPhone = '';
        $customerUpi = '';

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

        // If member is selected, use member data and check balance
        if ($request->member_id) {
            $member = Member::findOrFail($request->member_id);
            
            if ($member->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Member card is not active. Status: ' . $member->status
                ], 400);
            }

            // Apply member discount
            if ($member->membership_type !== 'guest') {
                $discount = $config->member_discount ?? 0;
                $amount = $amount * (1 - ($discount / 100));
            }

            // If paying with balance, check and deduct
            if ($request->payment_method === 'balance') {
                // Force strict balance deduction
                $balanceBefore = $member->balance;
                if (!$member->safeDeduct($amount)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient balance. Required: TZS ' . number_format($amount) . ', Available: TZS ' . number_format($member->balance),
                        'required' => $amount,
                        'balance' => $member->balance,
                    ], 400);
                }
                $balanceAfter = $member->balance;
            }

            $customerName = $member->name;
            $customerPhone = $member->phone;
            $customerUpi = $member->card_number;
        } else {
            // Non-member/guest
            $customerName = $request->customer_name ?? '';
            $customerPhone = $request->customer_phone ?? '';
            $customerUpi = '';
            
            // Ensure customer name is provided for guests
            if (empty($customerName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer name is required for non-member sessions'
                ], 422);
            }
        }

        // Map payment methods for database compatibility
        // Database enum: ['upi', 'cash', 'card', 'mobile']
        // Frontend sends: balance, cash, card, mobile_money
        $dbPaymentMethod = match($request->payment_method) {
            'balance' => 'upi', // Map balance to upi for non-member balance payments
            'mobile_money' => 'mobile',
            default => $request->payment_method,
        };
        
        // Create session
        $session = DrivingRangeSession::create([
            'member_id' => $member ? $member->id : null,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'customer_upi' => $customerUpi,
            'bay_number' => $request->bay_number,
            'session_type' => $request->session_type,
            'buckets_count' => $request->buckets_count ?? 1,
            'balls_limit_allowed' => $ballsLimitAllowed,
            'balls_used' => $initialBallsUsed,
            'start_time' => now(),
            'amount' => $amount,
            'payment_method' => $dbPaymentMethod,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        $smsSent = false;
        // Create transaction record only if member and balance payment
        if ($member && $request->payment_method === 'balance') {
            Transaction::create([
                'transaction_id' => Transaction::generateTransactionId(),
                'member_id' => $member->id,
                'customer_name' => $customerName,
                'type' => 'payment',
                'category' => 'driving_range',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'payment_method' => 'balance',
                'reference_type' => 'driving_range_session',
                'reference_id' => $session->id,
                'status' => 'completed',
            ]);
            
            // Send SMS notification for deduction
            $smsService = new SmsService();
            $smsResult = $smsService->sendPaymentNotification($member, $amount, $balanceAfter, 'Driving Range');
            $smsSent = $smsResult['success'] ?? false;

            // Log activity for member sessions
            ActivityLog::log('golf-services', 'created', "Driving range session started for {$customerName} (Bay #{$request->bay_number}, {$request->session_type})", 'DrivingRangeSession', $session->id, [
                'bay_number' => $request->bay_number,
                'session_type' => $request->session_type,
                'amount' => $amount,
                'balance_after' => $balanceAfter,
            ]);
        } else {
            // Send SMS notification for cash, card, or mobile money payments
            $smsService = new SmsService();
            $phoneNumber = $member ? $member->phone : $customerPhone;
            
            if ($phoneNumber) {
                $paymentMethodName = match($request->payment_method) {
                    'cash' => 'Cash',
                    'card' => 'Card',
                    'mobile_money' => 'Mobile Money',
                    default => 'Payment',
                };
                
                $message = "Dear {$customerName}, your Driving Range session has started at Bay {$request->bay_number}. ";
                $message .= "Amount: TZS " . number_format($amount) . " (Paid via {$paymentMethodName}). ";
                $message .= "Session Type: " . ucfirst($request->session_type) . ". Enjoy your practice!";
                
                $smsResult = $smsService->send($phoneNumber, $message);
                $smsSent = $smsResult['success'] ?? false;
            }

            // Log activity for non-member/guest sessions
            ActivityLog::log('golf-services', 'created', "Driving range session started for {$customerName} (Bay #{$request->bay_number}, {$request->session_type}) - Guest", 'DrivingRangeSession', $session->id, [
                'bay_number' => $request->bay_number,
                'session_type' => $request->session_type,
                'amount' => $amount,
                'payment_method' => $request->payment_method,
            ]);
        }

        $message = 'Session started successfully. Amount: TZS ' . number_format($amount);
        if ($member && $request->payment_method === 'balance') {
            $message .= '. New balance: TZS ' . number_format($balanceAfter);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'session' => $session,
            'amount_charged' => $amount,
            'new_balance' => $member ? $balanceAfter : null,
            'sms_sent' => $smsSent,
        ]);
        } catch (\Exception $e) {
            Log::error('Driving Range Session Store Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error starting session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function endSession(Request $request, $id)
    {
        $session = DrivingRangeSession::findOrFail($id);
        
        if ($session->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Session is not active',
            ], 400);
        }

        $config = DrivingRangeConfig::getConfig();
        $endTime = now();
        $duration = $session->start_time->diffInMinutes($endTime);
        $originalAmount = $session->amount;
        $additionalCharge = 0;
        $ballsUsed = $request->balls_used ?? $session->balls_used ?? 0;

        // For ball_limit sessions, check if balls used exceeds the limit and charge additional if needed
        if ($session->session_type === 'ball_limit' && $session->balls_limit_allowed) {
            // If balls used exceeds the limit, calculate additional charge
            if ($ballsUsed > $session->balls_limit_allowed) {
                $extraBalls = $ballsUsed - $session->balls_limit_allowed;
                // Charge for extra balls at bucket price per ball rate
                $pricePerBall = ($config->bucket_price ?? 2000) / ($config->balls_per_bucket ?? 50);
                $additionalCharge = ceil($extraBalls * $pricePerBall);
                
                // Get member and deduct additional charge
                $member = null;
                if ($session->member_id) {
                    $member = Member::find($session->member_id);
                } elseif ($session->customer_upi) {
                    $member = Member::where('card_number', $session->customer_upi)->first();
                }
                
                $balanceBefore = $member->balance;
                if ($member->safeDeduct($additionalCharge)) {
                    $balanceAfter = $member->balance;
                    
                    // Record additional charge transaction
                    Transaction::create([
                        'transaction_id' => Transaction::generateTransactionId(),
                        'member_id' => $member->id,
                        'customer_name' => $member->name,
                        'type' => 'payment',
                        'category' => 'driving_range',
                        'amount' => $additionalCharge,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'payment_method' => 'balance',
                        'reference_type' => 'driving_range_session',
                        'reference_id' => $session->id,
                        'notes' => "Additional charge for {$extraBalls} extra balls beyond limit",
                        'status' => 'completed',
                    ]);
                    
                    // Send SMS notification for additional charge
                    $smsService = new SmsService();
                    $smsService->sendPaymentNotification($member, $additionalCharge, $balanceAfter, 'Driving Range - Extra Balls');
                    
                    // Log activity for additional charge
                    ActivityLog::log('golf-services', 'updated', "Additional charge for extra balls: TZS " . number_format($additionalCharge), 'DrivingRangeSession', $session->id, [
                        'additional_charge' => $additionalCharge,
                        'extra_balls' => $extraBalls,
                        'balance_after' => $balanceAfter,
                    ]);
                } else {
                    // If insufficient balance or member not found, still end session but note the issue
                    $additionalCharge = 0;
                }
            }
        }

        $session->amount = $originalAmount + $additionalCharge;

        $session->update([
            'end_time' => $endTime,
            'duration_minutes' => $duration,
            'balls_used' => $ballsUsed,
            'amount' => $session->amount,
            'status' => 'completed',
        ]);

        // Log activity
        ActivityLog::log('golf-services', 'completed', "Driving range session ended (Duration: {$duration} minutes)", 'DrivingRangeSession', $session->id, [
            'duration_minutes' => $duration,
            'final_amount' => $session->amount,
            'additional_charge' => $additionalCharge ?? 0,
        ]);

        // Send SMS notification when session ends
        $smsSent = false;
        $member = null;
        if ($session->member_id) {
            $member = Member::find($session->member_id);
        } elseif ($session->customer_upi) {
            $member = Member::where('card_number', $session->customer_upi)->first();
        }
        
        $phoneNumber = $member ? $member->phone : $session->customer_phone;
        
        if ($phoneNumber) {
            try {
                $smsService = new SmsService();
                $durationHours = round($duration / 60, 1);
                $message = "Dear {$session->customer_name}, your Driving Range session at Bay {$session->bay_number} has ended. ";
                if ($session->session_type === 'ball_limit') {
                    $message .= "Balls used: {$ballsUsed}";
                    if ($session->balls_limit_allowed) {
                        $message .= " (Limit: {$session->balls_limit_allowed})";
                    }
                } else {
                    $message .= "Duration: {$durationHours} hours";
                }
                $message .= ". Total Amount: TZS " . number_format($session->amount) . ". ";
                if ($additionalCharge > 0) {
                    if ($session->session_type === 'ball_limit') {
                        $message .= "Additional charge of TZS " . number_format($additionalCharge) . " was applied for extra balls. ";
                    } else {
                        $message .= "Additional charge of TZS " . number_format($additionalCharge) . " was applied for extended time. ";
                    }
                }
                $message .= "Thank you for using our Driving Range!";
                
                $smsResult = $smsService->send($phoneNumber, $message);
                $smsSent = $smsResult['success'] ?? false;
            } catch (\Exception $e) {
                Log::error('Failed to send SMS for session end', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $message = 'Session ended successfully';
        if ($additionalCharge > 0) {
            if ($session->session_type === 'ball_limit') {
                $message .= '. Additional charge of TZS ' . number_format($additionalCharge) . ' deducted for extra balls.';
            } else {
                $message .= '. Additional charge of TZS ' . number_format($additionalCharge) . ' deducted for extended time.';
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'session' => $session,
            'additional_charge' => $additionalCharge,
            'sms_sent' => $smsSent,
        ]);
    }

    public function cancelSession($id)
    {
        $session = DrivingRangeSession::findOrFail($id);
        
        $session->update([
            'end_time' => now(),
            'status' => 'cancelled',
            'amount' => 0,
        ]);

        // Log activity
        ActivityLog::log('golf-services', 'cancelled', "Driving range session cancelled", 'DrivingRangeSession', $session->id);

        return response()->json([
            'success' => true,
            'message' => 'Session cancelled',
        ]);
    }

    public function updateConfig(Request $request)
    {
        $config = DrivingRangeConfig::getConfig();
        
        $config->update($request->only([
            'total_bays', 'balls_per_bucket', 'range_distance',
            'has_roof', 'has_lighting', 'has_tracking',
            'hourly_rate', 'ball_limit_price', 'balls_limit_per_session',
            'bucket_price', 'unlimited_price',
            'member_discount', 'premium_rate', 'regular_rate',
        ]));

        // Log activity
        ActivityLog::log('golf-services', 'updated', "Driving range configuration updated", 'DrivingRangeConfig', $config->id);

        return response()->json([
            'success' => true,
            'message' => 'Configuration updated successfully',
            'config' => $config,
        ]);
    }

    public function history(Request $request)
    {
        $query = DrivingRangeSession::query();

        if ($request->from_date) {
            $query->whereDate('start_time', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('start_time', '<=', $request->to_date);
        }

        $sessions = $query->orderBy('start_time', 'desc')->paginate(20);

        return response()->json($sessions);
    }

    public function stats()
    {
        return response()->json([
            'active_sessions' => DrivingRangeSession::active()->count(),
            'balls_used_today' => DrivingRangeSession::today()->sum('balls_used'),
            'customers_today' => DrivingRangeSession::today()->count(),
            'revenue_today' => DrivingRangeSession::today()->completed()->sum('amount'),
        ]);
    }

    public function rangeConfig()
    {
        $config = DrivingRangeConfig::getConfig();
        return view('golf-services.range-configuration', compact('config'));
    }

    public function pricingConfig()
    {
        $config = DrivingRangeConfig::getConfig();
        return view('golf-services.pricing-configuration', compact('config'));
    }
}

