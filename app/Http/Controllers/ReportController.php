<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\DrivingRangeSession;
use App\Models\EquipmentRental;
use App\Models\EquipmentSale;
use App\Models\Order;
use App\Models\Member;
use App\Models\Topup;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Today's Summary
        $todaySummary = [
            'driving_range' => DrivingRangeSession::today()->completed()->sum('amount'),
            'equipment_rental' => EquipmentRental::today()->where('status', 'returned')->sum('total_amount'),
            'equipment_sales' => EquipmentSale::today()->completed()->sum('total_amount'),
            'food_beverage' => Order::today()->completed()->sum('total_amount'),
            'topups' => Topup::today()->sum('amount'),
            'transactions' => Transaction::today()->where('type', 'payment')->sum('amount'),
        ];
        $todaySummary['total'] = array_sum($todaySummary) - $todaySummary['topups'];

        // Monthly Summary
        $monthlySummary = [
            'driving_range' => DrivingRangeSession::whereBetween('start_time', [$startOfMonth, $endOfMonth])->completed()->sum('amount'),
            'equipment_rental' => EquipmentRental::whereBetween('start_time', [$startOfMonth, $endOfMonth])->where('status', 'returned')->sum('total_amount'),
            'equipment_sales' => EquipmentSale::whereBetween('created_at', [$startOfMonth, $endOfMonth])->completed()->sum('total_amount'),
            'food_beverage' => Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->completed()->sum('total_amount'),
            'topups' => Topup::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount'),
        ];
        $monthlySummary['total'] = array_sum($monthlySummary) - $monthlySummary['topups'];

        // Member Stats
        $memberStats = [
            'total_members' => Member::count(),
            'active_members' => Member::active()->count(),
            'total_balance' => Member::sum('balance'),
            'new_this_month' => Member::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
        ];

        return view('reports.index', compact('todaySummary', 'monthlySummary', 'memberStats'));
    }

    public function revenue(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        // Revenue by category
        $revenueByCategory = [
            'driving_range' => DrivingRangeSession::whereBetween('start_time', [$fromDate, $toDate])->where('status', 'completed')->sum('amount'),
            'ball_management' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('category', 'ball_management')->where('type', 'payment')->sum('amount'),
            'equipment_rental' => EquipmentRental::whereBetween('start_time', [$fromDate, $toDate])->where('status', 'returned')->sum('total_amount'),
            'equipment_sales' => EquipmentSale::whereBetween('created_at', [$fromDate, $toDate])->where('status', 'completed')->sum('total_amount'),
            'food_beverage' => Order::whereBetween('created_at', [$fromDate, $toDate])->where('status', 'completed')->sum('total_amount'),
        ];
        $revenueByCategory['total'] = array_sum($revenueByCategory);

        // Payment method breakdown
        $paymentMethodBreakdown = [
            'balance' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->where('payment_method', 'balance')->sum('amount'),
            'cash' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->where('payment_method', 'cash')->sum('amount'),
            'card' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->where('payment_method', 'card')->sum('amount'),
            'mobile' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->where('payment_method', 'mobile')->sum('amount'),
        ];

        // Daily revenue trend (last 30 days)
        $dailyTrend = [];
        $currentDate = $fromDate->copy();
        while ($currentDate <= $toDate && count($dailyTrend) < 30) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();
            
            $drivingRange = (float) DrivingRangeSession::whereBetween('start_time', [$dayStart, $dayEnd])->where('status', 'completed')->sum('amount');
            $equipmentRental = (float) EquipmentRental::whereBetween('start_time', [$dayStart, $dayEnd])->where('status', 'returned')->sum('total_amount');
            $equipmentSales = (float) EquipmentSale::whereBetween('created_at', [$dayStart, $dayEnd])->where('status', 'completed')->sum('total_amount');
            $foodBeverage = (float) Order::whereBetween('created_at', [$dayStart, $dayEnd])->where('status', 'completed')->sum('total_amount');
            
            $dailyTrend[$dateStr] = [
                'date' => $dateStr,
                'driving_range' => $drivingRange,
                'equipment_rental' => $equipmentRental,
                'equipment_sales' => $equipmentSales,
                'food_beverage' => $foodBeverage,
                'total' => $drivingRange + $equipmentRental + $equipmentSales + $foodBeverage,
            ];
            $currentDate->addDay();
        }

        // Top revenue generating transactions
        $topTransactions = Transaction::whereBetween('created_at', [$fromDate, $toDate])
            ->where('type', 'payment')
            ->where('status', 'completed')
            ->orderBy('amount', 'desc')
            ->limit(20)
            ->with('member')
            ->get();

        return view('reports.revenue-reports', compact(
            'revenueByCategory', 
            'paymentMethodBreakdown',
            'dailyTrend',
            'topTransactions',
            'fromDate', 
            'toDate'
        ));
    }

    public function members(Request $request)
    {
        $query = Member::withCount(['transactions', 'topups'])
            ->withSum('topups', 'amount')
            ->withSum('transactions', 'amount');

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->membership_type) {
            $query->where('membership_type', $request->membership_type);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('card_number', 'like', '%' . $request->search . '%')
                  ->orWhere('member_id', 'like', '%' . $request->search . '%');
            });
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(50);

        $stats = [
            'total' => Member::count(),
            'active' => Member::active()->count(),
            'expired' => Member::where('status', 'expired')->count(),
            'suspended' => Member::where('status', 'suspended')->count(),
            'total_balance' => Member::sum('balance'),
            'average_balance' => Member::avg('balance'),
            'total_topups' => Topup::sum('amount'),
            'total_transactions' => Transaction::where('type', 'payment')->sum('amount'),
            'membership_types' => Member::selectRaw('membership_type, count(*) as count')
                ->groupBy('membership_type')
                ->pluck('count', 'membership_type'),
        ];

        // Top members by balance
        $topMembersByBalance = Member::orderBy('balance', 'desc')->limit(10)->get();

        // Top members by transactions
        $topMembersByTransactions = Member::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->limit(10)
            ->get();

        return view('reports.members', compact('members', 'stats', 'topMembersByBalance', 'topMembersByTransactions'));
    }

    public function transactions(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->subDays(30)->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Transaction::with('member')
            ->whereBetween('created_at', [$fromDate, $toDate]);

        // Filters
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(100);

        $summary = [
            'total_transactions' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->count(),
            'total_payments' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->sum('amount'),
            'total_topups' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'topup')->sum('amount'),
            'total_refunds' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'refund')->sum('amount'),
            'by_category' => Transaction::whereBetween('created_at', [$fromDate, $toDate])
                ->where('type', 'payment')
                ->selectRaw('category, sum(amount) as total, count(*) as count')
                ->groupBy('category')
                ->get(),
            'by_payment_method' => Transaction::whereBetween('created_at', [$fromDate, $toDate])
                ->where('type', 'payment')
                ->selectRaw('payment_method, sum(amount) as total, count(*) as count')
                ->groupBy('payment_method')
                ->get(),
        ];

        return view('reports.transactions', compact('transactions', 'summary', 'fromDate', 'toDate'));
    }

    public function dailySummary(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $dateStart = $date->copy()->startOfDay();
        $dateEnd = $date->copy()->endOfDay();

        $summary = [
            'driving_range' => [
                'sessions' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->count(),
                'completed' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'completed')->count(),
                'active' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'active')->count(),
                'revenue' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'completed')->sum('amount'),
                'average_session' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'completed')->avg('amount'),
            ],
            'ball_management' => [
                'transactions' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('category', 'ball_management')->count(),
                'revenue' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('category', 'ball_management')->where('type', 'payment')->sum('amount'),
            ],
            'equipment_rental' => [
                'rentals' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->count(),
                'returned' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'returned')->count(),
                'active' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'active')->count(),
                'revenue' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'returned')->sum('total_amount'),
                'average_rental' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'returned')->avg('total_amount'),
            ],
            'equipment_sales' => [
                'sales' => EquipmentSale::whereBetween('created_at', [$dateStart, $dateEnd])->count(),
                'completed' => EquipmentSale::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->count(),
                'revenue' => EquipmentSale::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->sum('total_amount'),
                'average_sale' => EquipmentSale::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->avg('total_amount'),
            ],
            'food_beverage' => [
                'orders' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->count(),
                'completed' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->count(),
                'pending' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'pending')->count(),
                'revenue' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->sum('total_amount'),
                'average_order' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->avg('total_amount'),
            ],
            'topups' => [
                'count' => Topup::whereBetween('created_at', [$dateStart, $dateEnd])->count(),
                'amount' => Topup::whereBetween('created_at', [$dateStart, $dateEnd])->sum('amount'),
                'average' => Topup::whereBetween('created_at', [$dateStart, $dateEnd])->avg('amount'),
            ],
            'transactions' => [
                'total' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->count(),
                'payments' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('type', 'payment')->count(),
                'topups_count' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('type', 'topup')->count(),
                'refunds' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('type', 'refund')->count(),
            ],
        ];

        $summary['total_revenue'] = $summary['driving_range']['revenue'] + 
                                    $summary['ball_management']['revenue'] +
                                    $summary['equipment_rental']['revenue'] + 
                                    $summary['equipment_sales']['revenue'] + 
                                    $summary['food_beverage']['revenue'];

        return view('reports.daily-summary', compact('summary', 'date'));
    }

    public function revenuePdf(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        // Revenue by category
        $revenueByCategory = [
            'driving_range' => DrivingRangeSession::whereBetween('start_time', [$fromDate, $toDate])->where('status', 'completed')->sum('amount'),
            'ball_management' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('category', 'ball_management')->where('type', 'payment')->sum('amount'),
            'equipment_rental' => EquipmentRental::whereBetween('start_time', [$fromDate, $toDate])->where('status', 'returned')->sum('total_amount'),
            'equipment_sales' => EquipmentSale::whereBetween('created_at', [$fromDate, $toDate])->where('status', 'completed')->sum('total_amount'),
            'food_beverage' => Order::whereBetween('created_at', [$fromDate, $toDate])->where('status', 'completed')->sum('total_amount'),
        ];
        $revenueByCategory['total'] = array_sum($revenueByCategory);

        // Payment method breakdown
        $paymentMethodBreakdown = [
            'balance' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->where('payment_method', 'balance')->sum('amount'),
            'cash' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->where('payment_method', 'cash')->sum('amount'),
            'card' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->where('payment_method', 'card')->sum('amount'),
            'mobile' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->where('payment_method', 'mobile')->sum('amount'),
        ];

        return view('reports.pdf.revenue-pdf', compact('revenueByCategory', 'paymentMethodBreakdown', 'fromDate', 'toDate'));
    }

    public function transactionsPdf(Request $request)
    {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : Carbon::today()->subDays(30)->startOfDay();
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : Carbon::today()->endOfDay();

        $query = Transaction::with('member')
            ->whereBetween('created_at', [$fromDate, $toDate]);

        // Filters
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total_transactions' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->count(),
            'total_payments' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'payment')->sum('amount'),
            'total_topups' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'topup')->sum('amount'),
            'total_refunds' => Transaction::whereBetween('created_at', [$fromDate, $toDate])->where('type', 'refund')->sum('amount'),
        ];

        return view('reports.pdf.transactions-pdf', compact('transactions', 'summary', 'fromDate', 'toDate'));
    }

    public function membersPdf(Request $request)
    {
        $query = Member::withCount(['transactions', 'topups'])
            ->withSum('topups', 'amount')
            ->withSum('transactions', 'amount');

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->membership_type) {
            $query->where('membership_type', $request->membership_type);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('card_number', 'like', '%' . $request->search . '%')
                  ->orWhere('member_id', 'like', '%' . $request->search . '%');
            });
        }

        $members = $query->orderBy('created_at', 'desc')->get();

        $stats = [
            'total' => Member::count(),
            'active' => Member::active()->count(),
            'expired' => Member::where('status', 'expired')->count(),
            'suspended' => Member::where('status', 'suspended')->count(),
            'total_balance' => Member::sum('balance'),
        ];

        return view('reports.pdf.members-pdf', compact('members', 'stats'));
    }

    public function dailySummaryPdf(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $dateStart = $date->copy()->startOfDay();
        $dateEnd = $date->copy()->endOfDay();

        $summary = [
            'driving_range' => [
                'sessions' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->count(),
                'completed' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'completed')->count(),
                'active' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'active')->count(),
                'revenue' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'completed')->sum('amount'),
                'average_session' => DrivingRangeSession::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'completed')->avg('amount'),
            ],
            'ball_management' => [
                'transactions' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('category', 'ball_management')->count(),
                'revenue' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('category', 'ball_management')->where('type', 'payment')->sum('amount'),
            ],
            'equipment_rental' => [
                'rentals' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->count(),
                'returned' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'returned')->count(),
                'active' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'active')->count(),
                'revenue' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'returned')->sum('total_amount'),
                'average_rental' => EquipmentRental::whereBetween('start_time', [$dateStart, $dateEnd])->where('status', 'returned')->avg('total_amount'),
            ],
            'equipment_sales' => [
                'sales' => EquipmentSale::whereBetween('created_at', [$dateStart, $dateEnd])->count(),
                'completed' => EquipmentSale::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->count(),
                'revenue' => EquipmentSale::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->sum('total_amount'),
                'average_sale' => EquipmentSale::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->avg('total_amount'),
            ],
            'food_beverage' => [
                'orders' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->count(),
                'completed' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->count(),
                'pending' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'pending')->count(),
                'revenue' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->sum('total_amount'),
                'average_order' => Order::whereBetween('created_at', [$dateStart, $dateEnd])->where('status', 'completed')->avg('total_amount'),
            ],
            'topups' => [
                'count' => Topup::whereBetween('created_at', [$dateStart, $dateEnd])->count(),
                'amount' => Topup::whereBetween('created_at', [$dateStart, $dateEnd])->sum('amount'),
                'average' => Topup::whereBetween('created_at', [$dateStart, $dateEnd])->avg('amount'),
            ],
            'transactions' => [
                'total' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->count(),
                'payments' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('type', 'payment')->count(),
                'topups_count' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('type', 'topup')->count(),
                'refunds' => Transaction::whereBetween('created_at', [$dateStart, $dateEnd])->where('type', 'refund')->count(),
            ],
        ];

        $summary['total_revenue'] = $summary['driving_range']['revenue'] + 
                                    $summary['ball_management']['revenue'] +
                                    $summary['equipment_rental']['revenue'] + 
                                    $summary['equipment_sales']['revenue'] + 
                                    $summary['food_beverage']['revenue'];

        return view('reports.pdf.daily-summary-pdf', compact('summary', 'date'));
    }
}


