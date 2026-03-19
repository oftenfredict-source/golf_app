@extends('settings._layout-base')

@section('title', 'Dashboard')
@section('description', 'Dashboard - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Home /</span> Dashboard
</h4>

<!-- Welcome Banner & Quick Desk -->
<div class="row mb-6">
  <div class="col-md-9 mb-4">
    <div class="card h-100" style="background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);">
      <div class="card-body text-white py-4 d-flex align-items-center">
        <div>
          <h4 class="text-white mb-1">Welcome, {{ Auth::user()->name ?? 'User' }}</h4>
          <p class="mb-0 opacity-75">Golf Club Management System - Quick Desk</p>
          <div class="mt-4">
            <a href="{{ route('golf-services.driving-range') }}" class="btn btn-warning me-2">
              <i class="ri ri-play-circle-line me-1"></i> Start Range Session
            </a>
            <a href="{{ route('payments.top-ups') }}" class="btn btn-light">
              <i class="ri ri-add-circle-line me-1"></i> Quick Member Top-up
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 mb-4">
    <div class="card h-100 bg-label-primary">
      <div class="card-body d-flex flex-column justify-content-center text-center">
        <h3 class="mb-1 text-primary">{{ $todayStats['access_entries'] ?? 0 }}</h3>
        <p class="mb-0 text-muted">Entries Today</p>
        <div class="mt-3">
          <a href="{{ route('access-control.entry-gates') }}" class="btn btn-primary btn-sm w-100">Access Logs</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Additional Statistics -->
<div class="row mb-4">
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-label-primary rounded">
              <i class="icon-base ri ri-wallet-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Total Member Balance</p>
            <h5 class="mb-0">TZS {{ number_format($memberStats['total_balance'] ?? 0, 2) }}</h5>
            <small class="text-muted">Avg: TZS {{ number_format($memberStats['average_balance'] ?? 0, 2) }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-label-success rounded">
              <i class="icon-base ri ri-coins-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Top-ups Today</p>
            <h5 class="mb-0">TZS {{ number_format($todayStats['topup_amount'] ?? 0, 2) }}</h5>
            <small class="text-muted">{{ $todayStats['topups'] ?? 0 }} transactions</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-label-warning rounded">
              <i class="icon-base ri ri-user-add-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">New Members Today</p>
            <h5 class="mb-0">{{ number_format($todayStats['members'] ?? 0) }}</h5>
            <small class="text-muted">{{ $monthlyStats['members'] ?? 0 }} this month</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-label-info rounded">
              <i class="icon-base ri ri-calendar-check-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Monthly Revenue</p>
            <h5 class="mb-0">TZS {{ number_format($monthlyStats['revenue'] ?? 0, 2) }}</h5>
            <small class="text-muted">{{ $monthlyStats['transactions'] ?? 0 }} transactions</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-6">
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-primary rounded">
              <i class="icon-base ri ri-user-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Total Members</p>
            <h4 class="mb-0">{{ number_format($memberStats['total'] ?? 0) }}</h4>
            <small class="text-success">{{ $memberStats['active'] ?? 0 }} active</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-success rounded">
              <i class="icon-base ri ri-money-dollar-circle-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Today's Revenue</p>
            <h4 class="mb-0">TZS {{ number_format($todayStats['revenue'] ?? 0) }}</h4>
            <small class="text-muted">This Month: TZS {{ number_format($monthlyStats['revenue'] ?? 0) }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-warning rounded">
              <i class="icon-base ri ri-shopping-cart-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Today's Transactions</p>
            <h4 class="mb-0">{{ number_format($todayStats['transactions'] ?? 0) }}</h4>
            <small class="text-success">{{ $todayStats['topups'] ?? 0 }} top-ups</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-info rounded">
              <i class="icon-base ri ri-golf-ball-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Active Sessions</p>
            <h4 class="mb-0">{{ number_format($todayStats['active_sessions'] ?? 0) }}</h4>
            <small class="text-info">Bays: {{ $todayStats['active_sessions'] ?? 0 }} occupied</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Operations Center -->
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent py-4">
        <h5 class="mb-0 fw-bold"><i class="ri ri-flashlight-line me-2 text-warning"></i>Operations Center</h5>
        <p class="text-muted mb-0">Frequently used daily tasks</p>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-3">
            <a href="{{ route('golf-services.ball-management') }}" class="d-flex align-items-center p-3 border rounded secondary-hover text-decoration-none">
              <div class="avatar avatar-md bg-label-primary me-3">
                <i class="ri ri-golf-ball-line"></i>
              </div>
              <div>
                <h6 class="mb-0">Ball Management</h6>
                <small class="text-muted">Issue & Return</small>
              </div>
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('golf-services.equipment-rental') }}" class="d-flex align-items-center p-3 border rounded secondary-hover text-decoration-none">
              <div class="avatar avatar-md bg-label-success me-3">
                <i class="ri ri-tools-line"></i>
              </div>
              <div>
                <h6 class="mb-0">Equipment Rental</h6>
                <small class="text-muted">Checkout & Return</small>
              </div>
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('golf-services.equipment-sales') }}" class="d-flex align-items-center p-3 border rounded secondary-hover text-decoration-none">
              <div class="avatar avatar-md bg-label-warning me-3">
                <i class="ri ri-shopping-bag-line"></i>
              </div>
              <div>
                <h6 class="mb-0">Pro Shop Sales</h6>
                <small class="text-muted">Quick POS</small>
              </div>
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('payments.upi-management') }}" class="d-flex align-items-center p-3 border rounded secondary-hover text-decoration-none">
              <div class="avatar avatar-md bg-label-info me-3">
                <i class="ri ri-user-line"></i>
              </div>
              <div>
                <h6 class="mb-0">Member Directory</h6>
                <small class="text-muted">Search & Info</small>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .secondary-hover:hover {
    background-color: #f8f9fa;
    border-color: #3949ab !important;
  }
  .bg-label-primary { background-color: #e8eaf6 !important; color: #3949ab !important; }
  .bg-label-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; }
  .bg-label-warning { background-color: #fff3e0 !important; color: #ef6c00 !important; }
  .bg-label-info { background-color: #e1f5fe !important; color: #0288d1 !important; }
</style>

<!-- Recent Activity & Revenue Chart -->
<div class="row mb-6">
  <div class="col-xl-8">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Transactions</h5>
        <a href="{{ route('payments.transactions') }}" class="btn btn-sm btn-label-primary">View All</a>
      </div>
      <div class="card-body">
        <div class="table-responsive text-nowrap">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Transaction ID</th>
                <th>Member</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentTransactions as $txn)
              <tr>
                <td><code>{{ $txn->transaction_id }}</code></td>
                <td>
                  <strong>{{ $txn->customer_name }}</strong>
                  @if($txn->member)
                    <br><small class="text-muted">Card: {{ $txn->member->card_number }}</small>
                  @endif
                </td>
                <td>
                  @if($txn->type === 'payment')
                    <span class="badge bg-label-danger">Payment</span>
                  @elseif($txn->type === 'topup')
                    <span class="badge bg-label-success">Top-up</span>
                  @elseif($txn->type === 'refund')
                    <span class="badge bg-label-warning">Refund</span>
                  @endif
                </td>
                <td>
                  <strong class="{{ $txn->type === 'payment' ? 'text-danger' : ($txn->type === 'topup' ? 'text-success' : 'text-warning') }}">
                    {{ $txn->type === 'payment' ? '-' : '+' }}TZS {{ number_format($txn->amount) }}
                  </strong>
                </td>
                <td>{{ $txn->created_at->format('H:i:s') }}</td>
                <td><span class="badge bg-{{ $txn->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($txn->status) }}</span></td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-body-secondary">No recent transactions</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Revenue Summary</h5>
      </div>
      <div class="card-body">
        @php
          $totalRevenue = $serviceRevenue['driving_range'] + $serviceRevenue['equipment_sales'] + 
                         $serviceRevenue['equipment_rental'] + $serviceRevenue['food_beverage'] + 
                         $serviceRevenue['ball_management'];
          $maxRevenue = $totalRevenue > 0 ? $totalRevenue : 1;
        @endphp
        <div class="mb-4">
          <div class="d-flex justify-content-between mb-2">
            <span>Driving Range</span>
            <strong>TZS {{ number_format($serviceRevenue['driving_range'] ?? 0) }}</strong>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-primary" style="width: {{ ($serviceRevenue['driving_range'] ?? 0) / $maxRevenue * 100 }}%"></div>
          </div>
        </div>
        <div class="mb-4">
          <div class="d-flex justify-content-between mb-2">
            <span>Equipment Sales</span>
            <strong>TZS {{ number_format($serviceRevenue['equipment_sales'] ?? 0) }}</strong>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-success" style="width: {{ ($serviceRevenue['equipment_sales'] ?? 0) / $maxRevenue * 100 }}%"></div>
          </div>
        </div>
        <div class="mb-4">
          <div class="d-flex justify-content-between mb-2">
            <span>Equipment Rental</span>
            <strong>TZS {{ number_format($serviceRevenue['equipment_rental'] ?? 0) }}</strong>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-warning" style="width: {{ ($serviceRevenue['equipment_rental'] ?? 0) / $maxRevenue * 100 }}%"></div>
          </div>
        </div>
        <div class="mb-4">
          <div class="d-flex justify-content-between mb-2">
            <span>Food & Beverage</span>
            <strong>TZS {{ number_format($serviceRevenue['food_beverage'] ?? 0) }}</strong>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-info" style="width: {{ ($serviceRevenue['food_beverage'] ?? 0) / $maxRevenue * 100 }}%"></div>
          </div>
        </div>
        <div class="mb-4">
          <div class="d-flex justify-content-between mb-2">
            <span>Ball Management</span>
            <strong>TZS {{ number_format($serviceRevenue['ball_management'] ?? 0) }}</strong>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-secondary" style="width: {{ ($serviceRevenue['ball_management'] ?? 0) / $maxRevenue * 100 }}%"></div>
          </div>
        </div>
        <div class="border-top pt-3 mt-3">
          <div class="d-flex justify-content-between">
            <strong>Total Today</strong>
            <strong class="text-primary">TZS {{ number_format($totalRevenue) }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Revenue Trend Chart -->
<div class="row mb-6">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-line-chart-line me-2"></i>Revenue Trend (Last 7 Days)</h5>
      </div>
      <div class="card-body">
        <canvas id="revenueChart" height="80"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Payment Methods & Recent Activities -->
<div class="row mb-6">
  <div class="col-xl-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Payment Methods (Today)</h5>
      </div>
      <div class="card-body">
        @php
          $totalPaymentMethod = ($paymentMethods['balance'] ?? 0) + ($paymentMethods['cash'] ?? 0) + 
                               ($paymentMethods['card'] ?? 0) + ($paymentMethods['mobile'] ?? 0);
          $paymentMax = $totalPaymentMethod > 0 ? $totalPaymentMethod : 1;
        @endphp
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span>Member Balance</span>
            <strong>TZS {{ number_format($paymentMethods['balance'] ?? 0) }}</strong>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-primary" style="width: {{ ($paymentMethods['balance'] ?? 0) / $paymentMax * 100 }}%"></div>
          </div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span>Cash</span>
            <strong>TZS {{ number_format($paymentMethods['cash'] ?? 0) }}</strong>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-success" style="width: {{ ($paymentMethods['cash'] ?? 0) / $paymentMax * 100 }}%"></div>
          </div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span>Card</span>
            <strong>TZS {{ number_format($paymentMethods['card'] ?? 0) }}</strong>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-warning" style="width: {{ ($paymentMethods['card'] ?? 0) / $paymentMax * 100 }}%"></div>
          </div>
        </div>
        <div>
          <div class="d-flex justify-content-between mb-1">
            <span>Mobile Money</span>
            <strong>TZS {{ number_format($paymentMethods['mobile'] ?? 0) }}</strong>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-info" style="width: {{ ($paymentMethods['mobile'] ?? 0) / $paymentMax * 100 }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Top-ups</h5>
        <a href="{{ route('payments.top-ups') }}" class="btn btn-sm btn-label-primary">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive text-nowrap" style="max-height: 300px; overflow-y: auto;">
          <table class="table table-sm mb-0">
            <thead class="table-light sticky-top">
              <tr>
                <th>Member</th>
                <th>Amount</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentTopups as $topup)
              <tr>
                <td>
                  <strong>{{ $topup->member->name ?? $topup->customer_name }}</strong>
                  @if($topup->member)
                    <br><small class="text-muted">{{ $topup->member->card_number }}</small>
                  @endif
                </td>
                <td><strong class="text-success">+TZS {{ number_format($topup->amount) }}</strong></td>
                <td>{{ $topup->created_at->format('H:i') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="text-center py-3 text-muted">No recent top-ups</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Access Logs</h5>
        <a href="{{ route('access-control.entry-gates') }}" class="btn btn-sm btn-label-primary">View All</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive text-nowrap" style="max-height: 300px; overflow-y: auto;">
          <table class="table table-sm mb-0">
            <thead class="table-light sticky-top">
              <tr>
                <th>Member</th>
                <th>Gate</th>
                <th>Type</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentAccessLogs as $log)
              <tr>
                <td><strong>{{ $log->member_name }}</strong></td>
                <td>{{ $log->gate->name ?? '-' }}</td>
                <td>
                  <span class="badge bg-label-{{ $log->access_type === 'entry' ? 'success' : 'info' }}">
                    {{ ucfirst($log->access_type) }}
                  </span>
                </td>
                <td>{{ $log->created_at->format('H:i') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-3 text-muted">No recent access logs</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Active Sessions & Alerts -->
<div class="row">
  <div class="col-xl-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Active Golf Sessions</h5>
        <a href="{{ route('golf-services.driving-range') }}" class="btn btn-sm btn-label-primary">View All</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Member</th>
                <th>Location</th>
                <th>Start Time</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody>
              @forelse($activeSessions as $session)
              <tr>
                <td>
                  <strong>{{ $session->customer_name }}</strong>
                  @if($session->member)
                    <br><small class="text-muted">Card: {{ $session->member->card_number }}</small>
                  @endif
                </td>
                <td>Bay {{ $session->bay_number }}</td>
                <td>{{ $session->start_time->format('H:i') }}</td>
                <td>{{ $session->start_time->diffForHumans() }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-3 text-body-secondary">No active sessions</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">System Alerts</h5>
      </div>
      <div class="card-body">
        @if($activeRentals->count() > 0)
        <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
          <i class="icon-base ri ri-alert-line me-2"></i>
          <span><strong>{{ $activeRentals->count() }}</strong> active equipment rental(s)</span>
        </div>
        @else
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
          <i class="icon-base ri ri-checkbox-circle-line me-2"></i>
          <span>All systems operational</span>
        </div>
        @endif
        <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
          <i class="icon-base ri ri-information-line me-2"></i>
          <div>
            <strong>Today's Summary</strong><br>
            <small>Revenue: TZS {{ number_format($todayStats['revenue'] ?? 0) }} | 
            Transactions: {{ $todayStats['transactions'] ?? 0 }} | 
            Entries: {{ $todayStats['access_entries'] ?? 0 }}</small>
          </div>
        </div>
        <div class="alert alert-primary d-flex align-items-center mb-0" role="alert">
          <i class="icon-base ri ri-time-line me-2"></i>
          <span>Last updated: {{ now()->format('d M Y H:i:s') }}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6">
    <div class="card h-100 border-start border-warning border-5 shadow-sm">
      <div class="card-header bg-transparent d-flex justify-content-between align-items-center pb-2">
        <h5 class="mb-0 fw-bold text-warning"><i class="ri-bank-card-line me-2"></i>Card Issuance Queue</h5>
        <a href="{{ route('payments.upi-management') }}" class="btn btn-sm btn-label-warning text-warning">Member Directory</a>
      </div>
      <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @forelse($pendingCards as $member)
            <div class="list-group-item px-4 py-3 border-0 border-bottom">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar bg-label-warning rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold small">{{ $member->name }}</h6>
                        <small class="text-muted" style="font-size: 0.7rem;">Reg: {{ $member->created_at->format('M d, H:i') }}</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-3">
                    <div class="small fw-bold" style="font-size: 0.75rem;">
                        @if($member->card_status == 'pending_design')
                            <span class="text-warning"><i class="ri-time-line me-1"></i> Pending Design</span>
                        @elseif($member->card_status == 'printing')
                            <span class="text-primary"><i class="ri-printer-line me-1"></i> Printing</span>
                        @elseif($member->card_status == 'ready')
                            <span class="text-success"><i class="ri-checkbox-circle-line me-1"></i> Ready</span>
                        @endif
                    </div>
                    <div class="btn-group">
                        @if($member->card_status == 'pending_design')
                            <button class="btn btn-xs btn-primary py-0 px-2" onclick="updateCardStatus('{{ $member->id }}', 'printing')" style="font-size: 0.7rem;">Design Done</button>
                        @elseif($member->card_status == 'printing')
                            <button class="btn btn-xs btn-success py-0 px-2" onclick="updateCardStatus('{{ $member->id }}', 'ready')" style="font-size: 0.7rem;">Mark Ready</button>
                        @elseif($member->card_status == 'ready')
                            <button class="btn btn-xs btn-dark py-0 px-2" onclick="updateCardStatus('{{ $member->id }}', 'issued')" style="font-size: 0.7rem;">Issue Now</button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted small">No cards in the issuance queue.</div>
            @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Active Equipment Rentals -->
@if($activeRentals->count() > 0)
<div class="row mb-6">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Active Equipment Rentals</h5>
        <a href="{{ route('golf-services.equipment-rental') }}" class="btn btn-sm btn-label-primary">Manage</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Customer</th>
                <th>Equipment</th>
                <th>Quantity</th>
                <th>Start Time</th>
                <th>Expected Return</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody>
              @foreach($activeRentals as $rental)
              <tr>
                <td>
                  <strong>{{ $rental->customer_name }}</strong>
                  @if($rental->member)
                    <br><small class="text-muted">Card: {{ $rental->member->card_number }}</small>
                  @endif
                </td>
                <td>{{ $rental->equipment->name ?? '-' }}</td>
                <td>{{ $rental->quantity }}</td>
                <td>{{ $rental->start_time->format('d M Y H:i') }}</td>
                <td>{{ $rental->expected_return->format('d M Y H:i') }}</td>
                <td>{{ $rental->start_time->diffForHumans() }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Revenue Trend Chart
  const revenueCtx = document.getElementById('revenueChart');
  if (revenueCtx) {
    const revenueData = @json($revenueTrend);
    new Chart(revenueCtx, {
      type: 'line',
      data: {
        labels: revenueData.map(d => d.date),
        datasets: [{
          label: 'Revenue (TZS)',
          data: revenueData.map(d => d.revenue),
          borderColor: '#940000',
          backgroundColor: 'rgba(148, 0, 0, 0.1)',
          tension: 0.4,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Revenue: TZS ' + context.parsed.y.toLocaleString();
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'TZS ' + value.toLocaleString();
              }
            }
          }
        }
      }
    });
  }
  
  
  // Card Status Management
  window.updateCardStatus = function(memberId, status) {
      let confirmText = "Transitioning card to " + status.replace('_', ' ') + "...";
      if (status === 'ready') confirmText = "The member will be notified via SMS that their card is ready for pickup.";
      if (status === 'issued') confirmText = "Confirm that the member has successfully collected their card.";

      Swal.fire({
          title: 'Update Card Status?',
          text: confirmText,
          icon: 'info',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed',
          showLoaderOnConfirm: true,
          preConfirm: () => {
              return fetch(`/payments/members/${memberId}/card-status`, {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                      'Accept': 'application/json'
                  },
                  body: JSON.stringify({ status: status })
              })
              .then(response => {
                  if (!response.ok) throw new Error(response.statusText);
                  return response.json();
              })
              .catch(error => {
                  Swal.showValidationMessage(`Request failed: ${error}`);
              });
          },
          allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
          if (result.isConfirmed && result.value.success) {
              Swal.fire('Success', result.value.message, 'success').then(() => location.reload());
          }
      });
  }

  // Auto-refresh dashboard every 60 seconds
  setTimeout(function() {
    location.reload();
  }, 60000);
});
</script>
@endpush
