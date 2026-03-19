@extends('settings._layout-base')

@section('title', 'Reports Overview')
@section('description', 'Reports Overview - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Reports /</span> Overview
</h4>

<!-- Today's Summary -->
<div class="row mb-6">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Today's Summary</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2 col-6 mb-4">
            <div class="text-center">
              <div class="avatar avatar-lg mx-auto mb-2">
                <div class="avatar-initial bg-primary rounded">
                  <i class="ri ri-golf-ball-line"></i>
                </div>
              </div>
              <h6 class="mb-0">Driving Range</h6>
              <h4 class="text-primary">TZS {{ number_format($todaySummary['driving_range'] ?? 0) }}</h4>
            </div>
          </div>
          <div class="col-md-2 col-6 mb-4">
            <div class="text-center">
              <div class="avatar avatar-lg mx-auto mb-2">
                <div class="avatar-initial bg-info rounded">
                  <i class="ri ri-tools-line"></i>
                </div>
              </div>
              <h6 class="mb-0">Equipment Rental</h6>
              <h4 class="text-info">TZS {{ number_format($todaySummary['equipment_rental'] ?? 0) }}</h4>
            </div>
          </div>
          <div class="col-md-2 col-6 mb-4">
            <div class="text-center">
              <div class="avatar avatar-lg mx-auto mb-2">
                <div class="avatar-initial bg-warning rounded">
                  <i class="ri ri-shopping-bag-line"></i>
                </div>
              </div>
              <h6 class="mb-0">Equipment Sales</h6>
              <h4 class="text-warning">TZS {{ number_format($todaySummary['equipment_sales'] ?? 0) }}</h4>
            </div>
          </div>
          @if(auth()->user()->role !== 'storekeeper')
          <div class="col-md-2 col-6 mb-4">
            <div class="text-center">
              <div class="avatar avatar-lg mx-auto mb-2">
                <div class="avatar-initial bg-success rounded">
                  <i class="ri ri-restaurant-line"></i>
                </div>
              </div>
              <h6 class="mb-0">Food & Beverage</h6>
              <h4 class="text-success">TZS {{ number_format($todaySummary['food_beverage'] ?? 0) }}</h4>
            </div>
          </div>
          @endif
          <div class="col-md-2 col-6 mb-4">
            <div class="text-center">
              <div class="avatar avatar-lg mx-auto mb-2">
                <div class="avatar-initial bg-secondary rounded">
                  <i class="ri ri-add-circle-line"></i>
                </div>
              </div>
              <h6 class="mb-0">Top-ups</h6>
              <h4 class="text-secondary">TZS {{ number_format($todaySummary['topups'] ?? 0) }}</h4>
            </div>
          </div>
          <div class="col-md-2 col-6 mb-4">
            <div class="text-center">
              <div class="avatar avatar-lg mx-auto mb-2">
                <div class="avatar-initial bg-dark rounded">
                  <i class="ri ri-money-dollar-circle-line"></i>
                </div>
              </div>
              <h6 class="mb-0">Total Revenue</h6>
              <h4 class="text-dark">TZS {{ number_format($todaySummary['total'] ?? 0) }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Monthly Summary -->
<div class="row mb-6">
  <div class="col-xl-8">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Monthly Summary ({{ date('F Y') }})</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Category</th>
                <th class="text-end">Amount (TZS)</th>
                <th style="width: 40%">Progress</th>
              </tr>
            </thead>
            <tbody>
              @php $maxAmount = max(array_values($monthlySummary ?? [1])); @endphp
              <tr>
                <td>Driving Range</td>
                <td class="text-end">{{ number_format($monthlySummary['driving_range'] ?? 0) }}</td>
                <td>
                  <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-primary" style="width: {{ ($monthlySummary['driving_range'] ?? 0) / max($maxAmount, 1) * 100 }}%"></div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Equipment Rental</td>
                <td class="text-end">{{ number_format($monthlySummary['equipment_rental'] ?? 0) }}</td>
                <td>
                  <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-info" style="width: {{ ($monthlySummary['equipment_rental'] ?? 0) / max($maxAmount, 1) * 100 }}%"></div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Equipment Sales</td>
                <td class="text-end">{{ number_format($monthlySummary['equipment_sales'] ?? 0) }}</td>
                <td>
                  <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-warning" style="width: {{ ($monthlySummary['equipment_sales'] ?? 0) / max($maxAmount, 1) * 100 }}%"></div>
                  </div>
                </td>
              </tr>
              @if(auth()->user()->role !== 'storekeeper')
              <tr>
                <td>Food & Beverage</td>
                <td class="text-end">{{ number_format($monthlySummary['food_beverage'] ?? 0) }}</td>
                <td>
                  <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: {{ ($monthlySummary['food_beverage'] ?? 0) / max($maxAmount, 1) * 100 }}%"></div>
                  </div>
                </td>
              </tr>
              @endif
              <tr class="table-dark">
                <td><strong>Total Revenue</strong></td>
                <td class="text-end"><strong>{{ number_format($monthlySummary['total'] ?? 0) }}</strong></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  @if(auth()->user()->role !== 'storekeeper')
  <div class="col-xl-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Member Statistics</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <span>Total Members</span>
          <h4 class="mb-0">{{ number_format($memberStats['total_members'] ?? 0) }}</h4>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <span>Active Members</span>
          <h4 class="mb-0 text-success">{{ number_format($memberStats['active_members'] ?? 0) }}</h4>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <span>Total Balance</span>
          <h4 class="mb-0 text-primary">TZS {{ number_format($memberStats['total_balance'] ?? 0) }}</h4>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <span>New This Month</span>
          <h4 class="mb-0 text-info">{{ number_format($memberStats['new_this_month'] ?? 0) }}</h4>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>

<!-- Quick Links -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Report Links</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3 col-6">
            <a href="{{ route('reports.revenue') }}" class="btn btn-outline-primary w-100 py-3">
              <i class="ri ri-money-dollar-circle-line d-block mb-2" style="font-size: 24px;"></i>
              Revenue Reports
            </a>
          </div>
          <div class="col-md-3 col-6">
            <a href="{{ route('reports.transactions') }}" class="btn btn-outline-info w-100 py-3">
              <i class="ri ri-exchange-line d-block mb-2" style="font-size: 24px;"></i>
              Transaction Reports
            </a>
          </div>
          @if(auth()->user()->role !== 'storekeeper')
          <div class="col-md-3 col-6">
            <a href="{{ route('reports.members') }}" class="btn btn-outline-success w-100 py-3">
              <i class="ri ri-user-line d-block mb-2" style="font-size: 24px;"></i>
              Member Reports
            </a>
          </div>
          @endif
          <div class="col-md-3 col-6">
            <a href="{{ route('reports.daily-summary') }}" class="btn btn-outline-warning w-100 py-3">
              <i class="ri ri-calendar-line d-block mb-2" style="font-size: 24px;"></i>
              Daily Summary
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection



