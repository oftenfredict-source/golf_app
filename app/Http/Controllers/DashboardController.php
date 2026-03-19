<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\Topup;
use App\Models\DrivingRangeSession;
use App\Models\EquipmentRental;
use App\Models\EquipmentSale;
use App\Models\Order;
use App\Models\BallInventory;
use App\Models\BallTransaction;
use App\Models\AccessLog;
use App\Models\BallCollector;
use App\Models\BallCollectionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->role === 'ball_manager') {
            return $this->ballManagerDashboard();
        }

        if ($user && $user->role === 'reception') {
            return $this->receptionDashboard();
        }

        if ($user && $user->role === 'storekeeper') {
            return $this->storekeeperDashboard();
        }

        if ($user && $user->role === 'counter') {
            return $this->counterDashboard();
        }

        if ($user && $user->role === 'chef') {
            return redirect()->route('kitchen.dashboard');
        }

        if ($user && $user->role === 'waiter') {
            return redirect()->route('waiter.dashboard');
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Today's Statistics
        $todayStats = [
            'revenue' => $this->getTodayRevenue(),
            'transactions' => Transaction::whereDate('created_at', $today)->count(),
            'topups' => Topup::whereDate('created_at', $today)->count(),
            'topup_amount' => Topup::whereDate('created_at', $today)->sum('amount'),
            'members' => Member::whereDate('created_at', $today)->count(),
            'active_sessions' => DrivingRangeSession::where('status', 'active')->count(),
            'access_entries' => AccessLog::whereDate('created_at', $today)->where('access_type', 'entry')->where('status', 'success')->count(),
        ];

        // Monthly Statistics
        $monthlyStats = [
            'revenue' => $this->getMonthlyRevenue($thisMonth),
            'transactions' => Transaction::where('created_at', '>=', $thisMonth)->count(),
            'topups' => Topup::where('created_at', '>=', $thisMonth)->count(),
            'topup_amount' => Topup::where('created_at', '>=', $thisMonth)->sum('amount'),
            'members' => Member::where('created_at', '>=', $thisMonth)->count(),
        ];

        // Service-wise Revenue Today
        $serviceRevenue = [
            'driving_range' => DrivingRangeSession::whereDate('start_time', $today)->where('status', 'completed')->sum('amount'),
            'equipment_rental' => EquipmentRental::whereDate('start_time', $today)->where('status', 'returned')->sum('total_amount'),
            'equipment_sales' => EquipmentSale::whereDate('created_at', $today)->where('status', 'completed')->sum('total_amount'),
            'food_beverage' => Transaction::whereDate('created_at', $today)->where('type', 'payment')->where('category', 'food_beverage')->sum('amount'),
            'ball_management' => Transaction::whereDate('created_at', $today)->where('category', 'ball_management')->where('type', 'payment')->sum('amount'),
        ];

        // Recent Transactions
        $recentTransactions = Transaction::with('member')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent Top-ups
        $recentTopups = Topup::with('member')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Active Sessions
        $activeSessions = DrivingRangeSession::with('member')
            ->where('status', 'active')
            ->orderBy('start_time', 'desc')
            ->get();

        // Active Rentals
        $activeRentals = EquipmentRental::with(['equipment', 'member'])
            ->where('status', 'active')
            ->orderBy('start_time', 'desc')
            ->get();

        // Recent Access Logs
        $recentAccessLogs = AccessLog::with(['gate', 'member'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Revenue Trend (Last 7 Days)
        $revenueTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStart = $date->copy()->startOfDay();
            $dateEnd = $date->copy()->endOfDay();

            $revenueTrend[] = [
                'date' => $date->format('M d'),
                'revenue' => $this->getDayRevenue($dateStart, $dateEnd),
            ];
        }

        // Payment Method Breakdown Today
        $paymentMethods = [
            'balance' => Transaction::whereDate('created_at', $today)->where('type', 'payment')->where('payment_method', 'balance')->sum('amount'),
            'cash' => Transaction::whereDate('created_at', $today)->where('type', 'payment')->where('payment_method', 'cash')->sum('amount') +
            DrivingRangeSession::whereDate('start_time', $today)->where('payment_method', 'cash')->sum('amount') +
            EquipmentSale::whereDate('created_at', $today)->where('payment_method', 'cash')->sum('total_amount'),
            'card' => Transaction::whereDate('created_at', $today)->where('type', 'payment')->where('payment_method', 'card')->sum('amount') +
            DrivingRangeSession::whereDate('start_time', $today)->where('payment_method', 'card')->sum('amount') +
            EquipmentSale::whereDate('created_at', $today)->where('payment_method', 'card')->sum('total_amount'),
            'mobile' => Transaction::whereDate('created_at', $today)->where('type', 'payment')->where('payment_method', 'mobile')->sum('amount') +
            Topup::whereDate('created_at', $today)->where('payment_method', 'mobile')->sum('amount') +
            DrivingRangeSession::whereDate('start_time', $today)->where('payment_method', 'mobile')->sum('amount') +
            EquipmentSale::whereDate('created_at', $today)->where('payment_method', 'mobile')->sum('total_amount'),
        ];

        // Member Statistics
        $memberStats = [
            'total' => Member::count(),
            'active' => Member::active()->count(),
            'total_balance' => Member::sum('balance'),
            'average_balance' => Member::avg('balance'),
            'cards_pending' => Member::where('has_full_access', true)
                ->where('card_status', '!=', Member::CARD_STATUS_ISSUED)
                ->count(),
        ];

        // Members in the card issuance workflow
        $pendingCards = Member::where('has_full_access', true)
            ->where('card_status', '!=', Member::CARD_STATUS_ISSUED)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'todayStats',
            'monthlyStats',
            'serviceRevenue',
            'recentTransactions',
            'recentTopups',
            'activeSessions',
            'activeRentals',
            'recentAccessLogs',
            'revenueTrend',
            'paymentMethods',
            'memberStats',
            'pendingCards'
        ));
    }

    private function getTodayRevenue()
    {
        $today = Carbon::today();
        return DrivingRangeSession::whereDate('start_time', $today)->where('status', 'completed')->sum('amount') +
            EquipmentRental::whereDate('start_time', $today)->where('status', 'returned')->sum('total_amount') +
            EquipmentSale::whereDate('created_at', $today)->where('status', 'completed')->sum('total_amount') +
            Transaction::whereDate('created_at', $today)->where('type', 'payment')->where('category', 'food_beverage')->sum('amount') +
            Transaction::whereDate('created_at', $today)->where('category', 'ball_management')->where('type', 'payment')->sum('amount');
    }

    private function getMonthlyRevenue($startDate)
    {
        return DrivingRangeSession::where('start_time', '>=', $startDate)->where('status', 'completed')->sum('amount') +
            EquipmentRental::where('start_time', '>=', $startDate)->where('status', 'returned')->sum('total_amount') +
            EquipmentSale::where('created_at', '>=', $startDate)->where('status', 'completed')->sum('total_amount') +
            Transaction::where('created_at', '>=', $startDate)->where('type', 'payment')->where('category', 'food_beverage')->sum('amount') +
            Transaction::where('created_at', '>=', $startDate)->where('category', 'ball_management')->where('type', 'payment')->sum('amount');
    }

    private function getDayRevenue($startDate, $endDate)
    {
        return DrivingRangeSession::whereBetween('start_time', [$startDate, $endDate])->where('status', 'completed')->sum('amount') +
            EquipmentRental::whereBetween('start_time', [$startDate, $endDate])->where('status', 'returned')->sum('total_amount') +
            EquipmentSale::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->sum('total_amount') +
            Transaction::whereBetween('created_at', [$startDate, $endDate])->where('type', 'payment')->where('category', 'food_beverage')->sum('amount') +
            Transaction::whereBetween('created_at', [$startDate, $endDate])->where('category', 'ball_management')->where('type', 'payment')->sum('amount');
    }

    private function ballManagerDashboard()
    {
        $inventory = BallInventory::first() ?? BallInventory::create([
            'ball_type' => 'standard',
            'total_quantity' => 5000,
            'available_quantity' => 5000,
            'in_use' => 0,
            'damaged' => 0,
            'cost_per_ball' => 500
        ]);

        $todayTransactions = BallTransaction::today()->orderBy('created_at', 'desc')->limit(10)->get();

        $stats = [
            'total' => $inventory->total_quantity,
            'available' => $inventory->available_quantity,
            'in_use' => $inventory->in_use,
            'damaged' => $inventory->damaged,
            'issued_today' => BallTransaction::today()->where('type', 'issued')->sum('quantity'),
            'returned_today' => BallTransaction::today()->where('type', 'returned')->sum('quantity'),
            'revenue_today' => Transaction::whereDate('created_at', Carbon::today())->where('category', 'ball_management')->where('type', 'payment')->sum('amount')
        ];

        return view('dashboard.ball_manager', compact('inventory', 'todayTransactions', 'stats'));
    }

    private function receptionDashboard()
    {
        $today = Carbon::today();
        
        $stats = [
            'members_today' => Member::whereDate('created_at', $today)->count(),
            'topups_today' => Topup::whereDate('created_at', $today)->count(),
            'topup_amount_today' => Topup::whereDate('created_at', $today)->sum('amount'),
            'cards_pending' => Member::where('has_full_access', true)
                ->where('card_status', '!=', Member::CARD_STATUS_ISSUED)
                ->count(),
        ];

        $recentMembers = Member::orderBy('created_at', 'desc')->limit(5)->get();
        $recentTopups = Topup::with('member')->orderBy('created_at', 'desc')->limit(5)->get();
        
        // Members in the card issuance workflow (any status except Issued)
        $pendingCards = Member::where('has_full_access', true)
            ->where('card_status', '!=', Member::CARD_STATUS_ISSUED)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.reception', compact('stats', 'recentMembers', 'recentTopups', 'pendingCards'));
    }

    private function storekeeperDashboard()
    {
        $today = Carbon::today();

        // Ball Stats
        $inventory = BallInventory::first();
        $ballStats = [
            'total' => $inventory->total_quantity ?? 0,
            'available' => $inventory->available_quantity ?? 0,
            'in_use' => $inventory->in_use ?? 0,
            'damaged' => $inventory->damaged ?? 0,
            'collected_today' => BallCollectionLog::whereDate('created_at', $today)->sum('quantity_collected'),
        ];

        // Equipment Stats
        $equipmentStats = [
            'active_rentals' => EquipmentRental::where('status', 'active')->count(),
            'sales_today' => EquipmentSale::whereDate('created_at', $today)->sum('total_amount'),
        ];

        // Recent Ball Transactions
        $recentBallTransactions = BallTransaction::orderBy('created_at', 'desc')->limit(5)->get();

        // Recent Collections
        $recentCollections = BallCollectionLog::with('collector', 'assigner')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Active Collectors
        $collectors = BallCollector::where('status', 'active')->get();

        return view('dashboard.storekeeper', compact(
            'ballStats', 
            'equipmentStats', 
            'recentBallTransactions', 
            'recentCollections', 
            'collectors'
        ));
    }

    private function counterDashboard()
    {
        $user = Auth::user();
        $counter = \App\Models\Counter::where('assigned_user_id', $user->id)->first();
        
        $stats = [
            'orders_today' => $counter ? Order::where('counter_id', $counter->id)->today()->count() : 0,
            'revenue_today' => $counter ? Transaction::today()->where('type', 'payment')->where('category', 'food_beverage')->where('reference_type', 'order')->whereIn('reference_id', Order::where('counter_id', $counter->id)->pluck('id'))->sum('amount') : 0,
            'recent_orders' => $counter ? Order::where('counter_id', $counter->id)->orderBy('created_at', 'desc')->limit(5)->get() : collect(),
        ];

        return view('dashboard.counter', compact('counter', 'stats'));
    }
}
