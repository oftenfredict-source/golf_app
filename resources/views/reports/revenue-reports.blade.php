@extends('settings._layout-base')

@section('title', 'Revenue Reports')
@section('description', 'Revenue Reports - Golf Club Management System')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

  /* ── Category cards ── */
  .rev-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .rev-card:hover { 
    transform: translateY(-6px) scale(1.03); 
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }
  .rev-card:hover .icon-wrap {
    background-color: currentColor !important;
    color: #fff !important;
    transform: rotate(-12deg) scale(1.15);
    box-shadow: 0 0 15px currentColor;
  }
  .rev-card .icon-wrap {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
  }
  .rev-card .cat-pct-bar {
    height: 4px; border-radius: 99px; background: #e9ecef; margin-top: 0.5rem;
  }
  .rev-card .cat-pct-fill { height: 100%; border-radius: 99px; }

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
  .date-preset-btn.active { background: #940000; color: #fff; border-color: #940000; }

  /* ── Trend chart card ── */
  .chart-card { border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: none; }

  /* ── Table ── */
  .txn-table th { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 700; color: #888; }
  .rank-badge { width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800; }
</style>
@endpush

@section('content')

@php
  $totalRevenue   = $revenueByCategory['total'] ?? 0;
  $daysDiff       = round($fromDate->diffInDays($toDate)) + 1;
  $averagePerDay  = $daysDiff > 0 ? $totalRevenue / $daysDiff : 0;

  $cats = [
    'driving_range'    => ['label' => 'Driving Range',    'icon' => 'ri-golf-ball-line',     'color' => '#0d6efd', 'bg' => '#e7f0ff'],
    'equipment_rental' => ['label' => 'Equipment Rental', 'icon' => 'ri-tools-line',          'color' => '#17a2b8', 'bg' => '#e0f7fa'],
    'equipment_sales'  => ['label' => 'Equipment Sales',  'icon' => 'ri-shopping-bag-line',   'color' => '#fd7e14', 'bg' => '#fff3e0'],
    'ball_management'  => ['label' => 'Ball Management',  'icon' => 'ri-basketball-line',     'color' => '#6f42c1', 'bg' => '#ede7f6'],
  ];
  if(auth()->user()->role !== 'storekeeper') {
    $cats['food_beverage'] = ['label' => 'Food & Beverage',  'icon' => 'ri-restaurant-line',     'color' => '#28a745', 'bg' => '#e8f5e9'];
  }

  $topCategory = 'N/A'; $topAmount = 0;
  foreach($revenueByCategory as $k => $v) {
    if($k !== 'total' && $v > $topAmount) { $topAmount = $v; $topCategory = $cats[$k]['label'] ?? ucfirst(str_replace('_', ' ', $k)); }
  }

  $totalPaymentMethods = array_sum($paymentMethodBreakdown ?? []);
  $balancePct  = $totalPaymentMethods > 0 ? ($paymentMethodBreakdown['balance'] ?? 0) / $totalPaymentMethods * 100 : 0;
  $cashPct     = $totalPaymentMethods > 0 ? ($paymentMethodBreakdown['cash']    ?? 0) / $totalPaymentMethods * 100 : 0;
  $cardPct     = $totalPaymentMethods > 0 ? ($paymentMethodBreakdown['card']    ?? 0) / $totalPaymentMethods * 100 : 0;
  $mobilePct   = $totalPaymentMethods > 0 ? ($paymentMethodBreakdown['mobile']  ?? 0) / $totalPaymentMethods * 100 : 0;
@endphp

{{-- ═══════════════════ HEADER ═══════════════════ --}}
<div class="reports-header shadow-sm">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
      <h4 class="mb-1 fw-bold text-dark">Revenue Reports</h4>
      <div class="d-flex align-items-center gap-2 text-muted small">
        <i class="icon-base ri ri-calendar-line"></i>
        <span>{{ $fromDate->format('d M Y') }} — {{ $toDate->format('d M Y') }}</span>
        <span class="badge bg-label-secondary rounded-pill">{{ number_format($daysDiff) }} Days</span>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('reports.revenue.pdf', request()->only(['from_date', 'to_date'])) }}" target="_blank" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
        <i class="icon-base ri ri-file-pdf-line me-1"></i> Export PDF
      </a>
      <button class="btn btn-primary btn-sm px-4 rounded-pill" style="background:#940000; border-color:#940000;" onclick="location.reload()">
        <i class="icon-base ri ri-refresh-line me-1"></i> Refresh Data
      </button>
    </div>
  </div>
</div>

{{-- ═══════════════════ KEY METRICS ═══════════════════ --}}
<div class="row g-3 mb-4">
  {{-- Total Revenue --}}
  <div class="col-md-3 col-sm-6">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(148,0,0,0.1); color:#940000;">
        <i class="icon-base ri ri-money-dollar-circle-line"></i>
      </div>
      <div>
        <div class="metric-label">Total Revenue</div>
        <div class="metric-value">TZS {{ number_format($totalRevenue) }}</div>
      </div>
    </div>
  </div>

  {{-- Avg / Day --}}
  <div class="col-md-3 col-sm-6">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(13,110,253,0.1); color:#0D6EFD;">
        <i class="icon-base ri ri-line-chart-line"></i>
      </div>
      <div>
        <div class="metric-label">Average / Day</div>
        <div class="metric-value">TZS {{ number_format($averagePerDay) }}</div>
      </div>
    </div>
  </div>

  {{-- Top Category --}}
  <div class="col-md-3 col-sm-6">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(255,193,7,0.1); color:#FFC107;">
        <i class="icon-base ri ri-star-line"></i>
      </div>
      <div>
        <div class="metric-label">Top Category</div>
        <div class="metric-value" style="font-size:1.15rem;">{{ $topCategory }}</div>
      </div>
    </div>
  </div>

  {{-- Top Amount --}}
  <div class="col-md-3 col-sm-6">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(40,167,69,0.1); color:#28A745;">
        <i class="icon-base ri ri-award-line"></i>
      </div>
      <div>
        <div class="metric-label">Top Transaction</div>
        <div class="metric-value">TZS {{ number_format($topAmount) }}</div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ FILTERS ═══════════════════ --}}
<div class="card mb-4" style="border-radius:12px; border:none; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
  <div class="card-body p-3">
    <div class="row g-3 align-items-center">
      <div class="col-lg-6">
        <div class="d-flex flex-wrap gap-2">
          @foreach([
            ['today',        'Today'],
            ['yesterday',    'Yesterday'],
            ['this_week',    'This Week'],
            ['last_week',    'Last Week'],
            ['this_month',   'This Month'],
            ['last_month',   'Last Month'],
            ['last_30_days', 'Last 30 Days'],
            ['this_year',    'This Year'],
          ] as [$key, $label])
            <button type="button" class="date-preset-btn" onclick="setDateRange('{{ $key }}')">{{ $label }}</button>
          @endforeach
        </div>
      </div>
      <div class="col-lg-6">
        <form method="GET" action="{{ route('reports.revenue') }}" id="filterForm">
          <div class="row g-2 align-items-center">
            <div class="col-sm-5 text-end">
              <input type="date" class="form-control form-control-sm" id="from_date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" />
            </div>
            <div class="col-sm-auto text-muted">to</div>
            <div class="col-sm-5">
              <input type="date" class="form-control form-control-sm" id="to_date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" />
            </div>
            <div class="col-sm-auto">
              <button type="submit" class="btn btn-dark btn-sm px-3 rounded-pill">Go</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ CATEGORY CARDS ═══════════════════ --}}
<div class="row g-3 mb-4">
  @foreach($cats as $key => $cat)
  @php $val = $revenueByCategory[$key] ?? 0; $pct = $totalRevenue > 0 ? ($val / $totalRevenue * 100) : 0; @endphp
  <div class="col-md-4 col-sm-6">
    <div class="card rev-card h-100">
      <div class="card-body py-3 px-3">
        <div class="d-flex align-items-center gap-3">
          <div class="icon-wrap" style="background:{{ $cat['bg'] }}; color:{{ $cat['color'] }};">
            <i class="icon-base ri {{ $cat['icon'] }}"></i>
          </div>
          <div class="flex-grow-1 min-w-0">
            <div class="text-muted small fw-semibold uppercase letter-spacing-1" style="font-size:0.65rem;">{{ $cat['label'] }}</div>
            <div class="fw-bold text-dark" style="font-size:1.1rem;">TZS {{ number_format($val) }}</div>
          </div>
          <div class="text-end">
            <span class="fw-bold text-muted small">{{ number_format($pct, 0) }}%</span>
          </div>
        </div>
        <div class="cat-pct-bar mt-2">
          <div class="cat-pct-fill" style="width:{{ $pct }}%;background:{{ $cat['color'] }};"></div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ═══════════════════ PAYMENT METHOD CARDS ═══════════════════ --}}
<div class="row g-3 mb-4">
  @php
    $pmethods = [
      'balance' => ['label'=>'Member Balance', 'icon'=>'ri-wallet-line',           'color'=>'#940000', 'pct'=>$balancePct],
      'cash'    => ['label'=>'Cash',            'icon'=>'ri-money-cny-circle-line', 'color'=>'#28a745', 'pct'=>$cashPct],
      'card'    => ['label'=>'Card',            'icon'=>'ri-bank-card-line',        'color'=>'#0d6efd', 'pct'=>$cardPct],
      'mobile'  => ['label'=>'Mobile Money',   'icon'=>'ri-smartphone-line',       'color'=>'#fd7e14', 'pct'=>$mobilePct],
    ];
  @endphp
  @foreach($pmethods as $key => $pm)
  @if(auth()->user()->role === 'storekeeper' && $key === 'balance') @continue @endif
  <div class="col-md-3 col-6">
    <div class="card h-100 shadow-none border-0" style="background:#f1f3f5; border-radius:12px;">
      <div class="card-body py-3 px-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <i class="icon-base ri {{ $pm['icon'] }}" style="color:{{ $pm['color'] }};"></i>
            <span class="small fw-bold text-muted" style="font-size:0.75rem;">{{ $pm['label'] }}</span>
          </div>
          <span class="badge bg-white text-dark border-0 rounded-pill shadow-sm" style="font-size:0.65rem;">{{ number_format($pm['pct'], 0) }}%</span>
        </div>
        <h5 class="fw-bold mb-1 text-dark">TZS {{ number_format($paymentMethodBreakdown[$key] ?? 0) }}</h5>
        <div style="background:rgba(0,0,0,0.05); border-radius:99px; height:4px;">
          <div style="width:{{ $pm['pct'] }}%;height:100%;border-radius:99px;background:{{ $pm['color'] }};"></div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ═══════════════════ CHARTS ═══════════════════ --}}
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card chart-card h-100">
      <div class="card-header border-bottom bg-transparent py-3">
        <h6 class="mb-0 fw-bold"><i class="icon-base ri ri-pie-chart-2-line me-2 text-primary"></i>Revenue by Category</h6>
      </div>
      <div class="card-body" style="height:300px;">
        <canvas id="categoryChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card chart-card h-100">
      <div class="card-header border-bottom bg-transparent py-3">
        <h6 class="mb-0 fw-bold"><i class="icon-base ri ri-bar-chart-grouped-line me-2 text-success"></i>Payment Method Breakdown</h6>
      </div>
      <div class="card-body" style="height:300px;">
        <canvas id="paymentMethodChart"></canvas>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ TREND CHART ═══════════════════ --}}
@if(isset($dailyTrend) && count($dailyTrend) > 0)
<div class="card chart-card mb-4">
  <div class="card-header border-bottom bg-transparent py-3 d-flex justify-content-between align-items-center">
    <h6 class="mb-0 fw-bold"><i class="icon-base ri ri-line-chart-line me-2 text-danger"></i>Revenue Trend (Daily)</h6>
    <button class="btn btn-xs btn-outline-secondary rounded-pill" onclick="toggleTrendChartType()">
      <i class="icon-base ri ri-swap-line me-1"></i>Toggle Style
    </button>
  </div>
  <div class="card-body">
    <canvas id="trendChart" height="100"></canvas>
  </div>
</div>
@endif

{{-- ═══════════════════ TOP TRANSACTIONS ═══════════════════ --}}
@if(isset($topTransactions) && $topTransactions->count() > 0)
<div class="card chart-card mb-4">
  <div class="card-header border-bottom bg-transparent py-3 d-flex justify-content-between align-items-center">
    <h6 class="mb-0 fw-bold"><i class="icon-base ri ri-trophy-line me-2 text-warning"></i>Top Revenue Transactions</h6>
    <span class="badge bg-label-info rounded-pill">Top {{ $topTransactions->count() }}</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle txn-table mb-0">
      <thead class="table-light">
        <tr>
          <th class="ps-4">#</th>
          <th>ID</th>
          <th>Customer</th>
          <th>Category</th>
          <th>Payment</th>
          <th class="text-end pe-4">Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach($topTransactions as $index => $txn)
        @php
          $rankColors = ['#FFD700','#C0C0C0','#CD7F32'];
          $rankBg     = ['rgba(255,215,0,0.1)','rgba(192,192,192,0.1)','rgba(205,127,50,0.1)'];
          $isTop3 = $index < 3;
          $methodBadges = ['balance'=>'bg-label-danger','cash'=>'bg-label-success','card'=>'bg-label-info','mobile'=>'bg-label-warning'];
          $methodName = $txn->payment_method === 'balance' ? 'Balance' : ($txn->payment_method === 'mobile' ? 'Mobile' : ucfirst($txn->payment_method));
        @endphp
        <tr>
          <td class="ps-4">
            @if($isTop3)
              <div class="rank-badge" style="background:{{ $rankBg[$index] }};color:{{ $rankColors[$index] }}; border:1px solid {{ $rankColors[$index] }};">#{{ $index+1 }}</div>
            @else
              <span class="text-muted small fw-bold">#{{ $index+1 }}</span>
            @endif
          </td>
          <td><code class="text-primary small">{{ $txn->transaction_id }}</code></td>
          <td>
            <div class="fw-bold text-dark small">{{ $txn->customer_name }}</div>
            <div class="text-muted" style="font-size:0.65rem;">{{ $txn->created_at->format('d M, H:i') }}</div>
          </td>
          <td><span class="badge bg-label-secondary small" style="font-size:0.65rem;">{{ ucfirst(str_replace('_',' ',$txn->category)) }}</span></td>
          <td><span class="badge {{ $methodBadges[$txn->payment_method] ?? 'bg-label-secondary' }} small" style="font-size:0.65rem;">{{ $methodName }}</span></td>
          <td class="text-end pe-4">
            <span class="fw-bold text-dark">TZS {{ number_format($txn->amount) }}</span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Quick Date Presets ──
function setDateRange(range) {
  const today = new Date();
  const from  = document.getElementById('from_date');
  const to    = document.getElementById('to_date');
  let f, t;

  switch(range) {
    case 'today':       f = new Date(today); t = new Date(today); break;
    case 'yesterday':   f = new Date(today); f.setDate(f.getDate()-1); t = new Date(f); break;
    case 'this_week':   f = new Date(today); f.setDate(f.getDate()-f.getDay()); t = new Date(today); break;
    case 'last_week':   t = new Date(today); t.setDate(t.getDate()-t.getDay()-1); f = new Date(t); f.setDate(f.getDate()-6); break;
    case 'this_month':  f = new Date(today.getFullYear(), today.getMonth(), 1); t = new Date(today); break;
    case 'last_month':  f = new Date(today.getFullYear(), today.getMonth()-1, 1); t = new Date(today.getFullYear(), today.getMonth(), 0); break;
    case 'last_30_days':f = new Date(today); f.setDate(f.getDate()-30); t = new Date(today); break;
    case 'this_year':   f = new Date(today.getFullYear(), 0, 1); t = new Date(today); break;
  }
  from.value = f.toISOString().split('T')[0];
  to.value   = t.toISOString().split('T')[0];
  document.getElementById('filterForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {

// ── Revenue by Category Doughnut ──
const categoryCtx = document.getElementById('categoryChart');
if (categoryCtx) {
  new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
      labels: [
        'Driving Range', 
        'Equipment Rental', 
        'Equipment Sales', 
        @if(auth()->user()->role !== 'storekeeper') 'Food & Beverage', @endif
        'Ball Management'
      ],
      datasets: [{
        data: [
          {{ $revenueByCategory['driving_range']    ?? 0 }},
          {{ $revenueByCategory['equipment_rental'] ?? 0 }},
          {{ $revenueByCategory['equipment_sales']  ?? 0 }},
          @if(auth()->user()->role !== 'storekeeper') {{ $revenueByCategory['food_beverage']    ?? 0 }}, @endif
          {{ $revenueByCategory['ball_management']  ?? 0 }}
        ],
        backgroundColor: [
          '#0d6efd',
          '#17a2b8',
          '#fd7e14',
          @if(auth()->user()->role !== 'storekeeper') '#28a745', @endif
          '#6f42c1'
        ],
        borderWidth: 2,
        borderColor: '#fff',
        hoverOffset: 12
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '72%',
      plugins: {
        legend: { position: 'right', labels: { padding: 20, boxWidth: 12, font: { size: 11, weight: '600' }, color: '#495057' } },
        tooltip: { backgroundColor: '#212529', bodyFont: { size: 13 }, callbacks: { label: ctx => ' ' + ctx.label + ': TZS ' + ctx.raw.toLocaleString() } }
      }
    }
  });
}

// ── Payment Method Chart ──
const paymentMethodCtx = document.getElementById('paymentMethodChart');
if (paymentMethodCtx) {
  new Chart(paymentMethodCtx, {
    type: 'bar',
    data: {
      labels: ['Balance', 'Cash', 'Card', 'Mobile'],
      datasets: [{
        data: [
          {{ $paymentMethodBreakdown['balance'] ?? 0 }},
          {{ $paymentMethodBreakdown['cash']    ?? 0 }},
          {{ $paymentMethodBreakdown['card']    ?? 0 }},
          {{ $paymentMethodBreakdown['mobile']  ?? 0 }}
        ],
        backgroundColor: ['#940000','#28a745','#0d6efd','#fd7e14'],
        borderRadius: 5,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ' TZS ' + ctx.raw.toLocaleString() } }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.03)' }, ticks: { font: { size: 10 } } },
        x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' } } }
      }
    }
  });
}

// ── Daily Trend Chart ──
@if(isset($dailyTrend) && count($dailyTrend) > 0)
let trendChartType = 'line';
const trendCtx = document.getElementById('trendChart');
let trendChart = null;

function toggleTrendChartType() {
  trendChartType = trendChartType === 'line' ? 'bar' : 'line';
  if (trendChart) trendChart.destroy();
  initTrendChart();
}

function initTrendChart() {
  if (!trendCtx) return;
  const trendData = @json(array_values($dailyTrend));
  trendChart = new Chart(trendCtx, {
    type: trendChartType,
    data: {
      labels: trendData.map(d => { const dt = new Date(d.date); return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }); }),
      datasets: [
        { label: 'Total Revenue', data: trendData.map(d => d.total || 0), borderColor: '#940000', backgroundColor: 'rgba(148,0,0,0.08)', fill: true, tension: 0.4, borderWidth: 3 }
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ' TZS ' + ctx.raw.toLocaleString() } }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.03)' } },
        x: { grid: { display: false } }
      }
    }
  });
}
if (trendCtx) initTrendChart();
@endif
  });
</script>
@endpush
