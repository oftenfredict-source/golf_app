<?php

namespace App\Http\Controllers;

use App\Models\EntryGate;
use App\Models\AccessLog;
use App\Models\Member;
use App\Models\AccessControlConfig;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EntryGateController extends Controller
{
    public function index()
    {
        $gates = EntryGate::orderBy("name")->get();
        $todayLogs = AccessLog::today()->get();
        
        // Basic Statistics
        $stats = [
            "total_gates" => EntryGate::count(),
            "active_gates" => EntryGate::active()->online()->count(),
            "today_entries" => AccessLog::today()->entries()->successful()->count(),
            "today_exits" => AccessLog::today()->exits()->successful()->count(),
            "currently_inside" => $this->calculateCurrentlyInside(),
            "today_denied" => AccessLog::today()->denied()->count(),
        ];
        
        // Advanced Analytics
        $analytics = [
            // Hourly entries for today
            "hourly_entries" => $this->getHourlyEntries(),
            // Daily entries for last 7 days
            "daily_entries" => $this->getDailyEntries(7),
            // Gate-wise statistics
            "gate_stats" => $this->getGateStats(),
            // Member type breakdown
            "member_type_stats" => $this->getMemberTypeStats(),
            // Peak hours
            "peak_hours" => $this->getPeakHours(),
            // Currently inside members
            "inside_members" => $this->getCurrentlyInsideMembers(),
        ];
        
        $recentLogs = AccessLog::with(["gate", "member"])
            ->orderBy("created_at", "desc")
            ->limit(50)
            ->get();
        
        return view("access-control.entry-gates", compact("gates", "stats", "recentLogs", "analytics"));
    }
    
    private function getHourlyEntries()
    {
        $isSqlite = DB::getDriverName() === "sqlite";
        $tz = config("app.timezone", "Africa/Dar_es_Salaam");

        if ($isSqlite) {
            // SQLite: convert UTC to local time before extracting the hour
            $hourExpr = "CAST(strftime('%H', datetime(created_at, '+3 hours')) AS INTEGER)";
        } else {
            // MySQL: CONVERT_TZ from UTC to local timezone
            $hourExpr = "HOUR(CONVERT_TZ(created_at, '+00:00', '+03:00'))";
        }

        $entries = AccessLog::whereDate(DB::raw("CONVERT_TZ(created_at, '+00:00', '+03:00')"), now()->timezone($tz)->toDateString())
            ->where("access_type", "entry")
            ->where("status", "success")
            ->selectRaw("CAST($hourExpr AS UNSIGNED) as hour, COUNT(*) as count")
            ->groupBy("hour")
            ->orderBy("hour")
            ->get();

        // Initialize array with all 24 hours set to 0
        $data = array_fill(0, 24, 0);

        // Fill in actual counts
        foreach ($entries as $entry) {
            $hour = (int)$entry->hour;
            if ($hour >= 0 && $hour < 24) {
                $data[$hour] = (int)$entry->count;
            }
        }

        return array_values($data);
    }
    
    private function getDailyEntries($days = 7)
    {
        $isSqlite = DB::getDriverName() === "sqlite";
        $tz = config("app.timezone", "Africa/Dar_es_Salaam");

        if ($isSqlite) {
            $dateExpr = "strftime('%Y-%m-%d', datetime(created_at, '+3 hours'))";
        } else {
            $dateExpr = "DATE(CONVERT_TZ(created_at, '+00:00', '+03:00'))";
        }

        $entries = AccessLog::where("created_at", ">=", Carbon::now()->timezone($tz)->subDays($days)->startOfDay()->utc())
            ->where("access_type", "entry")
            ->where("status", "success")
            ->selectRaw("$dateExpr as date, COUNT(*) as count")
            ->groupBy("date")
            ->orderBy("date")
            ->get();

        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->timezone($tz)->subDays($i)->format("Y-m-d");
            $data[$date] = 0;
        }

        foreach ($entries as $entry) {
            if (isset($data[$entry->date])) {
                $data[$entry->date] = $entry->count;
            }
        }

        return $data;
    }
    
    private function getGateStats()
    {
        return EntryGate::withCount([
            "accessLogs as today_entries" => function($query) {
                $query->whereDate("created_at", today())
                    ->where("access_type", "entry")
                    ->where("status", "success");
            },
            "accessLogs as today_exits" => function($query) {
                $query->whereDate("created_at", today())
                    ->where("access_type", "exit")
                    ->where("status", "success");
            },
            "accessLogs as today_denied" => function($query) {
                $query->whereDate("created_at", today())
                    ->where("status", "denied");
            }
        ])->get();
    }
    
    private function getMemberTypeStats()
    {
        return AccessLog::whereDate("access_logs.created_at", today())
            ->where("access_logs.access_type", "entry")
            ->where("access_logs.status", "success")
            ->join("members", "access_logs.member_id", "=", "members.id")
            ->selectRaw("members.membership_type, COUNT(*) as count")
            ->groupBy("members.membership_type")
            ->get()
            ->pluck("count", "membership_type")
            ->toArray();
    }
    
    private function getPeakHours()
    {
        $isSqlite = DB::getDriverName() === "sqlite";
        $tz = config("app.timezone", "Africa/Dar_es_Salaam");

        if ($isSqlite) {
            $hourExpr = "CAST(strftime('%H', datetime(created_at, '+3 hours')) AS INTEGER)";
        } else {
            $hourExpr = "HOUR(CONVERT_TZ(created_at, '+00:00', '+03:00'))";
        }

        $peak = AccessLog::whereDate(\DB::raw("CONVERT_TZ(created_at, '+00:00', '+03:00')"), now()->timezone($tz)->toDateString())
            ->where("access_type", "entry")
            ->where("status", "success")
            ->selectRaw("CAST($hourExpr AS UNSIGNED) as hour, COUNT(*) as count")
            ->groupBy("hour")
            ->orderByDesc("count")
            ->limit(3)
            ->get();

        return $peak->pluck("hour")->toArray();
    }
    
    private function getCurrentlyInsideMembers()
    {
        // Get all successful entries today
        $entries = AccessLog::today()
            ->entries()
            ->successful()
            ->with("member")
            ->get();
        
        // Get all successful exits today
        $exits = AccessLog::today()
            ->exits()
            ->successful()
            ->pluck("member_id")
            ->toArray();
        
        // Members who entered but haven't exited
        $inside = $entries->filter(function($log) use ($exits) {
            return $log->member_id && !in_array($log->member_id, $exits);
        })->unique("member_id");
        
        return $inside->values();
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "type" => "required|in:entry,exit,both",
            "location" => "nullable|string|max:255",
            "device_id" => "nullable|string|max:255",
            "is_active" => "boolean",
        ]);

        $gate = EntryGate::create([
            "name" => $request->name,
            "type" => $request->type,
            "location" => $request->location,
            "device_id" => $request->device_id,
            "is_active" => $request->has("is_active") ? $request->is_active : true,
            "requires_card" => $request->has("requires_card") ? $request->requires_card : false,
        ]);

        return response()->json([
            "success" => true,
            "message" => "Gate created successfully",
            "gate" => $gate
        ]);
    }

    public function update(Request $request, $id)
    {
        $gate = EntryGate::findOrFail($id);
        
        $request->validate([
            "name" => "required|string|max:255",
            "type" => "required|in:entry,exit,both",
            "location" => "nullable|string|max:255",
            "device_id" => "nullable|string|max:255",
            "is_active" => "boolean",
            "status" => "nullable|in:online,offline,maintenance",
        ]);

        $gate->update($request->only([
            "name", "type", "location", "device_id", "is_active", "status", "requires_card"
        ]));

        return response()->json([
            "success" => true,
            "message" => "Gate updated successfully",
            "gate" => $gate->fresh()
        ]);
    }

    public function destroy($id)
    {
        $gate = EntryGate::findOrFail($id);
        $gate->delete();

        return response()->json([
            "success" => true,
            "message" => "Gate deleted successfully"
        ]);
    }

    public function toggleStatus($id)
    {
        $gate = EntryGate::findOrFail($id);
        $gate->is_active = !$gate->is_active;
        $gate->save();

        return response()->json([
            "success" => true,
            "message" => "Gate status updated",
            "gate" => $gate
        ]);
    }

    /**
     * Update global system mode (Open All, Lock All, Emergency)
     */
    public function updateGlobalMode(Request $request)
    {
        $request->validate([
            "mode" => "required|in:normal,open,locked,emergency",
            "duration" => "nullable|integer|min:1", // minutes
        ]);

        $config = AccessControlConfig::getConfig();
        $mode = $request->mode;
        $expiresAt = null;

        if ($request->duration) {
            $expiresAt = now()->addMinutes($request->duration);
        }

        $config->update([
            "global_mode" => $mode,
            "global_mode_expires_at" => $expiresAt,
        ]);

        $message = "System mode updated to " . strtoupper($mode);
        if ($expiresAt) {
            $message .= " until " . $expiresAt->format("H:i");
        }

        return response()->json([
            "success" => true,
            "message" => $message,
            "mode" => $mode,
            "expires_at" => $expiresAt ? $expiresAt->toIso8601String() : null
        ]);
    }

    public function scanCard(Request $request)
    {
        $request->validate([
            "card_number" => "required|string|min:1",
            "gate_id" => "required|exists:entry_gates,id",
            "access_type" => "required|in:entry,exit",
            "member_id" => "nullable|exists:members,id", // Added for selected member
            "action" => "nullable|in:scan,get_enter,show_balance", // GET ENTER and SHOW BALANCE actions
        ]);

        $gate = EntryGate::findOrFail($request->gate_id);

        // Handle Global Mode Overrides
        $config = AccessControlConfig::getConfig();
        
        // Check for expiration
        if ($config->global_mode !== "normal" && $config->global_mode_expires_at && $config->global_mode_expires_at->isPast()) {
            $config->update(["global_mode" => "normal", "global_mode_expires_at" => null]);
            $config = $config->fresh();
        }

        if ($config->global_mode === "emergency") {
            return response()->json([
                "success" => true,
                "status" => "success",
                "message" => "EMERGENCY OVERRIDE: Gate Open",
                "member_name" => "EMERGENCY EVACUATION",
            ]);
        }
        
        if ($config->global_mode === "locked") {
            return response()->json([
                "success" => false,
                "status" => "denied",
                "message" => "SYSTEM LOCKDOWN: All gates are locked by administration",
                "denial_reason" => "System Lockdown"
            ], 403);
        }
        
        if ($config->global_mode === "open") {
            return response()->json([
                "success" => true,
                "status" => "success",
                "message" => "ADMIN OVERRIDE: Gate Open",
                "member_name" => "GUEST / ADMIN ACCESS",
            ]);
        }
        
        if (!$gate->is_active) {
            return response()->json([
                "success" => false,
                "status" => "denied",
                "message" => "Gate is currently disabled",
                "denial_reason" => "Gate disabled"
            ], 400);
        }

        $searchTerm = $request->card_number;
        
        // Find member - if search term is numeric and looks like an ID, try ID first
        $member = null;
        if (is_numeric($searchTerm) && strlen($searchTerm) < 10) {
            // Could be a member ID
            $member = Member::where("member_id", $searchTerm)->first();
        }
        
        // If not found, search by card number, member_id, or name
        if (!$member) {
            $member = Member::where("card_number", $searchTerm)
                ->orWhere("member_id", $searchTerm)
                ->orWhere("name", "like", "%" . $searchTerm . "%")
                ->first();
        }
        
        // Get the actual card number from member if found
        $cardNumber = $member ? $member->card_number : $searchTerm;
        $memberName = $member ? $member->name : "Guest/Unknown";
        $memberBalance = $member ? $member->balance : null;
        
        // Determine access status
        $status = "success";
        $denialReason = null;
        
        if ($member) {
            // Check member status
            if ($member->status !== "active") {
                $status = "denied";
                $denialReason = "Member account is " . $member->status;
            }
            
            // Check balance before allowing entry (only for entry, not exit)
            if ($status === "success" && $request->access_type === "entry") {
                // Minimum balance required for entry (can be configured, default: 0)
                $minimumBalance = config("golf.minimum_entry_balance", 0);
                
                if ($member->balance < $minimumBalance) {
                    $status = "denied";
                    $denialReason = "Insufficient balance. Current balance: TZS " . number_format($member->balance) . ". Minimum required: TZS " . number_format($minimumBalance) . ". Please top up your account.";
                }
            }

            // Check card access requirement for this gate
            if ($status === "success" && $gate->requires_card && !$member->has_full_access) {
                $status = "denied";
                $denialReason = "Access restricted. You are registered as a Golf-Only member. This gate is reserved for Full Access cardholders.";
            }
        } else {
            // No member found - deny access
            $status = "denied";
            $denialReason = "Member not found. Please register or use a valid card number, member ID, or name.";
        }

        // Create access log
        $accessLog = AccessLog::create([
            "gate_id" => $gate->id,
            "member_id" => $member?->id,
            "card_number" => $cardNumber,
            "member_name" => $memberName,
            "access_type" => $request->access_type,
            "status" => $status,
            "denial_reason" => $denialReason,
            "member_balance" => $memberBalance,
            "ip_address" => $request->ip(),
            "user_agent" => $request->userAgent(),
        ]);

        // Send SMS notification for successful entry or exit
        $smsSent = false;
        if ($status === "success" && $member) {
            try {
                $smsService = new SmsService();
                $gateName = $gate->name;
                
                if ($request->access_type === "entry") {
                    $message = "Welcome {$member->name}! You have entered {$gateName} at " . now()->format("d M Y H:i") . ". Enjoy your visit!";
                } else {
                    // Exit
                    $message = "Thank you {$member->name}! You have exited {$gateName} at " . now()->format("d M Y H:i") . ". Have a great day!";
                }
                
                $smsResult = $smsService->send($member->phone, $message);
                $smsSent = $smsResult["success"] ?? false;
            } catch (\Exception $e) {
                \Log::error("Failed to send SMS notification", [
                    "member_id" => $member->id,
                    "access_type" => $request->access_type,
                    "error" => $e->getMessage()
                ]);
            }
        }

        // Handle special actions (GET ENTER, SHOW BALANCE)
        if ($request->has("action")) {
            if ($request->action === "get_enter" || $request->action === "show_balance") {
                if (!$member) {
                    return response()->json([
                        "success" => false,
                        "message" => "Member not found"
                    ], 404);
                }
                
                return response()->json([
                    "success" => true,
                    "member" => [
                        "id" => $member->id,
                        "name" => $member->name,
                        "card_number" => $member->card_number,
                        "member_id" => $member->member_id,
                        "membership_type" => $member->membership_type,
                        "card_color" => $member->card_color ?? "standard",
                        "balance" => $member->balance,
                        "status" => $member->status,
                        "phone" => $member->phone,
                        "email" => $member->email,
                        "valid_until" => $member->valid_until ? $member->valid_until->format("Y-m-d") : null,
                    ],
                    "message" => $request->action === "show_balance" 
                        ? "Balance retrieved successfully" 
                        : "Member information retrieved successfully"
                ]);
            }
        }

        return response()->json([
            "success" => true,
            "status" => $status,
            "message" => $status === "success" 
                ? ucfirst($request->access_type) . " granted" 
                : "Access denied: " . $denialReason,
            "member_name" => $memberName,
            "member_balance" => $memberBalance,
            "card_number" => $cardNumber,
            "member" => $member ? [
                "id" => $member->id,
                "name" => $member->name,
                "card_number" => $member->card_number,
                "member_id" => $member->member_id,
                "membership_type" => $member->membership_type,
                "card_color" => $member->card_color ?? "standard",
                "balance" => $member->balance,
                "status" => $member->status,
                "phone" => $member->phone,
                "email" => $member->email,
            ] : null,
            "sms_sent" => $smsSent,
            "access_log" => $accessLog->load("gate")
        ]);
    }

    public function searchMembers(Request $request)
    {
        $query = $request->get("q", "");
        
        if (strlen($query) < 1) {
            return response()->json([]);
        }
        
        $members = Member::where("name", "like", "%" . $query . "%")
            ->orWhere("card_number", "like", "%" . $query . "%")
            ->orWhere("member_id", "like", "%" . $query . "%")
            ->limit(10)
            ->get(["id", "name", "card_number", "member_id", "phone", "status"]);
        
        return response()->json($members);
    }

    public function getLogs(Request $request)
    {
        $query = AccessLog::with(["gate", "member"])
            ->orderBy("created_at", "desc");

        // Filters
        if ($request->gate_id) {
            $query->where("gate_id", $request->gate_id);
        }
        if ($request->access_type) {
            $query->where("access_type", $request->access_type);
        }
        if ($request->status) {
            $query->where("status", $request->status);
        }
        if ($request->date_from) {
            $query->whereDate("created_at", ">=", $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate("created_at", "<=", $request->date_to);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where("card_number", "like", "%" . $request->search . "%")
                  ->orWhere("member_name", "like", "%" . $request->search . "%");
            });
        }

        $logs = $query->paginate($request->per_page ?? 50);

        return response()->json([
            "success" => true,
            "logs" => $logs
        ]);
    }

    public function exportLogs(Request $request)
    {
        $query = AccessLog::with(["gate", "member"])
            ->orderBy("created_at", "desc");

        // Apply same filters as getLogs
        if ($request->gate_id) {
            $query->where("gate_id", $request->gate_id);
        }
        if ($request->access_type) {
            $query->where("access_type", $request->access_type);
        }
        if ($request->status) {
            $query->where("status", $request->status);
        }
        if ($request->date_from) {
            $query->whereDate("created_at", ">=", $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate("created_at", "<=", $request->date_to);
        }

        $logs = $query->get();

        $filename = "access_logs_" . date("Y-m-d") . ".csv";
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"" . $filename . "\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen("php://output", "w");
            
            // CSV Headers
            fputcsv($file, [
                "Time", "Member Name", "Card Number", "Gate", "Type", "Status", 
                "Denial Reason", "Member Balance", "Notes"
            ]);

            // CSV Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format("Y-m-d H:i:s"),
                    $log->member_name,
                    $log->card_number,
                    $log->gate->name ?? "-",
                    ucfirst($log->access_type),
                    ucfirst($log->status),
                    $log->denial_reason ?? "-",
                    $log->member_balance ?? "-",
                    $log->notes ?? "-"
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateCurrentlyInside()
    {
        // Get all successful entries today
        $entries = AccessLog::today()
            ->entries()
            ->successful()
            ->select("member_id", "card_number")
            ->get();

        // Get all successful exits today
        $exits = AccessLog::today()
            ->exits()
            ->successful()
            ->select("member_id", "card_number")
            ->get();

        // Count unique entries
        $entryCount = $entries->count();
        $exitCount = $exits->count();

        return max(0, $entryCount - $exitCount);
    }
}
