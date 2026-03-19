@extends('settings._layout-base')

@section('title', 'Ball Manager Dashboard')
@section('description', 'Dashboard for Ball Manager - Golf Club Management System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <span class="text-muted fw-light">Dashboard /</span> Ball Manager
    </h4>
    <div>
        <span class="badge bg-primary fs-6"><i class="ri ri-user-settings-line me-1"></i> Ball Manager Mode</span>
    </div>
</div>

<!-- Visual Inventory & Quick Stats -->
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="card-body p-0">
        <div class="row g-0">
          <div class="col-md-8 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0 fw-bold"><i class="ri ri-inbox-archive-line me-2 text-primary"></i>Live Ball Inventory</h5>
            </div>
            
            @php
              $availablePercent = $stats['total'] > 0 ? ($stats['available'] / $stats['total']) * 100 : 0;
              $inUsePercent = $stats['total'] > 0 ? ($stats['in_use'] / $stats['total']) * 100 : 0;
              $damagedPercent = $stats['total'] > 0 ? ($stats['damaged'] / $stats['total']) * 100 : 0;
            @endphp
            
            <div class="progress mb-4" style="height: 42px; border-radius: 12px; background-color: #f1f3f9;">
              <div class="progress-bar bg-success" role="progressbar" style="width: {{ $availablePercent }}%" 
                   title="Available: {{ number_format($stats['available']) }}" 
                   data-bs-toggle="tooltip" data-bs-placement="top">
                <span class="fw-bold">{{ number_format($stats['available']) }} Available</span>
              </div>
              <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $inUsePercent }}%" 
                   title="In Use: {{ number_format($stats['in_use']) }}"
                   data-bs-toggle="tooltip" data-bs-placement="top">
                <span class="fw-bold">{{ number_format($stats['in_use']) }}</span>
              </div>
              <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $damagedPercent }}%" 
                   title="Damaged: {{ number_format($stats['damaged']) }}"
                   data-bs-toggle="tooltip" data-bs-placement="top"></div>
            </div>
            
            <div class="row g-4">
              <div class="col-md-4">
                <div class="p-3 border rounded bg-light-subtle">
                  <small class="text-muted d-block mb-1">Total Capacity</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($stats['total']) }} <span class="fs-6 fw-normal text-muted">Balls</span></h4>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 border border-success rounded bg-success-subtle">
                  <small class="text-success d-block mb-1">Available Now</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($stats['available']) }}</h4>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 border border-warning rounded bg-warning-subtle text-warning">
                  <small class="d-block mb-1">Currently in Use</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($stats['in_use']) }}</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4 bg-primary text-white p-4">
            <h5 class="text-white fw-bold mb-4">Today's Summary</h5>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>Balls Issued</span>
                <strong>{{ number_format($stats['issued_today'] ?? 0) }}</strong>
              </div>
              <div class="progress progress-white" style="height: 6px;">
                <div class="progress-bar bg-white" style="width: 70%"></div>
              </div>
            </div>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>Balls Returned</span>
                <strong>{{ number_format($stats['returned_today'] ?? 0) }}</strong>
              </div>
              <div class="progress progress-white" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 60%"></div>
              </div>
            </div>
            <div class="pt-2">
              <div class="alert alert-white bg-white text-primary border-0 mb-0 py-2">
                <small class="d-block">Daily Revenue</small>
                <h5 class="mb-0 fw-bold">TZS {{ number_format($stats['revenue_today'] ?? 0) }}</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions (Hidden per user request) -->
{{-- 
<div class="row mb-4">
    <!-- Links to core management functions instead of duplicating complex POS logic here -->
    <div class="col-md-6 mb-4">
        <a href="{{ route('golf-services.ball-management') }}#issue" class="card h-100 border-0 shadow-sm text-decoration-none hover-up transition-all">
            <div class="card-body p-4 text-center">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="ri ri-send-plane-fill fs-2"></i></span>
                </div>
                <h4 class="fw-bold text-heading">Issue Balls (POS)</h4>
                <p class="text-muted mb-0">Quickly issue balls to customers or members.</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 mb-4">
        <a href="{{ route('golf-services.ball-management') }}#return" class="card h-100 border-0 shadow-sm text-decoration-none hover-up transition-all">
            <div class="card-body p-4 text-center">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="ri ri-download-2-fill fs-2"></i></span>
                </div>
                <h4 class="fw-bold text-heading">Return Balls</h4>
                <p class="text-muted mb-0">Process ball returns and update inventory.</p>
            </div>
        </a>
    </div>
</div>
--}}

<!-- Recent Transactions List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold"><i class="ri ri-history-line me-2 text-primary"></i>Recent Transactions</h5>
        <a href="{{ route('golf-services.ball-management') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-borderless mb-0">
                <thead class="table-light border-bottom">
                    <tr>
                        <th class="ps-4">Time</th>
                        <th>Type</th>
                        <th>Customer / Member</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end pe-4">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayTransactions as $txn)
                        <tr>
                            <td class="ps-4 text-muted">{{ $txn->created_at->format('H:i') }}</td>
                            <td>
                                @if($txn->type === 'issued')
                                    <span class="badge bg-label-primary rounded-pill"><i class="ri ri-send-plane-fill me-1"></i>Issued</span>
                                @elseif($txn->type === 'returned')
                                    <span class="badge bg-label-success rounded-pill"><i class="ri ri-download-2-fill me-1"></i>Returned</span>
                                @elseif($txn->type === 'purchased')
                                    <span class="badge bg-label-info rounded-pill"><i class="ri ri-add-circle-fill me-1"></i>Stock Added</span>
                                @elseif($txn->type === 'damaged')
                                    <span class="badge bg-label-danger rounded-pill"><i class="ri ri-error-warning-fill me-1"></i>Damaged</span>
                                @else
                                    <span class="badge bg-label-secondary rounded-pill">{{ ucfirst($txn->type) }}</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $txn->customer_name ?: 'System' }}</td>
                            <td class="text-center fw-bold">{{ $txn->quantity }}</td>
                            <td class="text-end pe-4">
                                @if($txn->amount > 0)
                                    TZS {{ number_format($txn->amount) }}
                                    <small class="d-block text-muted" style="font-size: 0.7rem;">via {{ ucfirst(str_replace('_', ' ', $txn->payment_method)) }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No transactions recorded today.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.hover-up {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.hover-up:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection
