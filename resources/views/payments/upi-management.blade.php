@extends('settings._layout-base')

@section('title', 'Member Directory')
@section('description', 'Member Directory - Golf Club Management System')

@section('content')

{{-- Early function definitions so onclick handlers work --}}
<script>
function topupBalance(memberId, memberName, currentBalance) {
  document.getElementById('topup_member_id').value = memberId;
  document.getElementById('topup_member_name').textContent = memberName;
  document.getElementById('topup_current_balance').textContent = 'TZS ' + parseFloat(currentBalance).toLocaleString();
  document.getElementById('topup_amount').value = '';
  document.getElementById('topup_reference').value = '';
  new bootstrap.Modal(document.getElementById('topupModal')).show();
}
function adjustBalance(memberId, memberName, currentBalance) {
  document.getElementById('adjust_member_id').value = memberId;
  document.getElementById('adjust_member_name').textContent = memberName;
  document.getElementById('adjust_current_balance').textContent = 'TZS ' + parseFloat(currentBalance).toLocaleString();
  document.getElementById('adjust_new_balance').value = currentBalance;
  document.getElementById('adjust_reason').value = '';
  new bootstrap.Modal(document.getElementById('adjustBalanceModal')).show();
}
function viewMemberTransactions(memberId, memberName) {
  try {
    const modalEl = document.getElementById('transactionsModal');
    if (!modalEl) { alert('Transactions modal not found.'); return; }
    const modal = new bootstrap.Modal(modalEl);
    const memberNameEl = document.getElementById('transactions_member_name');
    if (memberNameEl) memberNameEl.textContent = memberName || 'Member';
    const pdfLink = document.getElementById('transactionsPdfLink');
    if (pdfLink) pdfLink.href = '{{ route("payments.members.transactions.pdf", ":id") }}'.replace(':id', memberId);
    const transactionsBody = document.getElementById('transactionsTableBody');
    if (!transactionsBody) { alert('Transactions table not found.'); return; }
    transactionsBody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>';
    modal.show();
    fetch('{{ route("payments.members.transactions", ":id") }}'.replace(':id', memberId), {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
      if (data.success && data.transactions) {
        if (!data.transactions.length) {
          transactionsBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No transactions found</td></tr>';
        } else {
          transactionsBody.innerHTML = data.transactions.map(txn => {
            const cat = (txn.category || '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            const amount = parseFloat(txn.amount || 0);
            const balanceAfter = parseFloat(txn.balance_after || 0);
            const typeClass = txn.type === 'payment' ? 'text-danger' : 'text-success';
            const typeSign = txn.type === 'payment' ? '-' : '+';
            const badgeClass = txn.type === 'payment' ? 'bg-label-danger' : (txn.type === 'topup' ? 'bg-label-success' : 'bg-label-warning');
            const badgeText = txn.type === 'payment' ? 'Payment' : (txn.type === 'topup' ? 'Top-up' : 'Refund');
            const date = new Date(txn.created_at);
            return '<tr>' +
              '<td><code>' + (txn.transaction_id || '-') + '</code></td>' +
              '<td><span class="badge ' + badgeClass + '">' + badgeText + '</span></td>' +
              '<td>' + cat + '</td>' +
              '<td><strong class="' + typeClass + '">' + typeSign + 'TZS ' + amount.toLocaleString() + '</strong></td>' +
              '<td>TZS ' + balanceAfter.toLocaleString() + '</td>' +
              '<td>' + date.toLocaleString() + '</td></tr>';
          }).join('');
        }
      } else {
        transactionsBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Error loading transactions</td></tr>';
      }
    })
    .catch(() => { transactionsBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Error loading transactions</td></tr>'; });
  } catch (error) { alert('An error occurred. Please refresh the page.'); }
}
function editMember(memberId) {
  fetch('{{ route("payments.members.show", ":id") }}'.replace(':id', memberId), {
    headers: { 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success && data.member) {
      const m = data.member;
      document.getElementById('edit_member_id').value = m.id;
      document.getElementById('edit_name').value = m.name;
      document.getElementById('edit_email').value = m.email || '';
      document.getElementById('edit_phone').value = m.phone;
      document.getElementById('edit_membership_type').value = m.membership_type || 'standard';
      document.getElementById('edit_status').value = m.status || 'active';
      if (m.valid_until) document.getElementById('edit_valid_until').value = m.valid_until.split(' ')[0];
      
      // Handle radio buttons for Full Access / Golf Only
      if (m.has_full_access) {
          document.getElementById('edit_tier_full_access').checked = true;
      } else {
          document.getElementById('edit_tier_golf_only').checked = true;
      }
      
      document.getElementById('edit_notes').value = m.notes || '';
      new bootstrap.Modal(document.getElementById('editMemberModal')).show();
    }
  })
  .catch(() => showError('Error loading member details'));
}
window.topupBalance = topupBalance;
window.adjustBalance = adjustBalance;
window.viewMemberTransactions = viewMemberTransactions;
window.editMember = editMember;
</script>

<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Payments /</span> Member Directory
</h4>

{{-- ============================================================ --}}
{{-- HERO STATS BANNER (Ball Management style) --}}
{{-- ============================================================ --}}
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="card-body p-0">
        <div class="row g-0">

          {{-- Left: membership breakdown --}}
          <div class="col-md-8 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0 fw-bold"><i class="ri ri-team-line me-2 text-primary"></i>Member Overview</h5>
              <div class="d-flex gap-2">
                @if(auth()->user()->role !== 'storekeeper')
                <a href="{{ route('payments.generate-card') }}" class="btn btn-sm btn-outline-secondary">
                  <i class="ri ri-qr-code-line me-1"></i> Generate Card
                </a>
                @endif
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newUPIModal">
                  <i class="ri ri-user-add-line me-1"></i> Register Member
                </button>
              </div>
            </div>

            @php
              $statTotal   = $stats['total_accounts'] ?? 0;
              $statActive  = $stats['active_accounts'] ?? 0;
              $statStd     = $stats['standard_count'] ?? 0;
              $statVip     = $stats['vip_count'] ?? 0;
              $statPremier = $stats['premier_count'] ?? 0;
              $activePct   = $statTotal > 0 ? ($statActive / $statTotal) * 100 : 0;
              $stdPct      = $statTotal > 0 ? ($statStd / $statTotal) * 100 : 0;
              $vipPct      = $statTotal > 0 ? ($statVip / $statTotal) * 100 : 0;
              $premierPct  = $statTotal > 0 ? ($statPremier / $statTotal) * 100 : 0;
            @endphp

            {{-- Membership type distribution --}}
              <div class="progress mb-4" style="height: 42px; border-radius: 12px; background-color: #f1f3f9;">
              <div class="progress-bar bg-label-secondary" role="progressbar" style="width: {{ $stdPct }}%; color: #495057; font-weight: 700;">
                <span class="fw-bold">{{ $statStd }} Standard</span>
              </div>
              <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $vipPct }}%">
                <span class="fw-bold" style="color: #495057;">{{ $statVip }} VIP</span>
              </div>
              <div class="progress-bar bg-dark" role="progressbar" style="width: {{ $premierPct }}%">
                <span class="fw-bold">{{ $statPremier }} Premier</span>
              </div>
            </div>

            <div class="row g-3 g-md-4">
              <div class="col-6 col-md-3">
                <div class="p-2 p-md-3 border rounded bg-light-subtle h-100">
                  <small class="text-muted d-block mb-1 small">Total Members</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($statTotal) }}</h4>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="p-2 p-md-3 border border-success rounded bg-success-subtle h-100">
                  <small class="text-success d-block mb-1 small">Active</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($statActive) }}</h4>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="p-2 p-md-3 border border-warning rounded h-100" style="background:#fff2cc;">
                  <small class="text-warning d-block mb-1 small">VIP</small>
                  <h4 class="mb-0 fw-bold text-warning">{{ number_format($statVip) }}</h4>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="p-2 p-md-3 border border-dark rounded bg-dark text-white h-100">
                  <small class="text-white-50 d-block mb-1 small">Premier</small>
                  <h4 class="mb-0 fw-bold text-white">{{ number_format($statPremier) }}</h4>
                </div>
              </div>
            </div>
          </div>

          {{-- Right: balance summary --}}
          <div class="col-md-4 bg-primary text-white p-4">
            <h5 class="text-white fw-bold mb-4">Balance Overview</h5>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>Active Members</span>
                <strong>{{ $statActive }}</strong>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-white" style="width: {{ $statTotal > 0 ? ($statActive / $statTotal) * 100 : 0 }}%"></div>
              </div>
            </div>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>Low Balance Alerts</span>
                <strong class="text-warning">{{ $stats['low_balance_count'] ?? 0 }}</strong>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: {{ $statTotal > 0 ? (($stats['low_balance_count'] ?? 0) / $statTotal) * 100 : 0 }}%"></div>
              </div>
            </div>
            <div class="pt-2">
              <div class="alert bg-white text-primary border-0 mb-0 py-3">
                <small class="d-block fw-semibold mb-1">Total Collective Balance</small>
                <h4 class="mb-0 fw-bold fs-5 fs-md-4">TZS {{ number_format($stats['total_balance'] ?? 0, 0) }}</h4>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================ --}}
{{-- MEMBER TABLE (full-width with filter bar) --}}
{{-- ============================================================ --}}
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm">

      {{-- Table header --}}
      <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 py-3" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%);">
        <div class="d-flex align-items-center gap-3">
          <h5 class="mb-0 text-white fw-bold"><i class="ri ri-bank-card-line me-2"></i>Member Cards Directory</h5>
          <span class="badge bg-white text-danger fw-bold">{{ $members->total() }} members</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('payments.upi-management', array_merge(request()->all(), ['export' => '1'])) }}" class="btn btn-xs btn-md-sm btn-outline-light">
            <i class="ri ri-download-line me-1"></i>Export
          </a>
          @if(auth()->user()->role !== 'storekeeper')
          <a href="{{ route('payments.generate-card') }}" class="btn btn-xs btn-md-sm btn-outline-light">
            <i class="ri ri-qr-code-line me-1"></i>ID Cards
          </a>
          @endif
          <button class="btn btn-xs btn-md-sm btn-light fw-bold" data-bs-toggle="modal" data-bs-target="#newUPIModal">
            <i class="ri ri-user-add-line me-1"></i>Register Member
          </button>
        </div>
      </div>

      {{-- Filter bar --}}
      <div class="card-body p-3 border-bottom bg-light-subtle">
        <form method="GET" action="{{ route('payments.upi-management') }}" id="filterForm">
          <div class="row g-2 align-items-center">
            {{-- Search --}}
            <div class="col-12 col-md-4">
              <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                  <i class="ri ri-search-2-line text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-0" name="search" id="search_upi"
                       placeholder="Search name, card #, phone, email…" value="{{ request('search') }}" autocomplete="off">
                @if(request('search'))
                <a href="{{ route('payments.upi-management', request()->except('search')) }}"
                   class="input-group-text bg-white border-start-0 text-danger">
                  <i class="ri ri-close-line"></i>
                </a>
                @endif
              </div>
            </div>
            {{-- Status --}}
            <div class="col-6 col-md-2">
              <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
              </select>
            </div>
            {{-- Membership --}}
            <div class="col-6 col-md-2">
              <select class="form-select form-select-sm" name="membership_type" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="standard" {{ request('membership_type') == 'standard' ? 'selected' : '' }}>Standard</option>
                <option value="vip" {{ request('membership_type') == 'vip' ? 'selected' : '' }}>VIP</option>
                <option value="premier" {{ request('membership_type') == 'premier' ? 'selected' : '' }}>Premier</option>
              </select>
            </div>
            {{-- Tier Filter (Full vs Golf) --}}
            <div class="col-6 col-md-2">
              <select class="form-select form-select-sm" name="tier" onchange="this.form.submit()">
                <option value="">All Tiers</option>
                <option value="1" {{ request('tier') === '1' ? 'selected' : '' }}>Full Access</option>
                <option value="0" {{ request('tier') === '0' ? 'selected' : '' }}>Golf Only</option>
              </select>
            </div>
            {{-- Sort --}}
            <div class="col-6 col-md-2">
              <select class="form-select form-select-sm" name="sort_by" onchange="this.form.submit()">
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Newest First</option>
                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name A–Z</option>
                <option value="balance" {{ request('sort_by') == 'balance' ? 'selected' : '' }}>By Balance</option>
                <option value="card_number" {{ request('sort_by') == 'card_number' ? 'selected' : '' }}>By Card #</option>
              </select>
            </div>
            {{-- Submit --}}
            <div class="col-12 d-md-none mt-2">
              <button type="submit" class="btn btn-primary w-100 fw-bold">
                <i class="ri ri-search-line me-1"></i>Apply Filters
              </button>
            </div>
          </div>
        </form>
        {{-- Active filter tags --}}
        @if(request()->hasAny(['search','status','membership_type']))
        <div class="d-flex align-items-center gap-2 flex-wrap mt-3">
          <small class="text-muted fw-semibold">Filters:</small>
          @if(request('search'))
            <span class="badge bg-label-primary rounded-pill py-1 px-3">
              "{{ request('search') }}"
              <a href="{{ route('payments.upi-management', request()->except('search')) }}" class="ms-1 text-primary text-decoration-none">&times;</a>
            </span>
          @endif
          @if(request('status'))
            <span class="badge bg-label-success rounded-pill py-1 px-3">
              {{ ucfirst(request('status')) }}
              <a href="{{ route('payments.upi-management', request()->except('status')) }}" class="ms-1 text-success text-decoration-none">&times;</a>
            </span>
          @endif
          @if(request('membership_type'))
            <span class="badge bg-label-warning rounded-pill py-1 px-3">
              {{ strtoupper(request('membership_type')) }}
              <a href="{{ route('payments.upi-management', request()->except('membership_type')) }}" class="ms-1 text-warning text-decoration-none">&times;</a>
            </span>
          @endif
          @if(request()->has('tier') && request('tier') !== '')
            <span class="badge bg-label-info rounded-pill py-1 px-3">
              {{ request('tier') == '1' ? 'FULL ACCESS' : 'GOLF ONLY' }}
              <a href="{{ route('payments.upi-management', request()->except('tier')) }}" class="ms-1 text-info text-decoration-none">&times;</a>
            </span>
          @endif
          <a href="{{ route('payments.upi-management') }}" class="ms-auto btn btn-sm btn-label-danger py-0 px-2">
            <i class="ri ri-close-circle-line me-1"></i>Clear All
          </a>
        </div>
        @endif
      </div>

      {{-- Table --}}
      <div class="card-body p-0">
        @php $maxBalance = max(1, (float)$members->max('balance')); @endphp
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="membersTable">
            <thead style="background: #fafafa;">
              <tr>
                <th class="ps-4 text-uppercase small fw-bold text-muted" style="width:150px;">Card</th>
                <th class="text-uppercase small fw-bold text-muted">Member</th>
                <th class="text-uppercase small fw-bold text-muted">Contact</th>
                <th class="text-uppercase small fw-bold text-muted" style="width:160px;">Balance</th>
                <th class="text-center text-uppercase small fw-bold text-muted" style="width:110px;">Status</th>
                <th class="text-uppercase small fw-bold text-muted" style="width:120px;">Joined</th>
                <th class="pe-4 text-uppercase small fw-bold text-muted" style="width:155px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($members ?? [] as $member)
              @php
                $cardNum      = (int) $member->card_number;
                $isReserved   = $cardNum >= 1 && $cardNum <= 20;
                $isVip        = $member->membership_type === 'vip';
                $isPremier    = $member->membership_type === 'premier';
                $balancePct   = min(100, ($member->balance / $maxBalance) * 100);
                $balanceBar   = $member->balance <= 0 ? 'bg-danger' : ($member->balance < 10000 ? 'bg-warning' : 'bg-success');
                $rowBorder    = $member->has_full_access ? 'border-start border-primary border-3' : 'border-start border-warning border-3';
              @endphp
              <tr class="member-row {{ $rowBorder }}" data-id="{{ $member->id }}">

                {{-- Styled card chip --}}
                <td class="ps-4 py-3">
                  @if($member->card_number)
                    <div class="mc-chip {{ $isVip ? 'mc-vip' : ($isPremier ? 'mc-premier' : 'mc-standard') }}">
                      <div class="mc-chip-inner">
                        <div class="mc-dots"><span></span><span></span><span></span></div>
                        <div class="mc-number">{{ $member->card_number }}</div>
                        <div class="mc-label">
                          @if($isVip) VIP @elseif($isPremier) PREMIER @else STANDARD @endif
                        </div>
                      </div>
                    </div>
                  @else
                    <div class="d-flex flex-column align-items-center justify-content-center p-2 border border-dashed rounded bg-light" style="width: 120px; height: 65px; opacity: 0.7;">
                      <i class="ri ri-ghost-line text-muted mb-1"></i>
                      <small class="text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.05em;">GOLF ONLY</small>
                    </div>
                  @endif
                </td>

                {{-- Merged identity cell --}}
                <td class="py-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3 flex-shrink-0">
                      <div class="avatar-initial rounded-circle fw-bold {{ $isVip ? 'bg-warning text-dark' : ($isPremier ? 'bg-dark text-white' : 'bg-label-primary') }}">
                        {{ strtoupper(substr($member->name, 0, 2)) }}
                      </div>
                    </div>
                    <div class="min-width-0">
                      <div class="fw-bold text-truncate">{{ $member->name }}</div>
                      <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                        @if($member->has_full_access)
                          @if($isVip)
                            <span class="badge bg-warning text-dark" style="font-size:10px;"><i class="ri ri-vip-crown-2-line"></i> VIP</span>
                          @elseif($isPremier)
                            <span class="badge bg-dark" style="font-size:10px;"><i class="ri ri-vip-diamond-line"></i> PREMIER</span>
                          @else
                            <span class="badge bg-label-secondary" style="font-size:10px;">STANDARD</span>
                          @endif
                        @endif
                        @if($member->has_full_access)
                          <span class="badge bg-label-info text-uppercase" style="font-size:10px;"><i class="ri ri-id-card-line"></i> FULL ACCESS</span>
                        @else
                          <span class="badge bg-label-warning text-uppercase" style="font-size:10px;"><i class="ri ri-ghost-line"></i> GOLF ONLY</span>
                        @endif
                        @if($member->member_id)
                          <small class="text-muted"><i class="ri ri-fingerprint-line"></i> {{ $member->member_id }}</small>
                        @endif
                      </div>
                      @if($member->email)
                        <small class="text-muted d-block text-truncate" style="max-width:200px;">
                          <i class="ri ri-mail-line"></i> {{ $member->email }}
                        </small>
                      @endif
                    </div>
                  </div>
                </td>

                {{-- Contact --}}
                <td class="py-3">
                  <div class="fw-medium"><i class="ri ri-phone-line me-1 text-muted"></i>{{ $member->phone }}</div>
                  @if($member->valid_until)
                    <small class="text-muted">
                      <i class="ri ri-calendar-check-line me-1"></i>Until {{ \Carbon\Carbon::parse($member->valid_until)->format('M Y') }}
                    </small>
                  @endif
                </td>

                {{-- Balance + mini progress bar --}}
                <td class="py-3">
                  <div class="fw-bold mb-1 {{ $member->balance > 0 ? 'text-success' : 'text-danger' }}">
                    TZS {{ number_format($member->balance, 0) }}
                  </div>
                  <div class="progress mb-1" style="height:4px; border-radius:4px; background:#e9ecef;">
                    <div class="progress-bar {{ $balanceBar }}" style="width:{{ $balancePct }}%; transition:width .4s;"></div>
                  </div>
                  @if($member->balance <= 0)
                    <small class="text-danger fw-semibold"><i class="ri ri-alert-line"></i> Empty</small>
                  @elseif($member->balance < 10000)
                    <small class="text-warning fw-semibold"><i class="ri ri-alert-line"></i> Low</small>
                  @endif
                </td>

                {{-- Status --}}
                <td class="text-center py-3">
                  @if($member->status === 'active')
                    <span class="badge bg-label-success rounded-pill px-3 py-1">
                      <i class="ri ri-checkbox-circle-line me-1"></i>Active
                    </span>
                  @elseif($member->status === 'suspended')
                    <span class="badge bg-label-danger rounded-pill px-3 py-1">
                      <i class="ri ri-forbid-line me-1"></i>Suspended
                    </span>
                  @else
                    <span class="badge bg-label-warning rounded-pill px-3 py-1">{{ ucfirst($member->status) }}</span>
                  @endif
                </td>

                {{-- Joined --}}
                <td class="py-3">
                  <div class="small fw-semibold">{{ $member->created_at->format('d M Y') }}</div>
                  <small class="text-muted">{{ $member->created_at->diffForHumans() }}</small>
                </td>

                {{-- Actions: Top-up button + overflow dropdown --}}
                <td class="pe-4 py-3">
                  <div class="d-flex align-items-center gap-1">
                    @if(auth()->user()->role !== 'storekeeper')
                    <button class="btn btn-sm btn-success fw-bold px-2"
                            onclick="topupBalance({{ $member->id }}, {{ json_encode($member->name) }}, {{ $member->balance }})">
                      <i class="ri ri-add-circle-line me-1"></i>Top-up
                    </button>
                    @endif
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary px-2" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri ri-more-2-line"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-1" style="min-width:170px;">
                        @if(auth()->user()->role === 'admin')
                        <li>
                          <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#"
                             onclick="adjustBalance({{ $member->id }}, {{ json_encode($member->name) }}, {{ $member->balance }}); return false;">
                            <i class="ri ri-edit-line text-info fs-6"></i><span>Adjust Balance</span>
                          </a>
                        </li>
                        @endif
                        <li>
                          <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#"
                             onclick="viewMemberTransactions({{ $member->id }}, {{ json_encode($member->name) }}); return false;">
                            <i class="ri ri-history-line text-primary fs-6"></i><span>Transactions</span>
                          </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                          <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#"
                             onclick="editMember({{ $member->id }}); return false;">
                            <i class="ri ri-pencil-line text-secondary fs-6"></i><span>Edit Member</span>
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                             href="{{ route('payments.generate-card') }}" target="_blank">
                            <i class="ri ri-qr-code-line text-success fs-6"></i><span>Print Card</span>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="ri ri-user-search-line ri-3x d-block mb-3 opacity-25"></i>
                  <p class="mb-1 fw-semibold fs-6">No members found</p>
                  <small>Try a different search term or adjust the filters above.</small>
                  @if(request()->hasAny(['search','status','membership_type','balance_min','balance_max']))
                    <br>
                    <a href="{{ route('payments.upi-management') }}" class="btn btn-sm btn-primary mt-3">
                      <i class="ri ri-refresh-line me-1"></i>Clear All Filters
                    </a>
                  @endif
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light-subtle">
          <small class="text-muted">
            @if($members->hasPages())
              Showing <strong>{{ $members->firstItem() }}</strong>–<strong>{{ $members->lastItem() }}</strong>
              of <strong>{{ $members->total() }}</strong> members
            @else
              <strong>{{ $members->total() }}</strong> member(s)
            @endif
          </small>
          @if($members->hasPages())
            {{ $members->links() }}
          @endif
        </div>
      </div>
    </div>
  </div>
</div>


{{-- ============================================================ --}}
{{-- MODALS --}}
{{-- ============================================================ --}}

{{-- Register New Member --}}
<div class="modal fade" id="newUPIModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0 py-4" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%);">
        <h5 class="modal-title text-white fw-bold"><i class="ri ri-user-add-line me-2"></i>Register New Member</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="newMemberForm" method="POST" action="#" onsubmit="return false;">
        @csrf
        <div class="modal-body p-4">
          <!-- Member Tier Selection (Primary) -->
          <div class="mb-6 p-4 border rounded bg-light shadow-sm">
            <h6 class="fw-bold mb-3"><i class="ri ri-id-card-line me-1"></i> Selection of Member Tier *</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-check custom-option custom-option-icon border rounded p-3 {{ auth()->user()->role === 'storekeeper' ? 'opacity-50 cursor-not-allowed' : '' }}">
                  <label class="form-check-label custom-option-content" for="tier_full_access">
                    <span class="custom-option-body">
                      <i class="ri ri-bank-card-line icon-24px mb-2 text-primary"></i>
                      <span class="custom-option-title d-block fw-bold mb-1">Cardholder Member</span>
                      <small>Full Access to all services including F&B and VIP Gates.</small>
                    </span>
                    <input name="has_full_access" class="form-check-input" type="radio" value="1" id="tier_full_access" {{ auth()->user()->role === 'storekeeper' ? 'disabled' : 'checked' }} />
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check custom-option custom-option-icon border rounded p-3">
                  <label class="form-check-label custom-option-content" for="tier_golf_only">
                    <span class="custom-option-body">
                      <i class="ri ri-ghost-line icon-24px mb-2 text-warning"></i>
                      <span class="custom-option-title d-block fw-bold mb-1">Custom Member</span>
                      <small>Golf Only. Access restricted to golf gates. No card issued.</small>
                    </span>
                    <input name="has_full_access" class="form-check-input" type="radio" value="0" id="tier_golf_only" {{ auth()->user()->role === 'storekeeper' ? 'checked' : '' }} />
                  </label>
                </div>
              </div>
            </div>
            @if(auth()->user()->role === 'storekeeper')
            <div class="alert alert-info py-2 mt-3 small mb-0">
              <i class="ri ri-information-line me-1"></i> Storekeepers can only register Custom (Golf Only) members.
            </div>
            @endif
          </div>
          <div class="row g-4">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="new_customer_name" name="name" placeholder="Full Name" required>
                <label>Full Name *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="tel" class="form-control" id="new_phone" name="phone" placeholder="Phone Number" required>
                <label>Phone Number *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="email" class="form-control" id="new_email" name="email" placeholder="Email Address">
                <label>Email Address</label>
              </div>
            </div>
            <div class="col-md-6 full-access-only">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="new_membership_type" name="membership_type">
                  <option value="standard">STANDARD (Silver Card)</option>
                  <option value="vip">VIP (Gold Card)</option>
                  <option value="premier">PREMIER (Black Card)</option>
                </select>
                <label>Membership Type</label>
              </div>
            </div>
            <div class="col-md-6 full-access-only">
              <div class="form-floating form-floating-outline">
                <input type="number" step="1000" class="form-control" id="new_initial_balance" name="initial_balance" placeholder="0" value="0" min="0">
                <label>Initial Balance (TZS)</label>
              </div>
            </div>
            <div class="col-md-6 full-access-only">
              <div class="form-floating form-floating-outline">
                <input type="date" class="form-control" id="new_valid_until" name="valid_until">
                <label>Valid Until (leave empty for 1 year)</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="new_notes" name="notes" style="height:80px" placeholder="Notes"></textarea>
                <label>Notes</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light py-3">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold">
            <i class="ri ri-user-add-line me-1"></i> Register Member
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Top-up Modal --}}
<div class="modal fade" id="topupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0 py-4" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%);">
        <h5 class="modal-title text-white fw-bold"><i class="ri ri-add-circle-line me-2"></i>Top-up Balance</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="topupForm">
        @csrf
        <input type="hidden" id="topup_member_id" name="member_id">
        <div class="modal-body p-4">
          <div class="card bg-label-primary border-0 mb-4">
            <div class="card-body p-3">
              <h6 class="mb-1 fw-bold" id="topup_member_name">-</h6>
              <small class="text-primary">Current Balance: <span id="topup_current_balance" class="fw-bold">TZS 0</span></small>
            </div>
          </div>
          <div class="form-floating form-floating-outline mb-3">
            <input type="number" step="1000" class="form-control" id="topup_amount" name="amount" min="1000" required placeholder="0">
            <label>Top-up Amount (TZS) *</label>
          </div>
          <div class="form-floating form-floating-outline mb-3">
            <select class="form-select" id="topup_payment_method" name="payment_method" required>
              <option value="cash">CASH</option>
              <option value="mobile_money">MOBILE MONEY</option>
              <option value="bank_transfer">BANK TRANSFER</option>
              <option value="card">CARD (POS)</option>
            </select>
            <label>Payment Method *</label>
          </div>
          <div class="form-floating form-floating-outline mb-3">
            <input type="text" class="form-control" id="topup_reference" name="reference_number" placeholder="Optional">
            <label>Reference Number</label>
          </div>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="topup_send_sms" name="send_sms" checked>
            <label class="form-check-label" for="topup_send_sms"><i class="ri ri-message-2-line me-1"></i>Send SMS notification</label>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light py-3">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success px-4 fw-bold"><i class="ri ri-add-circle-line me-1"></i>Top-up Balance</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Adjust Balance Modal --}}
<div class="modal fade" id="adjustBalanceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-label-warning">
        <h5 class="modal-title fw-bold"><i class="ri ri-edit-line me-2"></i>Adjust Balance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="adjustBalanceForm">
        @csrf
        <input type="hidden" id="adjust_member_id" name="member_id">
        <div class="modal-body p-4">
          <div class="alert alert-warning mb-4 py-2">
            <strong id="adjust_member_name">-</strong><br>
            Current: <span id="adjust_current_balance" class="fw-bold text-primary">TZS 0</span>
          </div>
          <div class="form-floating form-floating-outline mb-3">
            <input type="number" step="0.01" class="form-control" id="adjust_new_balance" name="new_balance" min="0" required placeholder="0">
            <label>New Balance (TZS) *</label>
          </div>
          <div class="form-floating form-floating-outline">
            <textarea class="form-control" id="adjust_reason" name="reason" rows="3" required placeholder="Reason"></textarea>
            <label>Reason for Adjustment *</label>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light py-3">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning fw-bold">Adjust Balance</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Transactions Modal --}}
<div class="modal fade" id="transactionsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-label-secondary">
        <h5 class="modal-title fw-bold"><i class="ri ri-history-line me-2"></i>Transaction History — <span id="transactions_member_name"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Transaction ID</th><th>Type</th><th>Category</th><th>Amount</th><th>Balance After</th><th>Date &amp; Time</th>
              </tr>
            </thead>
            <tbody id="transactionsTableBody">
              <tr><td colspan="6" class="text-center py-4">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <a href="#" id="transactionsPdfLink" class="btn btn-primary" target="_blank">
          <i class="ri ri-file-pdf-line me-1"></i> Download PDF
        </a>
      </div>
    </div>
  </div>
</div>

{{-- Edit Member Modal --}}
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-label-secondary">
        <h5 class="modal-title fw-bold"><i class="ri ri-user-settings-line me-2"></i>Edit Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editMemberForm">
        @csrf
        <input type="hidden" id="edit_member_id" name="member_id">
        <div class="modal-body p-4">
          <!-- Member Tier Selection (Edit) -->
          <div class="mb-6 p-4 border rounded bg-light shadow-sm">
            <h6 class="fw-bold mb-3"><i class="ri ri-id-card-line me-1"></i> Update Member Tier *</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-check custom-option custom-option-icon border rounded p-3">
                  <label class="form-check-label custom-option-content" for="edit_tier_full_access">
                    <span class="custom-option-body">
                      <i class="ri ri-bank-card-line icon-24px mb-2 text-primary"></i>
                      <span class="custom-option-title d-block fw-bold mb-1">Cardholder Member</span>
                      <small>Full Access</small>
                    </span>
                    <input name="has_full_access" class="form-check-input" type="radio" value="1" id="edit_tier_full_access" />
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check custom-option custom-option-icon border rounded p-3">
                  <label class="form-check-label custom-option-content" for="edit_tier_golf_only">
                    <span class="custom-option-body">
                      <i class="ri ri-ghost-line icon-24px mb-2 text-warning"></i>
                      <span class="custom-option-title d-block fw-bold mb-1">Custom Member</span>
                      <small>Golf Only</small>
                    </span>
                    <input name="has_full_access" class="form-check-input" type="radio" value="0" id="edit_tier_golf_only" />
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="row g-4">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="edit_name" name="name" required placeholder="Name">
                <label>Full Name *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="tel" class="form-control" id="edit_phone" name="phone" required placeholder="Phone">
                <label>Phone Number *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="email" class="form-control" id="edit_email" name="email" placeholder="Email">
                <label>Email</label>
              </div>
            </div>
            <div class="col-md-6 edit-full-access-only">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="edit_membership_type" name="membership_type">
                  <option value="standard">STANDARD</option>
                  <option value="vip">VIP (Gold)</option>
                  <option value="premier">PREMIER (Black)</option>
                </select>
                <label>Membership Type</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="edit_status" name="status">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="suspended">Suspended</option>
                </select>
                <label>Status</label>
              </div>
            </div>
            <div class="col-md-6 edit-full-access-only">
              <div class="form-floating form-floating-outline">
                <input type="date" class="form-control" id="edit_valid_until" name="valid_until">
                <label>Valid Until</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="edit_notes" name="notes" rows="2" style="height:70px"></textarea>
                <label>Notes</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light py-3">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold">Update Member</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('styles')
<style>
.bg-label-primary   { background-color: #e7e7ff !important; color: #696cff !important; }
.bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
.bg-label-success   { background-color: #e8fadf !important; color: #71dd37 !important; }
.bg-label-info      { background-color: #d7f5fc !important; color: #03c3ec !important; }
.bg-label-warning   { background-color: #fff2d6 !important; color: #ffab00 !important; }
.bg-label-danger    { background-color: #ffe5e5 !important; color: #ff3e1d !important; }
.bg-success-subtle  { background-color: #d1f2c0 !important; }
.bg-warning-subtle  { background-color: #fff2cc !important; }
</style>
@endpush

@push('scripts')
<script>
// Update balance in table row after topup/adjust
function updateMemberBalanceInTable(memberId, newBalance) {
  document.querySelectorAll('.member-row').forEach(row => {
    if (row.dataset.id == memberId) {
      const cell = row.querySelector('td:nth-child(6)');
      if (cell) {
        const v = parseFloat(newBalance);
        const cls = v > 0 ? 'text-success' : 'text-danger';
        const low = v <= 0 ? '<br><small class="text-danger">Low balance</small>' : (v < 10000 ? '<br><small class="text-warning">Low</small>' : '');
        cell.innerHTML = '<strong class="' + cls + '">TZS ' + v.toLocaleString('en-US', {minimumFractionDigits:0}) + '</strong>' + low;
      }
    }
  });
}

// New member form
document.addEventListener('DOMContentLoaded', function() {
  const newMemberForm = document.getElementById('newMemberForm');
  if (newMemberForm) {
    newMemberForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch('{{ route("payments.members.store") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
      })
      .then(async r => {
        let data;
        try { data = await r.json(); } catch(e) { showError('Invalid server response.'); return; }
        if (!r.ok) {
          if (data.errors) {
            showError('<ul style="text-align:left">' + Object.keys(data.errors).map(k => '<li>' + data.errors[k][0] + '</li>').join('') + '</ul>', 'Validation Error');
          } else showError(data.message || 'Failed to register');
          return;
        }
        if (data.success && data.member) {
          showTransactionSuccess({ title: 'Member Registered!', member: data.member.name, amount: null, new_balance: null, card_number: data.card_number, member_id: data.member.member_id })
            .then(() => {
              bootstrap.Modal.getInstance(document.getElementById('newUPIModal'))?.hide();
              document.getElementById('newMemberForm').reset();
              if (data.redirect_url) {
                  window.location.href = data.redirect_url;
              } else {
                  showSuccess('Custom Member registered successfully!').then(() => location.reload());
              }
            });
        } else showError(data.message || 'Failed to register');
      })
      .catch(err => showError('Network error: ' + err.message));
    });
  }
});

// Topup form
document.getElementById('topupForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.set('send_sms', document.getElementById('topup_send_sms').checked ? '1' : '0');
  fetch('{{ route("payments.top-ups.store") }}', {
    method: 'POST', body: formData,
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
  })
  .then(async r => {
    if (!r.ok) { const d = await r.json().catch(()=>{}); throw new Error(d?.message || 'Failed'); }
    return r.json();
  })
  .then(data => {
    if (data.success) {
      showTransactionSuccess({ title: 'Top-up Successful!', member: document.getElementById('topup_member_name').textContent, amount: parseFloat(document.getElementById('topup_amount').value), new_balance: data.new_balance })
        .then(() => {
          updateMemberBalanceInTable(document.getElementById('topup_member_id').value, data.new_balance);
          bootstrap.Modal.getInstance(document.getElementById('topupModal'))?.hide();
          location.reload();
        });
    } else showError(data.message || 'Failed to top-up');
  })
  .catch(err => showError('Error: ' + err.message));
});

// Adjust balance form
document.getElementById('adjustBalanceForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const memberId = document.getElementById('adjust_member_id').value;
  const newBalance = parseFloat(document.getElementById('adjust_new_balance').value);
  const reason = document.getElementById('adjust_reason').value;
  if (!confirm('Adjust this member\'s balance? This will be recorded in the audit log.')) return;
  fetch('{{ route("payments.members.adjust-balance", ":id") }}'.replace(':id', memberId), {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({ new_balance: newBalance, reason })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      updateMemberBalanceInTable(memberId, data.new_balance);
      bootstrap.Modal.getInstance(document.getElementById('adjustBalanceModal'))?.hide();
      showSuccess(data.message).then(() => location.reload());
    } else showError(data.message || 'Failed');
  })
  .catch(err => showError('Error: ' + err.message));
});

// Edit member form
document.getElementById('editMemberForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const memberId = document.getElementById('edit_member_id').value;
  const formData = new FormData(this);
  if (!formData.has('has_full_access')) formData.append('has_full_access', '0');
  fetch('{{ route("payments.members.update", ":id") }}'.replace(':id', memberId), {
    method: 'PUT', body: formData,
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) showSuccess('Member updated!').then(() => location.reload());
    else showError(data.message || 'Failed');
  })
  .catch(err => showError('Error: ' + err.message));
});

// Toggle dynamic fields in Registration/Edit modals
document.addEventListener('DOMContentLoaded', function() {
  function toggleFields(modalId, radioName, containerClass) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const radios = modal.querySelectorAll('input[name="' + radioName + '"]');
    const fields = modal.querySelectorAll('.' + containerClass);
    
    function update() {
      const isFull = modal.querySelector('input[name="' + radioName + '"]:checked')?.value === '1';
      fields.forEach(f => f.style.display = isFull ? 'block' : 'none');
    }
    
    radios.forEach(r => r.addEventListener('change', update));
    modal.addEventListener('shown.bs.modal', update);
    update();
  }

  toggleFields('newUPIModal', 'has_full_access', 'full-access-only');
  toggleFields('editMemberModal', 'has_full_access', 'edit-full-access-only');
});
</script>
@endpush

@push('styles')
<style>
/* ── Mini card chip ───────────────────────────────────────── */
.mc-chip {
  display: inline-block;
  width: 120px;
  border-radius: 8px;
  padding: 7px 10px;
  font-family: 'Courier New', monospace;
  position: relative;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,.18);
}
.mc-chip-inner { position: relative; z-index: 1; }
.mc-dots {
  display: flex;
  gap: 4px;
  margin-bottom: 4px;
}
.mc-dots span {
  display: inline-block;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  opacity: .6;
}
.mc-number {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .08em;
}
.mc-label {
  font-size: 9px;
  font-weight: 600;
  letter-spacing: .15em;
  margin-top: 3px;
  opacity: .8;
}
/* VIP — gold card */
.mc-vip { background: linear-gradient(135deg, #b8860b 0%, #daa520 100%); color: #fff; }
.mc-vip .mc-dots span { background: rgba(255,255,255,.8); }
/* Premier — dark card */
.mc-premier { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: #fff; }
.mc-premier .mc-dots span { background: #ffd700; }
/* Standard — silver card */
.mc-standard { background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%); color: #fff; }
.mc-standard .mc-dots span { background: rgba(255,255,255,.7); }

/* ── Table tweaks ─────────────────────────────────────────── */
#membersTable tbody tr:hover { background-color: #f9f9ff; }
.min-width-0 { min-width: 0; overflow: hidden; }
</style>
@endpush
@endsection
