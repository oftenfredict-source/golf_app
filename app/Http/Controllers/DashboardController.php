<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\Topup;
use App\Models\DrivingRangeSession;
use App\Models\EquipmentRental;
use App\Models\EquipmentSale;
use App\Models\Order;
use App\Models\BallTransaction;
use App\Models\AccessLog;
use Illuminate\Http\Request;
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
            'food_beverage' => Order::whereDate('created_at', $today)->where('status', 'completed')->sum('total_amount'),
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
        ];
        
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
            'memberStats'
        ));
    }
    
    private function getTodayRevenue()
    {
        $today = Carbon::today();
        return DrivingRangeSession::whereDate('start_time', $today)->where('status', 'completed')->sum('amount') +
               EquipmentRental::whereDate('start_time', $today)->where('status', 'returned')->sum('total_amount') +
               EquipmentSale::whereDate('created_at', $today)->where('status', 'completed')->sum('total_amount') +
               Order::whereDate('created_at', $today)->where('status', 'completed')->sum('total_amount') +
               Transaction::whereDate('created_at', $today)->where('category', 'ball_management')->where('type', 'payment')->sum('amount');
    }
    
    private function getMonthlyRevenue($startDate)
    {
        return DrivingRangeSession::where('start_time', '>=', $startDate)->where('status', 'completed')->sum('amount') +
               EquipmentRental::where('start_time', '>=', $startDate)->where('status', 'returned')->sum('total_amount') +
               EquipmentSale::where('created_at', '>=', $startDate)->where('status', 'completed')->sum('total_amount') +
               Order::where('created_at', '>=', $startDate)->where('status', 'completed')->sum('total_amount') +
               Transaction::where('created_at', '>=', $startDate)->where('category', 'ball_management')->where('type', 'payment')->sum('amount');
    }
    
    private function getDayRevenue($startDate, $endDate)
    {
        return DrivingRangeSession::whereBetween('start_time', [$startDate, $endDate])->where('status', 'completed')->sum('amount') +
               EquipmentRental::whereBetween('start_time', [$startDate, $endDate])->where('status', 'returned')->sum('total_amount') +
               EquipmentSale::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->sum('total_amount') +
               Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed')->sum('total_amount') +
               Transaction::whereBetween('created_at', [$startDate, $endDate])->where('category', 'ball_management')->where('type', 'payment')->sum('amount');
    }
}
