@extends('settings._layout-base')

@section('title', 'Transaction Reports')
@section('description', 'Transaction Reports - Golf Club Management System')

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
    width: 56px; height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
  }
  .metric-label {
    font-size: 0.825rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
  }
  .metric-value {
    font-size: 1.35rem;
    font-weight: 800;
    color: #212529;
    line-height: 1.2;
  }

  /* ── Quick date buttons ── */
  .date-preset-btn {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 0.4rem 0.9rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
  }
  .date-preset-btn:hover { background: #940000; color: #fff; border-color: #940000; }

  /* ── Chart & Table Cards ── */
  .report-section-card { border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: none; margin-bottom: 1.5rem; }
  .report-section-card .card-header { background: transparent; padding: 1.25rem; border-bottom: 1px solid #f0f0f0; }

  /* ── Badges & Tables ── */
  .txn-table th { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 700; color: #888; background: #fafafa; }
  .txn-table td { padding: 1rem 0.75rem; vertical-align: middle; }
</style>
@endpush

@section('content')

{{-- ═══════════════════ HEADER ═══════════════════ --}}
<div class="reports-header shadow-sm">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
      <h4 class="mb-1 fw-bold text-dark">Transaction Reports</h4>
      <div class="d-flex align-items-center gap-2 text-muted small">
        <i class="icon-base ri ri-calendar-line"></i>
        <span>{{ $fromDate->format('d M Y') }} — {{ $toDate->format('d M Y') }}</span>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('reports.transactions.pdf', request()->all()) }}" target="_blank" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
        <i class="icon-base ri ri-file-pdf-line me-1"></i> Export PDF
      </a>
      <button class="btn btn-primary btn-sm px-4 rounded-pill" style="background:#940000; border-color:#940000;" onclick="location.reload()">
        <i class="icon-base ri ri-refresh-line me-1"></i> Refresh
      </button>
    </div>
  </div>
</div>

{{-- ═══════════════════ KEY METRICS ═══════════════════ --}}
<div class="row g-3 mb-4">
  {{-- Total Count --}}
  <div class="col-md-3 col-sm-6">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(148,0,0,0.1); color:#940000;">
        <i class="icon-base ri ri-exchange-line"></i>
      </div>
      <div>
        <div class="metric-label">Total Transactions</div>
        <div class="metric-value">{{ number_format($summary['total_transactions'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Total Payments --}}
  <div class="col-md-3 col-sm-6">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(148,0,0,0.1); color:#940000;">
        <i class="icon-base ri ri-arrow-down-circle-line"></i>
      </div>
      <div>
        <div class="metric-label">Total Payments</div>
        <div class="metric-value text-danger">TZS {{ number_format($summary['total_payments'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Total Topups --}}
  <div class="col-md-3 col-sm-6">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(40,167,69,0.1); color:#28A745;">
        <i class="icon-base ri ri-arrow-up-circle-line"></i>
      </div>
      <div>
        <div class="metric-label">Total Top-ups</div>
        <div class="metric-value text-success">TZS {{ number_format($summary['total_topups'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Refunds --}}
  <div class="col-md-3 col-sm-6">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(255,193,7,0.1); color:#FFC107;">
        <i class="icon-base ri ri-refund-2-line"></i>
      </div>
      <div>
        <div class="metric-label">Total Refunds</div>
        <div class="metric-value text-warning">TZS {{ number_format($summary['total_refunds'] ?? 0) }}</div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ FILTERS ═══════════════════ --}}
<div class="card report-section-card">
  <div class="card-body">
    <form method="GET" action="{{ route('reports.transactions') }}" id="filterForm" class="row g-3">
      <div class="col-md-3">
        <label class="form-label small fw-bold text-muted">From Date</label>
        <input type="date" class="form-control form-control-sm" name="from_date" id="from_date" value="{{ $fromDate->format('Y-m-d') }}" />
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-bold text-muted">To Date</label>
        <input type="date" class="form-control form-control-sm" name="to_date" id="to_date" value="{{ $toDate->format('Y-m-d') }}" />
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-bold text-muted">Type</label>
        <select class="form-select form-select-sm" name="type">
          <option value="">All Types</option>
          <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payment</option>
          <option value="topup" {{ request('type') === 'topup' ? 'selected' : '' }}>Top-up</option>
          <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>Refund</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-bold text-muted">Category</label>
        <select class="form-select form-select-sm" name="category">
          <option value="">All Categories</option>
          <option value="driving_range" {{ request('category') === 'driving_range' ? 'selected' : '' }}>Driving Range</option>
          <option value="ball_management" {{ request('category') === 'ball_management' ? 'selected' : '' }}>Ball Management</option>
          <option value="equipment_rental" {{ request('category') === 'equipment_rental' ? 'selected' : '' }}>Equipment Rental</option>
          <option value="equipment_sale" {{ request('category') === 'equipment_sale' ? 'selected' : '' }}>Equipment Sale</option>
          <option value="food_beverage" {{ request('category') === 'food_beverage' ? 'selected' : '' }}>Food & Beverage</option>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-dark btn-sm flex-grow-1 rounded-pill">Apply Filter</button>
        <a href="{{ route('reports.transactions') }}" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="ri-refresh-line"></i></a>
      </div>
    </form>
    
    <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
      @foreach([
        ['today', 'Today'],
        ['yesterday', 'Yesterday'],
        ['this_week', 'This Week'],
        ['this_month', 'This Month'],
        ['last_month', 'Last Month'],
      ] as [$key, $label])
        <button type="button" class="date-preset-btn" onclick="setDateRange('{{ $key }}')">{{ $label }}</button>
      @endforeach
    </div>
  </div>
</div>

{{-- ═══════════════════ BREAKDOWNS ═══════════════════ --}}
<div class="row g-3 mb-4">
  @if(isset($summary['by_category']) && $summary['by_category']->count() > 0)
  <div class="col-md-6">
    <div class="card report-section-card h-100">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="icon-base ri ri-pie-chart-line text-primary"></i>
        <h6 class="mb-0 fw-bold">Revenue by Category</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-sm txn-table mb-0">
          <thead>
            <tr>
              <th class="ps-4">Category</th>
              <th class="text-center">Transactions</th>
              <th class="text-end pe-4">Revenue</th>
            </tr>
          </thead>
          <tbody>
            @foreach($summary['by_category'] as $cat)
            <tr>
              <td class="ps-4">
                <span class="fw-semibold text-dark">{{ ucfirst(str_replace('_', ' ', $cat->category)) }}</span>
              </td>
              <td class="text-center"><span class="badge bg-label-secondary rounded-pill">{{ number_format($cat->count) }}</span></td>
              <td class="text-end pe-4"><strong class="text-dark">TZS {{ number_format($cat->total) }}</strong></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  @if(isset($summary['by_payment_method']) && $summary['by_payment_method']->count() > 0)
  <div class="col-md-6">
    <div class="card report-section-card h-100">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="icon-base ri ri-bank-card-line text-success"></i>
        <h6 class="mb-0 fw-bold">Revenue by Payment Method</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-sm txn-table mb-0">
          <thead>
            <tr>
              <th class="ps-4">Method</th>
              <th class="text-center">Transactions</th>
              <th class="text-end pe-4">Revenue</th>
            </tr>
          </thead>
          <tbody>
            @foreach($summary['by_payment_method'] as $method)
            @php
              $mLabels = ['balance'=>'Member Balance','cash'=>'Cash','card'=>'Card','mobile'=>'Mobile Money'];
              $mLabel = $mLabels[$method->payment_method] ?? strtoupper($method->payment_method);
            @endphp
            <tr>
              <td class="ps-4">
                <span class="fw-semibold text-dark">{{ $mLabel }}</span>
              </td>
              <td class="text-center"><span class="badge bg-label-secondary rounded-pill">{{ number_format($method->count) }}</span></td>
              <td class="text-end pe-4"><strong class="text-dark">TZS {{ number_format($method->total) }}</strong></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>

{{-- ═══════════════════ MAIN TRANSACTIONS TABLE ═══════════════════ --}}
<div class="card report-section-card">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center">
    <h6 class="mb-0 fw-bold"><i class="icon-base ri ri-list-check me-2"></i>Transaction Register</h6>
    <span class="badge bg-label-primary rounded-pill">Total: {{ $transactions->total() }}</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle txn-table mb-0">
      <thead>
        <tr>
          <th class="ps-4">ID</th>
          <th>Customer / Member</th>
          <th>Type & Category</th>
          <th class="text-end">Amount</th>
          <th>Payment</th>
          <th>Date & Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $txn)
        <tr>
          <td class="ps-4">
            <code class="text-primary small fw-bold">{{ $txn->transaction_id }}</code>
          </td>
          <td>
            <div class="fw-bold text-dark">{{ $txn->customer_name }}</div>
            @if($txn->member)<small class="text-muted">{{ $txn->member->card_number }}</small>@endif
          </td>
          <td>
            <div class="mb-1">
              @if($txn->type === 'payment')
                <span class="badge bg-label-danger py-0 px-2" style="font-size:0.65rem;">PAYMENT</span>
              @elseif($txn->type === 'topup')
                <span class="badge bg-label-success py-0 px-2" style="font-size:0.65rem;">TOP-UP</span>
              @else
                <span class="badge bg-label-warning py-0 px-2" style="font-size:0.65rem;">{{ strtoupper($txn->type) }}</span>
              @endif
            </div>
            <span class="text-muted small fw-semibold">{{ ucfirst(str_replace('_', ' ', $txn->category)) }}</span>
          </td>
          <td class="text-end">
            <div class="fw-extrabold {{ $txn->type === 'payment' ? 'text-danger' : 'text-success' }}" style="font-size:1rem;">
              {{ $txn->type === 'payment' ? '-' : '+' }}TZS {{ number_format($txn->amount) }}
            </div>
          </td>
          <td>
            @php
              $mColors = ['balance' => '#940000', 'cash' => '#28a745', 'card' => '#0d6efd', 'mobile' => '#fd7e14'];
              $mColor  = $mColors[$txn->payment_method] ?? '#6c757d';
            @endphp
            <span class="badge" style="background:{{ $mColor }}; color: #fff; font-size: 0.65rem; border: none; font-weight: 700;">
              {{ strtoupper($txn->payment_method) }}
            </span>
          </td>
          <td>
            <div class="small fw-semibold text-dark">{{ $txn->created_at->format('d M Y, H:i') }}</div>
            <div class="mt-1">
              @if($txn->status === 'completed')
                <span class="text-success small"><i class="ri-checkbox-circle-fill me-1"></i>Completed</span>
              @else
                <span class="text-warning small"><i class="ri-time-fill me-1"></i>{{ ucfirst($txn->status) }}</span>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-5">
            <div class="text-muted opacity-50">
              <i class="ri-inbox-line" style="font-size:3rem;"></i>
              <p class="mt-2">No transactions matching filters</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($transactions->hasPages())
  <div class="card-footer border-top bg-light py-3">
    {{ $transactions->links() }}
  </div>
  @endif
</div>

@endsection

@push('scripts')
<script>
function setDateRange(range) {
  const today = new Date();
  const from  = document.getElementById('from_date');
  const to    = document.getElementById('to_date');
  let f, t;

  switch(range) {
    case 'today':       f = new Date(today); t = new Date(today); break;
    case 'yesterday':   f = new Date(today); f.setDate(f.getDate()-1); t = new Date(f); break;
    case 'this_week':   f = new Date(today); f.setDate(f.getDate()-f.getDay()); t = new Date(today); break;
    case 'this_month':  f = new Date(today.getFullYear(), today.getMonth(), 1); t = new Date(today); break;
    case 'last_month':  f = new Date(today.getFullYear(), today.getMonth()-1, 1); t = new Date(today.getFullYear(), today.getMonth(), 0); break;
  }
  
  if(f && t) {
    from.value = f.toISOString().split('T')[0];
    to.value   = t.toISOString().split('T')[0];
    document.getElementById('filterForm').submit();
  }
}
</script>
@endpush
