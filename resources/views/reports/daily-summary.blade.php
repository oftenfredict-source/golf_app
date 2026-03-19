@extends('settings._layout-base')

@section('title', 'Daily Summary')
@section('description', 'Daily Summary Report - Golf Club Management System')

@push('styles')
<style>
  /* ── Page Header ── */
  .reports-header {
    background: #fff;
    border-bottom: 1px solid #e0e0e0;
    margin: -1.5rem -1.5rem 1.5rem -1.5rem;
    padding: 1.5rem;
  }
  
  /* ── Executive Metric Cards ── */
  .metric-card-clean {
    background: #fff;
    border: none;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
    transition: all 0.25s ease;
    height: 100%;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
  }
  .metric-card-clean:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.1), 0 5px 15px rgba(0,0,0,0.05), 0 0 15px currentColor;
    transform: translateY(-5px) scale(1.02);
    border-bottom: 4px solid currentColor;
  }
  .metric-card-clean:hover .metric-icon-circle {
    background-color: currentColor !important;
    color: #fff !important;
    transform: scale(1.15) rotate(5deg);
    box-shadow: 0 0 20px currentColor;
  }
  .metric-icon-circle {
    width: 52px; height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
  }
  .metric-label {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.2rem;
  }
  .metric-value {
    font-size: 1.2rem;
    font-weight: 800;
    color: #212529;
    line-height: 1.1;
  }

  /* ── Hero Revenue Card ── */
  .hero-revenue-card {
    background: linear-gradient(135deg, #940000 0%, #c00000 100%);
    color: white;
    border: none;
    border-radius: 16px;
    text-align: center;
    padding: 2.5rem 1.5rem;
    box-shadow: 0 8px 24px rgba(148,0,0,0.2);
    transition: all 0.3s ease;
    cursor: default;
  }
  .hero-revenue-card:hover {
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    filter: brightness(1.05);
    transform: translateY(-3px);
  }

  /* ── Service Section Cards ── */
  .service-card { border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: none; margin-bottom: 1.5rem; }
  .service-card .card-header { 
    background: transparent; 
    padding: 1.25rem 1.25rem 0.75rem 1.25rem; 
    border-bottom: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
  }
  .service-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: default; }
  .service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  }
  .service-card:hover .card-header .metric-icon-circle {
    background-color: currentColor !important;
    color: #fff !important;
    transform: rotate(-10deg) scale(1.2);
    box-shadow: 0 0 15px currentColor;
  }
  .service-card .card-body { padding: 0.75rem 1.25rem 1.25rem 1.25rem; }
  .service-detail-item { padding: 0.5rem 0; border-bottom: 1px solid #f8f9fa; display: flex; justify-content: space-between; align-items: center; }
  .service-detail-item:last-child { border-bottom: none; }
</style>
@endpush

@section('content')

{{-- ═══════════════════ HEADER ═══════════════════ --}}
<div class="reports-header shadow-sm mt-0">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
      <h4 class="mb-1 fw-bold text-dark">Daily Summary Report</h4>
      <div class="d-flex align-items-center gap-2 text-muted">
        <i class="ri-calendar-check-line text-primary"></i>
        <span class="fw-semibold">{{ $date->format('l, F d, Y') }}</span>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('reports.daily-summary.pdf', ['date' => $date->format('Y-m-d')]) }}" target="_blank" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
        <i class="ri-file-pdf-line me-1"></i> Export PDF
      </a>
      @if($date->format('Y-m-d') !== date('Y-m-d'))
        <a href="{{ route('reports.daily-summary') }}" class="btn btn-label-secondary btn-sm px-3 rounded-pill">
          <i class="ri-history-line me-1"></i> Today
        </a>
      @endif
      <button class="btn btn-primary btn-sm px-4 rounded-pill" style="background:#940000; border-color:#940000;" onclick="location.reload()">
        <i class="ri-refresh-line me-1"></i> Refresh
      </button>
    </div>
  </div>
</div>

{{-- ═══════════════════ HERO REVENUE ═══════════════════ --}}
<div class="row mb-4 pt-1">
  <div class="col-12">
    <div class="hero-revenue-card">
      <div class="mb-2 opacity-75 fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem;">Total Daily Revenue</div>
      <h1 class="display-4 fw-black mb-0" style="font-weight: 900;">TZS {{ number_format($summary['total_revenue'] ?? 0) }}</h1>
      <div class="mt-2 small opacity-75">Transactions: {{ number_format($summary['transactions']['total'] ?? 0) }}</div>
    </div>
  </div>
</div>

{{-- ═══════════════════ FILTERS ═══════════════════ --}}
<div class="card report-section-card mb-4">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('reports.daily-summary') }}" class="row g-2 align-items-end justify-content-center">
      <div class="col-md-4">
        <label class="form-label small fw-bold text-muted mb-1">Select Summary Date</label>
        <div class="input-group input-group-sm rounded-pill overflow-hidden border">
          <span class="input-group-text bg-white border-0 ps-3"><i class="ri-calendar-event-line"></i></span>
          <input type="date" class="form-control border-0 py-2" name="date" value="{{ $date->format('Y-m-d') }}" />
        </div>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-sm w-100 rounded-pill py-2 fw-bold" style="background:#940000; color:#fff;">View Report</button>
      </div>
    </form>
  </div>
</div>

{{-- ═══════════════════ SERVICE BREAKDOWN ═══════════════════ --}}
<div class="row g-3 mb-4">
  {{-- Driving Range --}}
  <div class="col-md-6 col-lg-4">
    <div class="card service-card h-100">
      <div class="card-header">
        <div class="metric-icon-circle" style="background:rgba(148,0,0,0.08); color:#940000; width:38px; height:38px; font-size:1.1rem;">
          <i class="icon-base ri ri-golf-ball-line"></i>
        </div>
        <h6 class="mb-0 fw-bold">Driving Range</h6>
      </div>
      <div class="card-body">
        <div class="service-detail-item">
          <span class="text-muted small">Total Sessions</span>
          <span class="fw-bold">{{ number_format($summary['driving_range']['sessions'] ?? 0) }}</span>
        </div>
        <div class="service-detail-item">
          <span class="text-muted small">Completed / Active</span>
          <span class="fw-bold">{{ $summary['driving_range']['completed'] ?? 0 }} / {{ $summary['driving_range']['active'] ?? 0 }}</span>
        </div>
        <div class="service-detail-item mt-2 bg-light p-2 rounded">
          <span class="fw-semibold">Revenue</span>
          <span class="fw-black" style="color:#940000;">TZS {{ number_format($summary['driving_range']['revenue'] ?? 0) }}</span>
        </div>
      </div>
    </div>
  </div>

  @if(auth()->user()->role !== 'storekeeper')
  {{-- Food & Beverage --}}
  <div class="col-md-6 col-lg-4">
    <div class="card service-card h-100">
      <div class="card-header">
        <div class="metric-icon-circle" style="background:rgba(40,167,69,0.08); color:#28A745; width:38px; height:38px; font-size:1.1rem;">
          <i class="icon-base ri ri-restaurant-line"></i>
        </div>
        <h6 class="mb-0 fw-bold">Food & Beverage</h6>
      </div>
      <div class="card-body">
        <div class="service-detail-item">
          <span class="text-muted small">Total Orders</span>
          <span class="fw-bold">{{ number_format($summary['food_beverage']['orders'] ?? 0) }}</span>
        </div>
        <div class="service-detail-item">
          <span class="text-muted small">Completed / Pending</span>
          <span class="fw-bold text-success">{{ $summary['food_beverage']['completed'] ?? 0 }}</span>
        </div>
        <div class="service-detail-item mt-2 bg-light p-2 rounded">
          <span class="fw-semibold">Revenue</span>
          <span class="fw-black text-success">TZS {{ number_format($summary['food_beverage']['revenue'] ?? 0) }}</span>
        </div>
      </div>
    </div>
  </div>
  @endif

  @if(auth()->user()->role !== 'storekeeper')
  {{-- Top-ups --}}
  <div class="col-md-6 col-lg-4">
    <div class="card service-card h-100">
      <div class="card-header">
        <div class="metric-icon-circle" style="background:rgba(255,193,7,0.1); color:#FFC107; width:38px; height:38px; font-size:1.1rem;">
          <i class="icon-base ri ri-add-circle-line"></i>
        </div>
        <h6 class="mb-0 fw-bold">Member Top-ups</h6>
      </div>
      <div class="card-body">
        <div class="service-detail-item">
          <span class="text-muted small">Transaction Count</span>
          <span class="fw-bold">{{ number_format($summary['topups']['count'] ?? 0) }}</span>
        </div>
        <div class="service-detail-item">
          <span class="text-muted small">Avg / Top-up</span>
          <span class="fw-bold">{{ number_format($summary['topups']['average'] ?? 0) }}</span>
        </div>
        <div class="service-detail-item mt-2 bg-light p-2 rounded">
          <span class="fw-semibold">Total Load</span>
          <span class="fw-black text-warning">TZS {{ number_format($summary['topups']['amount'] ?? 0) }}</span>
        </div>
      </div>
    </div>
  </div>
  @endif

  {{-- Equipment Rental --}}
  <div class="col-md-6 col-lg-4">
    <div class="card service-card h-100">
      <div class="card-header">
        <div class="metric-icon-circle" style="background:rgba(0,188,212,0.08); color:#00BCD4; width:38px; height:38px; font-size:1.1rem;">
          <i class="icon-base ri ri-tools-line"></i>
        </div>
        <h6 class="mb-0 fw-bold">Equipment Rental</h6>
      </div>
      <div class="card-body">
        <div class="service-detail-item">
          <span class="text-muted small">Total Rentals</span>
          <span class="fw-bold">{{ number_format($summary['equipment_rental']['rentals'] ?? 0) }}</span>
        </div>
        <div class="service-detail-item">
          <span class="text-muted small">Returned / Active</span>
          <span class="fw-bold text-info">{{ $summary['equipment_rental']['returned'] ?? 0 }} / {{ $summary['equipment_rental']['active'] ?? 0 }}</span>
        </div>
        <div class="service-detail-item mt-2 bg-light p-2 rounded">
          <span class="fw-semibold">Revenue</span>
          <span class="fw-black text-info">TZS {{ number_format($summary['equipment_rental']['revenue'] ?? 0) }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Equipment Sales --}}
  <div class="col-md-6 col-lg-4">
    <div class="card service-card h-100">
      <div class="card-header">
        <div class="metric-icon-circle" style="background:rgba(103,58,183,0.08); color:#673AB7; width:38px; height:38px; font-size:1.1rem;">
          <i class="icon-base ri ri-shopping-bag-line"></i>
        </div>
        <h6 class="mb-0 fw-bold">Equipment Sales</h6>
      </div>
      <div class="card-body">
        <div class="service-detail-item">
          <span class="text-muted small">Direct Sales</span>
          <span class="fw-bold">{{ number_format($summary['equipment_sales']['sales'] ?? 0) }}</span>
        </div>
        <div class="service-detail-item mt-2 bg-light p-2 rounded">
          <span class="fw-semibold">Revenue</span>
          <span class="fw-black" style="color:#673AB7;">TZS {{ number_format($summary['equipment_sales']['revenue'] ?? 0) }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Ball Management --}}
  <div class="col-md-6 col-lg-4">
    <div class="card service-card h-100">
      <div class="card-header">
        <div class="metric-icon-circle" style="background:rgba(233,30,99,0.08); color:#E91E63; width:38px; height:38px; font-size:1.1rem;">
          <i class="icon-base ri ri-basketball-line"></i>
        </div>
        <h6 class="mb-0 fw-bold">Ball Management</h6>
      </div>
      <div class="card-body">
        <div class="service-detail-item">
          <span class="text-muted small">Total Dispenses</span>
          <span class="fw-bold">{{ number_format($summary['ball_management']['transactions'] ?? 0) }}</span>
        </div>
        <div class="service-detail-item mt-2 bg-light p-2 rounded">
          <span class="fw-semibold">Revenue</span>
          <span class="fw-black" style="color:#E91E63;">TZS {{ number_format($summary['ball_management']['revenue'] ?? 0) }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ TRANSACTION SUMMARY ═══════════════════ --}}
<div class="row g-3 mb-2">
  <div class="col-12"><h6 class="fw-bold mb-0">Daily Audit Registry</h6></div>
  
  <div class="col-sm-6 col-md-3">
    <div class="metric-card-clean py-2" style="color:#212529;">
      <div class="metric-icon-circle" style="background:rgba(148,0,0,0.05); color:#940000; width:40px; height:40px; font-size:1.2rem; transition: all 0.3s;">
        <i class="icon-base ri ri-list-check"></i>
      </div>
      <div>
        <div class="metric-label">Total Txns</div>
        <div class="metric-value">{{ number_format($summary['transactions']['total'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-md-3">
    <div class="metric-card-clean py-2 border-start border-danger border-4" style="color:#940000;">
      <div class="metric-icon-circle" style="background:rgba(148,0,0,0.05); color:inherit; width:40px; height:40px; font-size:1.2rem; transition: all 0.3s;">
        <i class="icon-base ri ri-arrow-down-circle-line"></i>
      </div>
      <div>
        <div class="metric-label">Payments</div>
        <div class="metric-value text-danger">{{ number_format($summary['transactions']['payments'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  @if(auth()->user()->role !== 'storekeeper')
  <div class="col-sm-6 col-md-3">
    <div class="metric-card-clean py-2 border-start border-success border-4" style="color:#28A745;">
      <div class="metric-icon-circle" style="background:rgba(40,167,69,0.05); color:inherit; width:40px; height:40px; font-size:1.2rem; transition: all 0.3s;">
        <i class="icon-base ri ri-arrow-up-circle-line"></i>
      </div>
      <div>
        <div class="metric-label">Top-ups</div>
        <div class="metric-value text-success">{{ number_format($summary['transactions']['topups_count'] ?? 0) }}</div>
      </div>
    </div>
  </div>
  @endif

  <div class="col-sm-6 col-md-3">
    <div class="metric-card-clean py-2 border-start border-warning border-4" style="color:#FFC107;">
      <div class="metric-icon-circle" style="background:rgba(255,193,7,0.05); color:inherit; width:40px; height:40px; font-size:1.2rem; transition: all 0.3s;">
        <i class="icon-base ri ri-refund-2-line"></i>
      </div>
      <div>
        <div class="metric-label">Refunds</div>
        <div class="metric-value text-warning">{{ number_format($summary['transactions']['refunds'] ?? 0) }}</div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// Prevent multiple submissions
document.querySelector('form').addEventListener('submit', function() {
  this.querySelector('button[type="submit"]').disabled = true;
  this.querySelector('button[type="submit"]').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Loading...';
});
</script>
@endpush
