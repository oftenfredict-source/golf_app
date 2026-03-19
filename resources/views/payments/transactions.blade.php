@extends('settings._layout-base')

@section('title', 'Transactions')
@section('description', 'Transactions - Golf Club Management System')

@section('content')
<!-- Remix Icons -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

<style>
  :root {
    --txn-primary: #940000;
    --txn-secondary: #f8f9fa;
    --txn-success: #10b981;
    --txn-danger: #ef4444;
    --txn-warning: #f59e0b;
    --txn-info: #3b82f6;
    --glass-bg: rgba(255, 255, 255, 0.9);
    --glass-border: rgba(255, 255, 255, 0.2);
  }

  .hero-banner-card {
    background: linear-gradient(135deg, #940000 0%, #d40000 100%);
    border-radius: 1.5rem !important;
    overflow: hidden;
    position: relative;
  }

  .hero-banner-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    z-index: 0;
  }

  .premium-stats-card {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    border-radius: 1.25rem !important;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  }

  .premium-stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  }

  .icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
  }

  .category-pill {
    padding: 0.4rem 0.8rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .member-avatar {
    width: 40px;
    height: 40px;
    background: #f0f2f5;
    color: #940000;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-weight: 700;
  }

  .txn-row {
    transition: background 0.2s ease;
    cursor: pointer;
  }

  .txn-row:hover {
    background: #fcfcfc !important;
  }

  .floating-filter-card {
    border-radius: 1.25rem !important;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    margin-top: -30px;
    z-index: 10;
    position: relative;
  }

  .amount-indicator {
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 700;
  }

  .amount-positive { color: var(--txn-success); }
  .amount-negative { color: var(--txn-danger); }

  .filter-active {
    border-color: var(--txn-primary) !important;
    background-color: rgba(148, 0, 0, 0.02) !important;
    box-shadow: 0 0 0 0.2rem rgba(148, 0, 0, 0.1) !important;
  }

  .quick-filter-btn {
    padding: 0.35rem 0.8rem;
    font-size: 0.75rem;
    border-radius: 2rem;
    transition: all 0.2s;
    border: 1px solid #e0e0e0;
    background: white;
    color: #666;
    cursor: pointer;
  }

  .quick-filter-btn:hover, .quick-filter-btn.active {
    background: var(--txn-primary);
    color: white;
    border-color: var(--txn-primary);
  }

  .filter-label-with-icon {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.4rem;
  }

  /* Hero Metric Enhancements */
  .hero-metric-box {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 1.25rem 1.75rem;
    border-radius: 1.25rem;
    display: flex;
    flex-direction: column;
    min-width: 200px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  }

  .hero-metric-box:hover {
    background: rgba(255, 255, 255, 0.18);
    transform: translateY(-4px);
    border-color: rgba(255, 255, 255, 0.3);
  }

  .hero-metric-value {
    font-size: 2.75rem;
    line-height: 1;
    font-weight: 800;
    letter-spacing: -1px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .hero-metric-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    opacity: 0.9;
    margin-top: 0.75rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.9);
  }

  .hero-header-text {
    text-shadow: 0 4px 15px rgba(0,0,0,0.25);
    letter-spacing: -0.5px;
  }
</style>

<!-- Hero Section -->
<div class="card hero-banner-card mb-4 border-0 shadow-lg">
  <div class="card-body p-4 p-md-5">
    <div class="row align-items-center">
      <div class="col-lg-7 text-white position-relative" style="z-index: 2;">
        <h1 class="display-5 fw-bold mb-3 hero-header-text text-white">Financial Monitoring</h1>
        <p class="fs-5 opacity-75 mb-4 max-w-500">Real-time overview of the club's financial activities and member transactions.</p>
        
        <div class="d-flex flex-wrap gap-4 mt-2">
          <div class="hero-metric-box">
            <div class="hero-metric-value text-white">{{ number_format($stats['total'] ?? 0) }}</div>
            <div class="hero-metric-label"><i class="ri-history-line me-1"></i> Transactions</div>
          </div>
          <div class="hero-metric-box" style="background: rgba(255, 255, 255, 0.2);">
            <div class="hero-metric-value text-white">
              <span class="fs-3 opacity-75 fw-normal">TZS</span> {{ number_format($stats['today_revenue'] ?? 0) }}
            </div>
            <div class="hero-metric-label"><i class="ri-calendar-check-line me-1"></i> Revenue Today</div>
          </div>
        </div>
      </div>
      <div class="col-lg-5 d-none d-lg-block text-center position-relative">
        <i class="ri-bank-card-line text-white opacity-25" style="font-size: 10rem;"></i>
      </div>
    </div>
  </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4 g-4 mt-n5">
  <div class="col-md-3 col-6">
    <div class="card premium-stats-card border-0">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="icon-circle bg-label-primary">
            <i class="ri-exchange-funds-line"></i>
          </div>
          <span class="badge bg-label-primary rounded-pill">Total</span>
        </div>
        <small class="text-muted d-block mb-1">Total Volume</small>
        <h4 class="mb-0 fw-bold">TZS {{ number_format($stats['total_amount'] ?? 0) }}</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card premium-stats-card border-0">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="icon-circle bg-label-success">
            <i class="ri-arrow-left-down-line"></i>
          </div>
          <span class="badge bg-label-success rounded-pill">Inflow</span>
        </div>
        <small class="text-muted d-block mb-1">Payments Received</small>
        <h4 class="mb-0 fw-bold text-success">TZS {{ number_format($stats['payments'] ?? 0) }}</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card premium-stats-card border-0">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="icon-circle bg-label-warning">
            <i class="ri-add-circle-line"></i>
          </div>
          <span class="badge bg-label-warning rounded-pill">Top-ups</span>
        </div>
        <small class="text-muted d-block mb-1">Member Deposits</small>
        <h4 class="mb-0 fw-bold text-warning">TZS {{ number_format($stats['topups'] ?? 0) }}</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card premium-stats-card border-0">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="icon-circle bg-label-danger">
            <i class="ri-refund-2-line"></i>
          </div>
          <span class="badge bg-label-danger rounded-pill">Refunds</span>
        </div>
        <small class="text-muted d-block mb-1">Total Reversals</small>
        <h4 class="mb-0 fw-bold text-danger">TZS {{ number_format($stats['refunds'] ?? 0) }}</h4>
      </div>
    </div>
  </div>
</div>

<!-- Filters Section -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <i class="ri-filter-3-line me-2 text-primary fs-4"></i>
      <h5 class="mb-0 fw-bold">Advanced Search & Filters</h5>
    </div>
    <div id="activeFilterBadgeContainer">
      <!-- To be filled by JS -->
    </div>
  </div>
  <div class="card-body p-4">
    <form method="GET" action="{{ route('payments.transactions') }}" id="filterForm">
      <div class="mb-3">
        <label class="form-label small text-muted">Quick Dates</label>
        <div class="d-flex flex-wrap gap-2">
          <button type="button" class="quick-filter-btn" data-preset="today">Today</button>
          <button type="button" class="quick-filter-btn" data-preset="yesterday">Yesterday</button>
          <button type="button" class="quick-filter-btn" data-preset="last_7">Last 7 Days</button>
          <button type="button" class="quick-filter-btn" data-preset="last_30">Last 30 Days</button>
          <button type="button" class="quick-filter-btn" data-preset="this_month">This Month</button>
          <button type="button" class="quick-filter-btn" data-preset="custom">Custom Range</button>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-3">
          <label class="filter-label-with-icon small text-muted">
            <i class="ri-calendar-event-line"></i> Date Range
          </label>
          <div class="input-group">
            <input type="date" class="form-control" id="fromDateInput" name="from_date" value="{{ request('from_date', $fromDate) }}">
            <span class="input-group-text">to</span>
            <input type="date" class="form-control" id="toDateInput" name="to_date" value="{{ request('to_date', $toDate) }}">
          </div>
        </div>
        <div class="col-md-2">
          <label class="filter-label-with-icon small text-muted">
            <i class="ri-exchange-line"></i> Type
          </label>
          <select class="form-select filter-select" name="type">
            <option value="">All Types</option>
            <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payment</option>
            <option value="topup" {{ request('type') === 'topup' ? 'selected' : '' }}>Top-up</option>
            <option value="refund" {{ request('type') === 'refund' ? 'selected' : '' }}>Refund</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="filter-label-with-icon small text-muted">
            <i class="ri-money-dollar-circle-line"></i> Amount Range
          </label>
          <div class="input-group">
            <span class="input-group-text small">Min</span>
            <input type="number" class="form-control" name="min_amount" value="{{ request('min_amount') }}" placeholder="0">
            <span class="input-group-text small">Max</span>
            <input type="number" class="form-control" name="max_amount" value="{{ request('max_amount') }}" placeholder="Any">
          </div>
        </div>
        <div class="col-md-4">
          <label class="filter-label-with-icon small text-muted">
            <i class="ri-search-line"></i> Search Member / Transaction
          </label>
          <div class="premium-input-group d-flex align-items-center px-3 border rounded">
            <i class="ri-user-search-line text-muted me-2"></i>
            <input type="text" name="member_search" class="form-control border-0 bg-transparent p-1" value="{{ request('member_search') }}" placeholder="Name, Card Number, Txn ID...">
          </div>
        </div>
      </div>

      <div class="row g-3 mt-1">
        <div class="col-md-2">
          <label class="filter-label-with-icon small text-muted">
            <i class="ri-price-tag-3-line"></i> Category
          </label>
          <select class="form-select filter-select" name="category">
            <option value="">All Categories</option>
            @php
              $categories = ['driving_range', 'ball_management', 'equipment_rental', 'equipment_sale', 'food_beverage', 'membership', 'other'];
            @endphp
            @foreach($categories as $cat)
              <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="filter-label-with-icon small text-muted">
            <i class="ri-checkbox-circle-line"></i> Status
          </label>
          <select class="form-select filter-select" name="status">
            <option value="">All Statuses</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="filter-label-with-icon small text-muted">
            <i class="ri-bank-card-line"></i> Payment Method
          </label>
          <select class="form-select filter-select" name="payment_method">
            <option value="">All Methods</option>
            <option value="balance" {{ request('payment_method') === 'balance' ? 'selected' : '' }}>Member Balance</option>
            <option value="upi" {{ request('payment_method') === 'upi' ? 'selected' : '' }}>UPI</option>
            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
          </select>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
          <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
            <i class="ri-filter-2-line me-1"></i> Apply Filters
          </button>
          <button type="button" onclick="resetFilters()" class="btn btn-label-secondary px-4 fw-bold">
            <i class="ri-refresh-line me-1"></i> Reset
          </button>
        </div>
      </div>
      
      <hr class="my-4 opacity-50">
      
      <div class="d-flex justify-content-between align-items-center">
        <div class="btn-group">
          <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="ri-download-2-line me-1"></i> Export Data
          </button>
          <ul class="dropdown-menu shadow">
            <li><a class="dropdown-item" href="#" onclick="exportTransactionsCSV()"><i class="ri-file-excel-line me-2 text-success"></i>Export as CSV</a></li>
            <li><a class="dropdown-item" href="#" onclick="exportTransactionsPDF()"><i class="ri-file-pdf-line me-2 text-danger"></i>Export as PDF</a></li>
          </ul>
        </div>
        <small class="text-muted">Currently viewing <strong>{{ $transactions->total() }}</strong> records</small>
      </div>
    </form>
  </div>
</div>

<!-- Transactions Table Section -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
  <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <i class="ri-list-settings-line me-2 text-primary fs-4"></i>
      <h5 class="mb-0 fw-bold">Transaction History</h5>
    </div>
    <div class="d-flex gap-2">
      <span class="badge bg-label-secondary rounded-pill">{{ $transactions->total() }} Records found</span>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th class="ps-4">No. / Txn ID</th>
            <th>Member / Customer</th>
            <th>Category & Type</th>
            <th>Amount</th>
            <th>Balance Window</th>
            <th>Method & Status</th>
            <th class="text-end pe-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($transactions as $txn)
          <tr class="txn-row" onclick="viewTransaction({{ $txn->id }})">
            <td class="ps-4">
              <div class="d-flex flex-column">
                <span class="text-muted small">#{{ $loop->iteration + ($transactions->currentPage()-1) * $transactions->perPage() }}</span>
                <code class="text-primary fw-bold">{{ $txn->transaction_id }}</code>
              </div>
            </td>
            <td>
              <div class="d-flex align-items-center">
                <div class="member-avatar me-3">
                  {{ strtoupper(substr($txn->customer_name, 0, 1)) }}
                </div>
                <div>
                  <div class="fw-bold text-dark">{{ $txn->customer_name }}</div>
                  @if($txn->member)
                    <small class="text-muted"><i class="ri-vip-crown-line me-1 text-warning"></i>{{ $txn->member->card_number }}</small>
                  @else
                    <small class="text-muted"><i class="ri-user-line me-1"></i>Walk-in Customer</small>
                  @endif
                </div>
              </div>
            </td>
            <td>
              <div class="d-flex flex-column">
                <span class="category-pill bg-label-secondary mb-1" style="width: fit-content;">{{ str_replace('_', ' ', $txn->category) }}</span>
                <div class="d-flex align-items-center">
                  @if($txn->type === 'payment')
                    <i class="ri-arrow-down-circle-line text-danger me-1"></i><span class="small text-danger">Payment</span>
                  @elseif($txn->type === 'topup')
                    <i class="ri-arrow-up-circle-line text-success me-1"></i><span class="small text-success">Top-up</span>
                  @else
                    <i class="ri-arrow-left-right-line text-warning me-1"></i><span class="small text-warning">{{ ucfirst($txn->type) }}</span>
                  @endif
                </div>
              </div>
            </td>
            <td>
              <h6 class="mb-0 fw-bold {{ $txn->type === 'payment' ? 'text-danger' : 'text-success' }}">
                {{ $txn->type === 'payment' ? '-' : '+' }}{{ number_format($txn->amount) }}
              </h6>
              <small class="text-muted">TZS</small>
            </td>
            <td>
              @if($txn->member_id)
                <div class="d-flex flex-column">
                  <span class="small text-muted">Before: TZS {{ number_format($txn->balance_before) }}</span>
                  <span class="fw-bold text-primary">After: TZS {{ number_format($txn->balance_after) }}</span>
                </div>
              @else
                <span class="text-muted small">N/A (Cash)</span>
              @endif
            </td>
            <td>
              <div class="d-flex flex-column gap-1">
                <span class="badge bg-label-info rounded-pill" style="width: fit-content;">{{ strtoupper($txn->payment_method) }}</span>
                @if($txn->status === 'completed')
                  <span class="small text-success fw-bold"><i class="ri-checkbox-circle-fill me-1"></i>Completed</span>
                @elseif($txn->status === 'pending')
                  <span class="small text-warning fw-bold"><i class="ri-time-fill me-1"></i>Pending</span>
                @else
                  <span class="small text-danger fw-bold"><i class="ri-close-circle-fill me-1"></i>{{ ucfirst($txn->status) }}</span>
                @endif
              </div>
            </td>
            <td class="text-end pe-4">
              <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-sm btn-icon btn-label-primary rounded-pill shadow-sm" title="View Details">
                  <i class="ri-eye-line"></i>
                </button>
                <a href="{{ route('payments.transactions.receipt', $txn->id) }}" target="_blank" class="btn btn-sm btn-icon btn-label-secondary rounded-pill shadow-sm" title="Print Receipt" onclick="event.stopPropagation()">
                  <i class="ri-printer-line"></i>
                </a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-5">
              <div class="opacity-50">
                <i class="ri-inbox-line ri-4x mb-3 text-muted"></i>
                <h5 class="fw-bold">No Transactions Found</h5>
                <p>Try adjusting your search or filter parameters.</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
    
    <!-- Pagination -->
    @if($transactions->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
      <div class="text-body-secondary small">
        Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} results
      </div>
      <div>
        {{ $transactions->links() }}
      </div>
    </div>
    @endif

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-file-list-line me-2"></i>Transaction Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="transactionDetails">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <a href="#" id="transactionPdfLink" class="btn btn-primary" target="_blank">
          <i class="icon-base ri ri-file-download-line me-1"></i> Download PDF
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function viewTransaction(id) {
  const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
  const detailsDiv = document.getElementById('transactionDetails');
  
  detailsDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
  modal.show();
  
  fetch('{{ route("payments.transactions.show", ":id") }}'.replace(':id', id), {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success && data.transaction) {
      const txn = data.transaction;
      const typeBadge = {
        'payment': `<span class="badge bg-label-danger"><i class="ri-arrow-down-circle-line me-1"></i>Payment</span>`,
        'topup': `<span class="badge bg-label-success"><i class="ri-arrow-up-circle-line me-1"></i>Top-up</span>`,
        'refund': `<span class="badge bg-label-warning"><i class="ri-refund-line me-1"></i>Refund</span>`,
        'transfer': `<span class="badge bg-label-info"><i class="ri-swap-line me-1"></i>Transfer</span>`
      };
      
      const statusBadge = {
        'completed': `<span class="badge bg-label-success rounded-pill"><i class="ri-checkbox-circle-fill me-1"></i>Completed</span>`,
        'pending': `<span class="badge bg-label-warning rounded-pill"><i class="ri-time-fill me-1"></i>Pending</span>`,
        'failed': `<span class="badge bg-label-danger rounded-pill"><i class="ri-close-circle-fill me-1"></i>Failed</span>`,
        'refunded': `<span class="badge bg-label-info rounded-pill"><i class="ri-refund-fill me-1"></i>Refunded</span>`
      };
      
      // Set PDF link
      const pdfLink = document.getElementById('transactionPdfLink');
      if (pdfLink) {
        pdfLink.href = '{{ route("payments.transactions.receipt", ":id") }}'.replace(':id', id);
      }
      
      detailsDiv.innerHTML = `
        <div class="row g-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
              <div>
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Transaction ID</small>
                <code class="text-primary fs-5 fw-bold">${txn.transaction_id}</code>
              </div>
              <div class="text-end">
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Status</small>
                ${statusBadge[txn.status] || txn.status}
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="p-3 border rounded-3 h-100">
              <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted"><i class="ri-user-heart-line me-2"></i>Customer Details</h6>
              <div class="mb-3">
                <small class="text-muted d-block">Full Name</small>
                <div class="fw-bold">${txn.customer_name}</div>
              </div>
              ${txn.member ? `
              <div class="mb-3">
                <small class="text-muted d-block">Member Information</small>
                <div class="badge bg-label-warning me-1">CARD: ${txn.member.card_number}</div>
                <div class="badge bg-label-info">ID: ${txn.member.member_id}</div>
              </div>
              ` : '<div class="alert alert-secondary py-2 small mb-0"><i class="ri-information-line me-1"></i>Walk-in Customer</div>'}
            </div>
          </div>

          <div class="col-md-6">
            <div class="p-3 border rounded-3 h-100">
              <h6 class="fw-bold mb-3 border-bottom pb-2 text-muted"><i class="ri-money-dollar-circle-line me-2"></i>Financial Info</h6>
              <div class="mb-3">
                <small class="text-muted d-block">Transaction Type & Category</small>
                <div class="d-flex align-items-center gap-2">
                  ${typeBadge[txn.type] || txn.type}
                  <span class="badge bg-label-secondary">${txn.category.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                </div>
              </div>
              <div class="mb-3">
                <small class="text-muted d-block">Payment Method</small>
                <div class="fw-bold text-uppercase"><i class="ri-bank-card-line me-1"></i>${txn.payment_method}</div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="card bg-label-primary border-0 shadow-none">
              <div class="card-body p-3">
                <div class="row align-items-center">
                  <div class="col-md-6 border-end">
                    <small class="text-muted d-block">Amount</small>
                    <h3 class="mb-0 fw-bold ${txn.type === 'payment' ? 'text-danger' : 'text-success'}">
                      ${txn.type === 'payment' ? '-' : '+'}TZS ${parseFloat(txn.amount).toLocaleString()}
                    </h3>
                  </div>
                  <div class="col-md-6 ps-4">
                    ${txn.member_id ? `
                      <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Balance Before:</small>
                        <span class="fw-bold">TZS ${parseFloat(txn.balance_before).toLocaleString()}</span>
                      </div>
                      <div class="d-flex justify-content-between">
                        <small class="text-muted">Balance After:</small>
                        <span class="fw-bold text-primary">TZS ${parseFloat(txn.balance_after).toLocaleString()}</span>
                      </div>
                    ` : '<div class="text-center text-muted small">No balance tracking for non-members</div>'}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <small class="text-muted d-block mb-1"><i class="ri-calendar-event-line me-1"></i>Date Created</small>
            <div class="fw-bold">${new Date(txn.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' })}</div>
          </div>
          <div class="col-md-6 text-md-end">
            <small class="text-muted d-block mb-1"><i class="ri-time-line me-1"></i>Time Recorded</small>
            <div class="fw-bold">${new Date(txn.created_at).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' })}</div>
          </div>

          ${txn.notes ? `
          <div class="col-12 mt-3">
            <div class="alert alert-secondary mb-0 border-0">
              <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Staff Notes</small>
              <p class="mb-0 text-dark">${txn.notes}</p>
            </div>
          </div>
          ` : ''}
        </div>
      `;
    } else {
      detailsDiv.innerHTML = '<div class="alert alert-danger">Failed to load transaction details</div>';
    }
  })
  .catch(err => {
    console.error(err);
    detailsDiv.innerHTML = '<div class="alert alert-danger">Error loading transaction details</div>';
  });
}

function exportTransactionsCSV() {
  const form = document.getElementById('filterForm');
  const formData = new FormData(form);
  const params = new URLSearchParams(formData);
  
  window.open('{{ route("payments.transactions") }}?export=1&' + params.toString(), '_blank');
}

function exportTransactionsPDF() {
  // Check if jsPDF is loaded
  if (typeof jspdf === 'undefined') {
    alert('PDF library not loaded. Please refresh the page and try again.');
    return;
  }
  
  // Show loading message
  const loadingOverlay = document.createElement('div');
  loadingOverlay.id = 'pdfLoadingOverlay';
  loadingOverlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);z-index:99999;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;';
  loadingOverlay.innerHTML = '<div style="text-align:center;"><div class="spinner-border text-white mb-3" role="status" style="width:3rem;height:3rem;border-width:0.3em;"></div><p>Generating PDF...</p></div>';
  document.body.appendChild(loadingOverlay);
  
  // Get filter form data
  const form = document.getElementById('filterForm');
  const formData = new FormData(form);
  const params = new URLSearchParams(formData);
  params.append('export_pdf', '1');
  
  // Fetch all transactions matching filters
  fetch('{{ route("payments.transactions") }}?export_pdf=1&' + params.toString(), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (!data.success || !data.transactions) {
      throw new Error('Failed to fetch transactions data');
    }
    
    try {
      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF('l', 'mm', 'a4'); // Landscape
      const pdfWidth = pdf.internal.pageSize.getWidth();
      const pdfHeight = pdf.internal.pageSize.getHeight();
      const margin = 10;
      const pageWidth = pdfWidth - (margin * 2);
      let yPos = margin;
      const rowHeight = 7;
      const headerHeight = 8;
      
      // Header
      pdf.setFillColor(148, 0, 0);
      pdf.rect(margin, yPos, pageWidth, 25, 'F');
      pdf.setTextColor(255, 255, 255);
      pdf.setFontSize(20);
      pdf.text('Golf Club Management System', pdfWidth / 2 + margin, yPos + 10, { align: 'center' });
      pdf.setFontSize(12);
      pdf.text('Transactions Report', pdfWidth / 2 + margin, yPos + 18, { align: 'center' });
      
      yPos += 30;
      pdf.setTextColor(0, 0, 0);
      pdf.setFontSize(9);
      
      // Filter info
      const filters = data.filters || {};
      pdf.text(`Date Range: ${filters.from_date || 'All Time'} to ${filters.to_date || 'Today'}`, margin, yPos);
      pdf.text(`Type: ${filters.type || 'All'} | Category: ${filters.category || 'All'}`, margin, yPos + 5);
      pdf.text(`Total Records: ${data.total || 0} | Generated: ${new Date().toLocaleString()}`, margin, yPos + 10);
      
      yPos += 18;
      
      // Table column widths (sum = pageWidth)
      const colWidths = [28, 32, 15, 22, 20, 28, 18, 22, 18, 15];
      const headers = ['Txn ID', 'Customer', 'Type', 'Category', 'Amount', 'Balance', 'Payment', 'Date', 'Time', 'Status'];
      
      // Helper function to draw table header
      const drawHeader = () => {
        pdf.setFillColor(240, 240, 240);
        pdf.rect(margin, yPos, pageWidth, headerHeight, 'F');
        pdf.setFontSize(8);
        pdf.setFont(undefined, 'bold');
        let xPos = margin;
        headers.forEach((header, index) => {
          pdf.text(header, xPos + 2, yPos + 5);
          xPos += colWidths[index];
        });
        yPos += headerHeight;
        pdf.setFont(undefined, 'normal');
      };
      
      // Draw initial header
      drawHeader();
      
      // Table rows
      data.transactions.forEach((txn, index) => {
        // Check if we need a new page
        if (yPos + rowHeight > pdfHeight - 10) {
          pdf.addPage();
          yPos = margin;
          drawHeader();
        }
        
        // Draw row background (alternating)
        if (index % 2 === 0) {
          pdf.setFillColor(249, 249, 249);
          pdf.rect(margin, yPos, pageWidth, rowHeight, 'F');
        }
        
        // Draw cell borders
        pdf.setDrawColor(200, 200, 200);
        let xPos = margin;
        colWidths.forEach((width) => {
          pdf.rect(xPos, yPos, width, rowHeight, 'S');
          xPos += width;
        });
        
        // Row data
        xPos = margin;
        pdf.setFontSize(7);
        
        // Transaction ID (truncate)
        pdf.text((txn.transaction_id || '').substring(0, 12), xPos + 1, yPos + 4.5);
        xPos += colWidths[0];
        
        // Customer Name (truncate)
        const customerName = (txn.customer_name || '').length > 20 ? (txn.customer_name || '').substring(0, 17) + '...' : (txn.customer_name || '-');
        pdf.text(customerName, xPos + 1, yPos + 4.5);
        xPos += colWidths[1];
        
        // Type
        pdf.text((txn.type || '').toUpperCase().substring(0, 8), xPos + 1, yPos + 4.5);
        xPos += colWidths[2];
        
        // Category
        const category = (txn.category || '').replace('_', ' ').substring(0, 15);
        pdf.text(category, xPos + 1, yPos + 4.5);
        xPos += colWidths[3];
        
        // Amount
        const amount = (txn.type === 'payment' ? '-' : '+') + parseFloat(txn.amount || 0).toLocaleString();
        pdf.text(amount, xPos + 1, yPos + 4.5);
        xPos += colWidths[4];
        
        // Balance
        const balance = txn.balance_after ? parseFloat(txn.balance_after).toLocaleString() : '-';
        pdf.text(balance, xPos + 1, yPos + 4.5);
        xPos += colWidths[5];
        
        // Payment Method
        pdf.text((txn.payment_method || '').toUpperCase().substring(0, 10), xPos + 1, yPos + 4.5);
        xPos += colWidths[6];
        
        // Date
        const date = new Date(txn.created_at);
        pdf.text(date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }), xPos + 1, yPos + 4.5);
        xPos += colWidths[7];
        
        // Time
        pdf.text(date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }), xPos + 1, yPos + 4.5);
        xPos += colWidths[8];
        
        // Status
        pdf.text((txn.status || '').toUpperCase().substring(0, 8), xPos + 1, yPos + 4.5);
        
        yPos += rowHeight;
      });
      
      // Footer
      pdf.setFontSize(8);
      pdf.setTextColor(128, 128, 128);
      pdf.text('Generated by Golf Club Management System - EMCA Technologies', pdfWidth / 2 + margin, pdfHeight - 5, { align: 'center' });
      
      // Generate filename and save
      const fromDateStr = (filters.from_date || '').replace(/-/g, '') || 'ALL';
      const toDateStr = (filters.to_date || '').replace(/-/g, '') || 'TODAY';
      const filename = 'Transactions_Report_' + fromDateStr + '_' + toDateStr + '_' + Date.now() + '.pdf';
      
      pdf.save(filename);
      loadingOverlay.remove();
      
    } catch (err) {
      console.error('PDF generation error:', err);
      loadingOverlay.remove();
      alert('Error generating PDF: ' + (err.message || 'Unknown error') + '. Please try exporting as CSV instead.');
    }
  })
  .catch(err => {
    console.error('Error fetching transactions:', err);
    loadingOverlay.remove();
    alert('Error fetching transactions data: ' + (err.message || 'Unknown error') + '. Please try exporting as CSV instead.');
  });
}

function resetFilters() {
  window.location.href = '{{ route("payments.transactions") }}';
}

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('filterForm');
  const fromDateInput = document.getElementById('fromDateInput');
  const toDateInput = document.getElementById('toDateInput');
  const activeBadgeContainer = document.getElementById('activeFilterBadgeContainer');
  
  // Quick Date Logic
  document.querySelectorAll('.quick-filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const preset = this.dataset.preset;
      const now = new Date();
      let from, to = now;

      document.querySelectorAll('.quick-filter-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      switch(preset) {
        case 'today':
          from = to = now;
          break;
        case 'yesterday':
          from = to = new Date(now.setDate(now.getDate() - 1));
          break;
        case 'last_7':
          from = new Date(now.setDate(now.getDate() - 7));
          to = new Date();
          break;
        case 'last_30':
          from = new Date(now.setDate(now.getDate() - 30));
          to = new Date();
          break;
        case 'this_month':
          from = new Date(now.getFullYear(), now.getMonth(), 1);
          to = new Date();
          break;
        case 'custom':
          return; // Don't change values
      }

      fromDateInput.value = from.toISOString().split('T')[0];
      toDateInput.value = to.toISOString().split('T')[0];
      form.submit();
    });
  });

  // Active Filter Indicators
  function updateActiveFilters() {
    let activeCount = 0;
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
      let isActive = false;
      if (input.name === 'from_date' || input.name === 'to_date') {
        // Only count as active if different from default (not trivial to check here without data from Laravel, 
        // but we can check if they are manually filled)
        isActive = input.value !== '';
      } else if (input.value !== '') {
        isActive = true;
      }

      if (isActive) {
        input.classList.add('filter-active');
        // Closest for premium-input-group
        if (input.closest('.premium-input-group')) {
          input.closest('.premium-input-group').classList.add('filter-active');
        }
        activeCount++;
      } else {
        input.classList.remove('filter-active');
        if (input.closest('.premium-input-group')) {
          input.closest('.premium-input-group').classList.remove('filter-active');
        }
      }
    });

    if (activeCount > 0) {
      activeBadgeContainer.innerHTML = `<span class="badge bg-primary rounded-pill"><i class="ri-flashlight-line me-1"></i>${activeCount} Active Filters</span>`;
    } else {
      activeBadgeContainer.innerHTML = '';
    }
  }

  updateActiveFilters();
});
</script>
@endpush
