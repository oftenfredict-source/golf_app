@extends('settings._layout-base')

@section('title', 'Member Reports')
@section('description', 'Member Reports - Golf Club Management System')

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
    margin-bottom: 0.15rem;
  }
  .metric-value {
    font-size: 1.25rem;
    font-weight: 800;
    color: #212529;
    line-height: 1.1;
  }

  /* ── Section Cards ── */
  .report-section-card { border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: none; margin-bottom: 1.5rem; }
  .report-section-card .card-header { background: transparent; padding: 1.25rem; border-bottom: 1px solid #f0f0f0; }

  /* ── Tables & Badges ── */
  .txn-table th { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.6px; font-weight: 700; color: #888; background: #fafafa; }
  .txn-table td { padding: 1rem 0.75rem; vertical-align: middle; }
  .rank-badge { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; }
</style>
@endpush

@section('content')

{{-- ═══════════════════ HEADER ═══════════════════ --}}
<div class="reports-header shadow-sm">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
      <h4 class="mb-1 fw-bold text-dark">Member Reports</h4>
      <div class="d-flex align-items-center gap-3 text-muted small">
        <div class="d-flex align-items-center gap-1">
          <i class="ri-group-line" style="color:#940000;"></i>
          <span class="fw-semibold" style="color:#940000;">{{ number_format($stats['total'] ?? 0) }} Total Members</span>
        </div>
        <div class="text-secondary">|</div>
        <div class="d-flex align-items-center gap-1">
          <i class="ri-checkbox-circle-line text-success"></i>
          <span class="text-success fw-semibold">{{ number_format($stats['active'] ?? 0) }} Active</span>
        </div>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('reports.members.pdf', request()->all()) }}" target="_blank" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
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
  <div class="col-md-4 col-lg-2">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(148,0,0,0.1); color:#940000;">
        <i class="icon-base ri ri-group-line"></i>
      </div>
      <div>
        <div class="metric-label">Total</div>
        <div class="metric-value" style="color:#940000;">{{ number_format($stats['total'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Active --}}
  <div class="col-md-4 col-lg-2">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(40,167,69,0.1); color:#28A745;">
        <i class="icon-base ri ri-checkbox-circle-line"></i>
      </div>
      <div>
        <div class="metric-label">Active</div>
        <div class="metric-value text-success">{{ number_format($stats['active'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Expired --}}
  <div class="col-md-4 col-lg-2">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(255,193,7,0.1); color:#FFC107;">
        <i class="icon-base ri ri-error-warning-line"></i>
      </div>
      <div>
        <div class="metric-label">Expired</div>
        <div class="metric-value text-warning">{{ number_format($stats['expired'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Suspended --}}
  <div class="col-md-4 col-lg-2">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(148,0,0,0.1); color:#940000;">
        <i class="icon-base ri ri-indeterminate-circle-line"></i>
      </div>
      <div>
        <div class="metric-label">Suspended</div>
        <div class="metric-value text-danger">{{ number_format($stats['suspended'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Total Balance --}}
  <div class="col-md-4 col-lg-2">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(0,188,212,0.1); color:#00BCD4;">
        <i class="ri-wallet-3-line"></i>
      </div>
      <div>
        <div class="metric-label">Total Bal</div>
        <div class="metric-value" style="font-size:1.05rem;">{{ number_format($stats['total_balance'] ?? 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Avg Balance --}}
  <div class="col-md-4 col-lg-2">
    <div class="metric-card-clean">
      <div class="metric-icon-circle" style="background:rgba(103,58,183,0.1); color:#673AB7;">
        <i class="ri-bar-chart-2-line"></i>
      </div>
      <div>
        <div class="metric-label">Avg Bal</div>
        <div class="metric-value" style="font-size:1.05rem;">{{ number_format($stats['average_balance'] ?? 0) }}</div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ FILTERS ═══════════════════ --}}
<div class="card report-section-card">
  <div class="card-body py-3">
    <form method="GET" action="{{ route('reports.members') }}" class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label small fw-bold text-muted mb-1">Search Member</label>
        <div class="input-group input-group-sm input-group-merge border rounded-pill overflow-hidden">
          <span class="input-group-text border-0 bg-white ps-3"><i class="ri-search-line"></i></span>
          <input type="text" class="form-control border-0 py-2" name="search" value="{{ request('search') }}" placeholder="Name, Card #, or Member ID..." />
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-bold text-muted mb-1">Status</label>
        <select class="form-select form-select-sm rounded-pill" name="status">
          <option value="">All Status</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
          <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-bold text-muted mb-1">Type</label>
        <select class="form-select form-select-sm rounded-pill" name="membership_type">
          <option value="">All Membership Types</option>
          <option value="standard" {{ request('membership_type') === 'standard' ? 'selected' : '' }}>Standard</option>
          <option value="premium" {{ request('membership_type') === 'premium' ? 'selected' : '' }}>Premium</option>
          <option value="vip" {{ request('membership_type') === 'vip' ? 'selected' : '' }}>VIP</option>
          <option value="guest" {{ request('membership_type') === 'guest' ? 'selected' : '' }}>Guest</option>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-dark btn-sm flex-grow-1 rounded-pill px-4 py-2 fw-bold">Apply Filter</button>
        <a href="{{ route('reports.members') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px;"><i class="ri-refresh-line"></i></a>
      </div>
    </form>
  </div>
</div>

{{-- ═══════════════════ TOP MEMBERS ═══════════════════ --}}
<div class="row g-3 mb-4">
  @if(isset($topMembersByBalance) && $topMembersByBalance->count() > 0)
  <div class="col-md-6">
    <div class="card report-section-card h-100">
      <div class="card-header d-flex align-items-center gap-2 py-3">
        <i class="ri-wallet-3-line text-info"></i>
        <h6 class="mb-0 fw-bold">Top Members by Balance</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-sm txn-table mb-0">
          <thead>
            <tr>
              <th class="ps-4"># Rank</th>
              <th>Member Name</th>
              <th class="text-end pe-4">Current Balance</th>
            </tr>
          </thead>
          <tbody>
            @foreach($topMembersByBalance as $index => $member)
            @php
              $rBg = ['rgba(255,215,0,0.1)','rgba(192,192,192,0.1)','rgba(205,127,50,0.1)'];
              $rCo = ['#FFD700','#9E9E9E','#CD7F32'];
            @endphp
            <tr>
              <td class="ps-4">
                @if($index < 3)
                  <div class="rank-badge" style="background:{{ $rBg[$index] }}; color:{{ $rCo[$index] }}; border:1px solid {{ $rCo[$index] }};">#{{ $index+1 }}</div>
                @else
                  <span class="text-muted small fw-bold ms-2">#{{ $index+1 }}</span>
                @endif
              </td>
              <td>
                <div class="fw-bold text-dark">{{ $member->name }}</div>
                <code class="small" style="font-size:0.65rem;">{{ $member->card_number }}</code>
              </td>
              <td class="text-end pe-4">
                <strong class="text-success">TZS {{ number_format($member->balance) }}</strong>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  @if(isset($topMembersByTransactions) && $topMembersByTransactions->count() > 0)
  <div class="col-md-6">
    <div class="card report-section-card h-100">
      <div class="card-header d-flex align-items-center gap-2 py-3">
        <i class="ri-exchange-box-line text-warning"></i>
        <h6 class="mb-0 fw-bold">Most Active Members</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-sm txn-table mb-0">
          <thead>
            <tr>
              <th class="ps-4"># Rank</th>
              <th>Member Name</th>
              <th class="text-end pe-4">Txns Count</th>
            </tr>
          </thead>
          <tbody>
            @foreach($topMembersByTransactions as $index => $member)
            <tr>
              <td class="ps-4">
                @if($index < 3)
                  <div class="rank-badge" style="background:rgba(103,58,183,0.1); color:#673AB7; border:1px solid #673AB7;">#{{ $index+1 }}</div>
                @else
                  <span class="text-muted small fw-bold ms-2">#{{ $index+1 }}</span>
                @endif
              </td>
              <td>
                <div class="fw-bold text-dark">{{ $member->name }}</div>
                <code class="small" style="font-size:0.65rem;">{{ $member->card_number }}</code>
              </td>
              <td class="text-end pe-4">
                <span class="badge bg-label-primary rounded-pill">{{ number_format($member->transactions_count) }} Txns</span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>

{{-- ═══════════════════ MAIN MEMBERS REGISTER ═══════════════════ --}}
<div class="card report-section-card">
  <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
    <h6 class="mb-0 fw-bold"><i class="ri-user-search-line me-2 text-primary"></i>Member Register</h6>
    <span class="badge bg-label-primary rounded-pill">Total: {{ $members->total() }} Members</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle txn-table mb-0">
      <thead>
        <tr>
          <th class="ps-4">Member ID</th>
          <th>Full Name & Card</th>
          <th>Membership</th>
          <th>Status</th>
          <th class="text-end">Current Bal</th>
          <th class="text-center">Activity</th>
          <th class="ps-3">Expiry Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($members as $member)
        <tr>
          <td class="ps-4">
            <code class="text-primary small fw-bold">{{ $member->member_id }}</code>
          </td>
          <td>
            <div class="fw-bold text-dark">{{ $member->name }}</div>
            <code class="text-muted" style="font-size:0.7rem;">{{ $member->card_number }}</code>
          </td>
          <td>
            @php
              $tpColor = ['vip'=>'bg-label-warning','premium'=>'bg-label-primary','standard'=>'bg-label-info','guest'=>'bg-label-secondary'];
              $tpColor = $tpColor[strtolower($member->membership_type)] ?? 'bg-label-secondary';
            @endphp
            <span class="badge {{ $tpColor }} py-1 px-3" style="font-size:0.65rem;">{{ strtoupper($member->membership_type) }}</span>
          </td>
          <td>
            @if($member->status === 'active')
              <span class="text-success small fw-bold"><i class="ri-checkbox-circle-fill me-1"></i>Active</span>
            @elseif($member->status === 'expired')
              <span class="text-warning small fw-bold"><i class="ri-error-warning-fill me-1"></i>Expired</span>
            @else
              <span class="text-danger small fw-bold"><i class="ri-indeterminate-circle-fill me-1"></i>{{ ucfirst($member->status) }}</span>
            @endif
          </td>
          <td class="text-end">
            <div class="fw-extrabold {{ $member->balance >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.95rem;">
              TZS {{ number_format($member->balance) }}
            </div>
          </td>
          <td class="text-center">
            <div class="d-flex flex-column align-items-center">
              <span class="fw-bold text-dark small">{{ number_format($member->transactions_count ?? 0) }} Txns</span>
              <small class="text-muted" style="font-size:0.65rem;">{{ number_format($member->topups_count ?? 0) }} Top-ups</small>
            </div>
          </td>
          <td class="ps-3">
            @if($member->valid_until)
              <div class="fw-semibold text-dark small">{{ $member->valid_until->format('d M Y') }}</div>
              @if($member->valid_until->isPast())
                <span class="badge bg-label-danger py-0 px-1" style="font-size:0.55rem;">OVERDUE</span>
              @endif
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-5">
            <div class="text-muted opacity-50">
              <i class="ri-group-line" style="font-size:3rem;"></i>
              <p class="mt-2">No members found matching filters</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($members->hasPages())
  <div class="card-footer border-top bg-light py-3">
    {{ $members->links() }}
  </div>
  @endif
</div>

@endsection

@push('scripts')
<script>
</script>
@endpush
