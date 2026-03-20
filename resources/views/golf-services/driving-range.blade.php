@extends('settings._layout-base')

@section('title', 'Driving Range Management')
@section('description', 'Driving Range Management - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Golf Services /</span> Driving Range Management
</h4>

<style>
  .bay-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 15px;
  }
  .bay-card {
    height: 100px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
  }
  .bay-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }
  .bay-card.available {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    color: #2e7d32;
    border-color: #a5d6a7;
  }
  .bay-card.occupied {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    color: #ef6c00;
    border-color: #ffcc80;
  }
  .bay-number {
    font-size: 1.5rem;
    font-weight: 800;
    margin-bottom: 2px;
  }
  .bay-status {
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
  }
  .bay-timer {
    font-size: 0.7rem;
    position: absolute;
    bottom: 5px;
    font-weight: bold;
  }
</style>

<!-- Statistics Summary -->
<div class="row mb-6">
  <div class="col-md-3 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-primary rounded">
              <i class="icon-base ri ri-time-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Active Sessions</p>
            <h5 class="mb-0" id="stat_active">{{ $stats['active_sessions'] ?? 0 }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-success rounded">
              <i class="icon-base ri ri-golf-ball-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Balls Used Today</p>
            <h5 class="mb-0" id="stat_balls">{{ number_format($stats['balls_used_today'] ?? 0) }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-info rounded">
              <i class="icon-base ri ri-user-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Today's Customers</p>
            <h5 class="mb-0" id="stat_customers">{{ $stats['customers_today'] ?? 0 }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-warning rounded">
              <i class="icon-base ri ri-money-dollar-circle-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Today's Revenue</p>
            <h5 class="mb-0" id="stat_revenue">TZS {{ number_format($stats['revenue_today'] ?? 0) }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Active Sessions -->
<div class="row mb-6">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Active Driving Range Sessions</h5>
        <div>
          <a href="{{ route('settings.configuration') }}#driving-range" class="btn btn-label-primary btn-sm me-2">
            <i class="icon-base ri ri-settings-3-line me-1"></i> Configuration
          </a>
          <button type="button" class="btn btn-label-secondary btn-sm me-2" onclick="refreshData()">
            <i class="icon-base ri ri-refresh-line me-1"></i> Refresh
          </button>
          <button type="button" class="btn btn-primary btn-sm" onclick="openNewSessionModal()">
            <i class="icon-base ri ri-add-line me-1"></i> New Session
          </button>
        </div>
      </div>
      <div class="card-body">
        <!-- Visual Bay Grid -->
        <div class="bay-grid mb-5">
          @foreach($bays as $bay)
            <div class="bay-card {{ $bay['status'] }}" 
                 onclick="{{ $bay['status'] === 'available' ? 'openNewSessionModal('.$bay['number'].')' : 'showSessionDetails('.$bay['session']->id.', '.$bay['number'].')' }}">
              <div class="bay-number">{{ $bay['number'] }}</div>
              <div class="bay-status">{{ $bay['status'] }}</div>
              @if($bay['status'] === 'occupied')
                <div class="bay-timer session-duration" data-start="{{ $bay['session']->start_time->timestamp }}">
                  {{ $bay['session']->start_time->diffForHumans(null, true) }}
                </div>
              @endif
            </div>
          @endforeach
        </div>

        <hr class="my-6">
        <h6 class="mb-4">Session Details Table</h6>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover" id="activeSessionsTable">
            <thead>
              <tr>
                <th>Customer</th>
                <th>Bay</th>
                <th>Type</th>
                <th>Start Time</th>
                <th>Duration</th>
                <th>Balls</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($activeSessions ?? [] as $session)
              <tr data-session-id="{{ $session->id }}">
                <td>
                  <strong>{{ $session->customer_name }}</strong>
                  @if($session->customer_phone)
                  <br><small class="text-muted">{{ $session->customer_phone }}</small>
                  @endif
                </td>
                <td><span class="badge bg-label-primary">Bay {{ $session->bay_number }}</span></td>
                <td>{{ str_replace('_', ' ', ucwords($session->session_type, '_')) }}</td>
                <td>{{ $session->start_time->format('H:i') }}</td>
                <td class="session-duration" data-start="{{ $session->start_time->timestamp }}">
                  {{ $session->start_time->diffForHumans(null, true) }}
                </td>
                <td>{{ $session->balls_used }}</td>
                <td>TZS {{ number_format($session->amount) }}</td>
                <td><span class="badge bg-label-success">Active</span></td>
                <td>
                  <button class="btn btn-sm btn-success" onclick="endSession({{ $session->id }})">
                    <i class="icon-base ri ri-stop-circle-line"></i> End
                  </button>
                  <button class="btn btn-sm btn-danger" onclick="cancelSession({{ $session->id }})">
                    <i class="icon-base ri ri-close-line"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr id="noSessionsRow">
                <td colspan="9" class="text-center py-4 text-body-secondary">No active sessions</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Session History -->
<div class="row mb-6">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Today's Sessions</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive text-nowrap">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Bay</th>
                <th>Type</th>
                <th>Start</th>
                <th>End</th>
                <th>Duration</th>
                <th>Balls</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($todaySessions ?? [] as $session)
              <tr>
                <td>#{{ $session->id }}</td>
                <td>{{ $session->customer_name }}</td>
                <td>Bay {{ $session->bay_number }}</td>
                <td>{{ str_replace('_', ' ', ucwords($session->session_type, '_')) }}</td>
                <td>{{ $session->start_time->format('H:i') }}</td>
                <td>{{ $session->end_time ? $session->end_time->format('H:i') : '-' }}</td>
                <td>{{ $session->duration_minutes ? $session->duration_minutes . ' min' : '-' }}</td>
                <td>{{ $session->balls_used }}</td>
                <td>TZS {{ number_format($session->amount) }}</td>
                <td>
                  @if($session->status === 'active')
                    <span class="badge bg-label-success">Active</span>
                  @elseif($session->status === 'completed')
                    <span class="badge bg-label-primary">Completed</span>
                  @else
                    <span class="badge bg-label-danger">Cancelled</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="10" class="text-center py-4 text-body-secondary">No sessions today</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- New Session Modal -->
<div class="modal fade" id="newSessionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-fullscreen-md-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Start New Driving Range Session</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="newSessionForm">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-12 mb-4">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="customer_type" name="customer_type" required onchange="updateCustomerType()">
                  <option value="member">Member (Card Payment)</option>
                  <option value="guest">Guest / Non-Member</option>
                </select>
                <label for="customer_type">Customer Type *</label>
              </div>
            </div>
            <!-- Member Selection (Searchable) -->
            <div class="col-12 mb-4 position-relative" id="memberSelectionDiv">
              <label class="form-label fw-semibold text-body-secondary small text-uppercase mb-1">Find Member</label>
              <div class="input-group border rounded shadow-sm">
                <span class="input-group-text bg-white border-0"><i class="ri ri-search-2-line text-muted"></i></span>
                <input type="text" class="form-control border-0 py-3" id="member_search_input"
                  placeholder="Search by name, card #, or phone..." autocomplete="off" />
                <button type="button" class="btn btn-outline-secondary border-0" id="clearMemberBtn" style="display:none;" onclick="clearMemberSelection()">
                  <i class="ri ri-close-line"></i>
                </button>
              </div>
              <div id="memberSearchSuggestions" class="list-group position-absolute shadow-lg border-0 mt-1 bg-white"
                style="z-index: 2000; display: none; max-height: 280px; overflow-y: auto; border-radius: 8px; width: calc(100% - 2.5rem); left: 1.25rem;"></div>
              <input type="hidden" id="member_id" name="member_id" />
            </div>
            <!-- Guest Information -->
            <div class="col-12 mb-4" id="guestInfoDiv" style="display: none;">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="form-floating form-floating-outline">
                    <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name">
                    <label for="customer_name">Customer Name *</label>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-floating form-floating-outline">
                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone" placeholder="Phone Number">
                    <label for="customer_phone">Phone Number</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 mb-4" id="memberInfoBox" style="display: none;">
              <div class="card bg-light">
                <div class="card-body py-3">
                  <div class="row">
                    <div class="col-md-4">
                      <small class="text-muted">Member</small>
                      <p class="mb-0 fw-bold" id="info_name">-</p>
                    </div>
                    <div class="col-md-4">
                      <small class="text-muted">Card Number</small>
                      <p class="mb-0" id="info_card">-</p>
                    </div>
                    <div class="col-md-4">
                      <small class="text-muted">Available Balance</small>
                      <p class="mb-0 fw-bold text-success" id="info_balance">-</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Payment Method -->
            <div class="col-12 mb-4" id="paymentMethodDiv">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="payment_method" name="payment_method" required>
                  <option value="balance">MEMBER BALANCE (Card)</option>
                  <option value="cash">CASH</option>
                  <option value="mobile_money">MOBILE MONEY (LIPA NAMBA)</option>
                  <option value="bank">BANK</option>
                  <option value="card">CARD (POS)</option>
                </select>
                <label for="payment_method">Payment Method *</label>
              </div>
            </div>
            <!-- Bay display (auto-assigned from clicking bay card) -->
            <div class="col-12 mb-4" id="bayDisplayRow">
              <div class="d-flex align-items-center gap-3 p-3 rounded border bg-primary-subtle">
                <div class="avatar avatar-sm">
                  <span class="avatar-initial rounded bg-primary"><i class="ri ri-map-pin-2-fill"></i></span>
                </div>
                <div class="flex-grow-1">
                  <div class="small text-muted text-uppercase fw-bold">Assigned Bay / Slot</div>
                  <div class="fw-bold fs-5 text-primary" id="bayDisplayLabel">No bay selected</div>
                </div>
                <span class="badge bg-label-primary fs-6" id="bayBadge">—</span>
              </div>
              <input type="hidden" id="bay_number" name="bay_number" />
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="session_type" name="session_type" required onchange="updateSessionPrice()">
                  <option value="bucket" selected>1 Bucket (TZS {{ number_format($config->bucket_price ?? 2000) }})</option>
                  <option value="ball_limit">Ball Limit (50 balls)</option>
                  <option value="unlimited">Unlimited 1hr</option>
                </select>
                <label for="session_type">Session Type *</label>
              </div>
            </div>
            <div class="col-md-6 mb-4" id="bucketsCountDiv" style="display: none;">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="buckets_count" name="buckets_count" value="1" min="1" onchange="updateSessionPrice()" />
                <label for="buckets_count">Number of Buckets</label>
              </div>
            </div>
            <div class="col-md-6 mb-4" id="ballsLimitDiv">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="balls_limit_allowed" name="balls_limit_allowed" value="{{ $config->balls_limit_per_session ?? 50 }}" min="1" onchange="updateSessionPrice()" />
                <label for="balls_limit_allowed">Number of Balls Limit *</label>
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="estimated_amount" readonly />
                <label for="estimated_amount">Amount (TZS)</label>
              </div>
              <small class="text-muted" id="amountDescription">Will be deducted from member's card balance</small>
            </div>
            <div class="col-12 mb-4">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="notes" name="notes" placeholder="Notes" style="height: 80px"></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-play-circle-line me-1"></i> Start Session
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- End Session Modal -->
<div class="modal fade" id="endSessionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">End Session</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="endSessionForm">
        @csrf
        <input type="hidden" id="end_session_id" name="session_id" />
        <div class="modal-body">
          <div class="mb-4">
            <div class="form-floating form-floating-outline">
              <input type="number" class="form-control" id="end_balls_used" name="balls_used" />
              <label for="end_balls_used">Final Balls Used</label>
            </div>
          </div>
          <div class="alert alert-info">
            <strong>Session Summary</strong><br>
            Duration: <span id="end_duration">-</span><br>
            Amount: <span id="end_amount">-</span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="icon-base ri ri-check-line me-1"></i> End & Charge
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const config = {
  hourly_rate: {{ $config->hourly_rate ?? 5000 }},
  ball_limit_price: {{ $config->ball_limit_price ?? $config->hourly_rate ?? 5000 }},
  balls_limit_per_session: {{ $config->balls_limit_per_session ?? 50 }},
  bucket_price: {{ $config->bucket_price ?? 2000 }},
  unlimited_price: {{ $config->unlimited_price ?? 8000 }},
  balls_per_bucket: {{ $config->balls_per_bucket ?? 50 }}
};

let selectedMember = null;
let isGuest = false;

// Update customer type (member or guest)
function updateCustomerType() {
  const customerType = document.getElementById('customer_type').value;
  const memberDiv = document.getElementById('memberSelectionDiv');
  const guestDiv = document.getElementById('guestInfoDiv');
  const memberInfoBox = document.getElementById('memberInfoBox');
  const paymentMethod = document.getElementById('payment_method');
  
  isGuest = (customerType === 'guest');
  
  if (isGuest) {
    memberDiv.style.display = 'none';
    guestDiv.style.display = 'block';
    memberInfoBox.style.display = 'none';
    selectedMember = null;
    
    // Update payment method options for guest
    paymentMethod.innerHTML = `
      <option value="cash">CASH</option>
      <option value="mobile_money">MOBILE MONEY (LIPA NAMBA)</option>
      <option value="bank">BANK</option>
      <option value="card">CARD (POS)</option>
    `;
    document.getElementById('amountDescription').textContent = 'Amount to be paid';
    
    // Clear member_id
    document.getElementById('member_id').value = '';
  } else {
    memberDiv.style.display = 'block';
    guestDiv.style.display = 'none';
    
    // Update payment method options for member
    paymentMethod.innerHTML = `
      <option value="balance">MEMBER BALANCE (Card)</option>
      <option value="cash">CASH</option>
      <option value="mobile_money">MOBILE MONEY (LIPA NAMBA)</option>
      <option value="bank">BANK</option>
      <option value="card">CARD (POS)</option>
    `;
    document.getElementById('amountDescription').textContent = 'Will be deducted from member\'s card balance (if using balance payment)';
  }
  
  updateSessionPrice();
}

// ---- Member Live Search ----
let memberSearchTimeout;
document.getElementById('member_search_input')?.addEventListener('input', function() {
  const query = this.value.trim();
  const suggestions = document.getElementById('memberSearchSuggestions');
  if (query.length < 2) {
    suggestions.style.display = 'none';
    if (query.length === 0) clearMemberSelection(false);
    return;
  }
  clearTimeout(memberSearchTimeout);
  memberSearchTimeout = setTimeout(() => {
    fetch('{{ url("payments/members/search") }}?q=' + encodeURIComponent(query), {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(members => {
      const suggestions = document.getElementById('memberSearchSuggestions');
      if (members && members.length > 0) {
        suggestions.innerHTML = members.slice(0, 8).map(m => `
          <div class="list-group-item list-group-item-action p-3 member-suggestion-item" style="cursor:pointer;"
            data-id="${m.id}"
            data-name="${(m.name||'').replace(/"/g,'&quot;')}"
            data-card="${(m.card_number||'').replace(/"/g,'&quot;')}"
            data-phone="${(m.phone||'').replace(/"/g,'&quot;')}"
            data-balance="${parseFloat(m.balance)||0}"
            data-full-access="${m.has_full_access?1:0}"
            data-type="${m.membership_type||''}">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong class="text-primary">${m.name||'N/A'}</strong>
                <div class="small text-muted">${m.card_number||'No card'} | ${m.phone||'No phone'}</div>
              </div>
              <div class="text-end">
                <span class="badge bg-label-success">TZS ${m.balance ? parseFloat(m.balance).toLocaleString() : '0'}</span>
              </div>
            </div>
          </div>`
        ).join('');
        suggestions.style.display = 'block';
      } else {
        suggestions.innerHTML = '<div class="list-group-item text-muted small">No members found</div>';
        suggestions.style.display = 'block';
      }
    })
    .catch(() => { const s = document.getElementById('memberSearchSuggestions'); if(s) s.style.display = 'none'; });
  }, 300);
});

// Delegate click on member suggestions
document.getElementById('memberSearchSuggestions')?.addEventListener('click', function(e) {
  const item = e.target.closest('.member-suggestion-item');
  if (!item) return;
  selectMemberFromSearch(
    item.dataset.id,
    item.dataset.name,
    item.dataset.card,
    item.dataset.phone,
    item.dataset.balance,
    parseInt(item.dataset.fullAccess),
    item.dataset.type
  );
});

function selectMemberFromSearch(id, name, card, phone, balance, fullAccess, type) {
  document.getElementById('member_id').value = id;
  document.getElementById('member_search_input').value = name;
  document.getElementById('memberSearchSuggestions').style.display = 'none';
  document.getElementById('clearMemberBtn').style.display = 'inline-flex';

  selectedMember = { id, name, phone, card, balance: parseFloat(balance||0), type, full_access: fullAccess };

  const infoBox = document.getElementById('memberInfoBox');
  document.getElementById('info_name').textContent = name;
  document.getElementById('info_card').textContent = card || '-';
  document.getElementById('info_balance').textContent = 'TZS ' + selectedMember.balance.toLocaleString();
  infoBox.style.display = 'block';

  const paymentMethod = document.getElementById('payment_method');
  if (fullAccess) {
    paymentMethod.value = 'balance';
    Array.from(paymentMethod.options).forEach(opt => {
      opt.disabled = opt.value !== 'balance';
    });
    document.getElementById('amountDescription').textContent = 'Cardholders must pay via card balance.';
  } else {
    paymentMethod.value = 'cash';
    Array.from(paymentMethod.options).forEach(opt => {
      opt.disabled = opt.value === 'balance';
    });
    document.getElementById('amountDescription').textContent = 'Selected payment method for custom member.';
  }
  updateSessionPrice();
}

function clearMemberSelection(clearInput = true) {
  selectedMember = null;
  const memberIdEl = document.getElementById('member_id');
  if (memberIdEl) memberIdEl.value = '';
  const searchInput = document.getElementById('member_search_input');
  if (clearInput && searchInput) searchInput.value = '';
  const suggestions = document.getElementById('memberSearchSuggestions');
  if (suggestions) suggestions.style.display = 'none';
  const clearBtn = document.getElementById('clearMemberBtn');
  if (clearBtn) clearBtn.style.display = 'none';
  const infoBox = document.getElementById('memberInfoBox');
  if (infoBox) infoBox.style.display = 'none';
  updateSessionPrice();
}


// Close suggestions when clicking outside
document.addEventListener('click', function(e) {
  if (!e.target.closest('#member_search_input') && !e.target.closest('#memberSearchSuggestions')) {
    const s = document.getElementById('memberSearchSuggestions');
    if (s) s.style.display = 'none';
  }
});


// Kept for compatibility but no longer needed with search
function updateMemberInfo() {}

// Update estimated price
function updateSessionPrice() {
  const type = document.getElementById('session_type').value;
  const buckets = parseInt(document.getElementById('buckets_count').value) || 1;
  
  let amount = 0;
  if (type === 'ball_limit') {
    amount = config.ball_limit_price;
    document.getElementById('bucketsCountDiv').style.display = 'none';
    document.getElementById('ballsLimitDiv').style.display = 'block';
  } else if (type === 'bucket') {
    amount = config.bucket_price * buckets;
    document.getElementById('bucketsCountDiv').style.display = 'block';
    document.getElementById('ballsLimitDiv').style.display = 'none';
  } else if (type === 'unlimited') {
    amount = config.unlimited_price;
    document.getElementById('bucketsCountDiv').style.display = 'none';
    document.getElementById('ballsLimitDiv').style.display = 'none';
  }
  
  // Apply member discount only for members (not guest customer type)
  const customerType = document.getElementById('customer_type')?.value || 'member';
  if (customerType === 'member' && selectedMember && selectedMember.type !== 'guest') {
    const discount = {{ $config->member_discount ?? 0 }};
    amount = amount * (1 - (discount / 100));
  }
  
  document.getElementById('estimated_amount').value = amount.toLocaleString();
  
  // Check balance (only for member with balance payment)
  const paymentMethod = document.getElementById('payment_method').value;
  if (selectedMember && paymentMethod === 'balance' && selectedMember.balance < amount) {
    document.getElementById('info_balance').classList.remove('text-success');
    document.getElementById('info_balance').classList.add('text-danger');
  } else if (selectedMember && paymentMethod === 'balance') {
    document.getElementById('info_balance').classList.remove('text-danger');
    document.getElementById('info_balance').classList.add('text-success');
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  updateSessionPrice();
  // Set default buckets on load
  document.getElementById('buckets_count').value = 1;
  document.getElementById('balls_limit_allowed').value = config.balls_limit_per_session;
  
  // Trigger customer type update to set defaults
  updateCustomerType();
});

// New Session Form
document.getElementById('newSessionForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const customerType = document.getElementById('customer_type').value;
  const paymentMethod = document.getElementById('payment_method').value;
  
  // Validate member selection
  if (customerType === 'member' && !selectedMember) {
    showWarning('Please select a member first');
    return;
  }
  
  // Validate guest information
  if (customerType === 'guest') {
    const customerName = document.getElementById('customer_name').value.trim();
    if (!customerName) {
      showWarning('Please enter customer name');
      return;
    }
  }

  // Validate bay selection
  const bayNumber = document.getElementById('bay_number').value;
  if (!bayNumber) {
    showWarning('No bay selected. Please close this dialog and click an available bay on the map to start a session.');
    return;
  }
  

  if (customerType === 'member' && paymentMethod === 'balance') {
    const amount = parseFloat(document.getElementById('estimated_amount').value.replace(/,/g, ''));
    if (selectedMember.balance < amount) {
      showError('Insufficient balance!<br><br>Required: TZS ' + amount.toLocaleString() + '<br>Available: TZS ' + selectedMember.balance.toLocaleString() + '<br><br>Please top up the member card first or select a different payment method.');
      return;
    }
  }
  
  const formData = new FormData(this);
  
  // Only include customer_name and customer_phone for guests
  if (customerType === 'guest') {
    formData.append('customer_name', document.getElementById('customer_name').value.trim());
    formData.append('customer_phone', document.getElementById('customer_phone').value.trim() || '');
  } else {
    // Remove customer_name and customer_phone fields for members to avoid validation issues
    formData.delete('customer_name');
    formData.delete('customer_phone');
  }
  
  fetch('{{ route("golf-services.driving-range.store") }}', {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    }
  })
  .then(response => {
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      return response.text().then(text => {
        console.error('Non-JSON response:', text);
        throw new Error('Server returned HTML instead of JSON. Check console for details.');
      });
    }
    if (!response.ok) {
      return response.json().then(json => {
        throw new Error(json.message || 'Server error: ' + response.status);
      });
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      showSuccess(data.message).then(() => location.reload());
    } else {
      showError(data.message || 'Failed to start session');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showError('Error starting session: ' + error.message);
  });
});

// Helper to open new session modal with pre-selected bay
function openNewSessionModal(bayNumber = null) {
  try {
    clearMemberSelection();
  } catch(e) { console.warn('clearMemberSelection error:', e); }

  try {
    const bayInput  = document.getElementById('bay_number');
    const bayLabel  = document.getElementById('bayDisplayLabel');
    const bayBadge  = document.getElementById('bayBadge');

    if (bayNumber) {
      if (bayInput)  bayInput.value = bayNumber;
      if (bayLabel)  bayLabel.textContent = 'Bay ' + bayNumber;
      if (bayBadge) { bayBadge.textContent = 'Bay ' + bayNumber; bayBadge.className = 'badge bg-primary fs-6'; }
    } else {
      if (bayInput)  bayInput.value = '';
      if (bayLabel)  bayLabel.textContent = 'No bay selected — please click an available bay';
      if (bayBadge) { bayBadge.textContent = '—'; bayBadge.className = 'badge bg-label-primary fs-6'; }
    }
  } catch(e) { console.warn('Bay badge error:', e); }

  const modal = new bootstrap.Modal(document.getElementById('newSessionModal'));
  modal.show();
}


// Function to show session details (or directly end session for simplicity)
function showSessionDetails(sessionId, bayNumber) {
  // For now, let's just trigger the end session flow directly or show a quick confirm
  endSession(sessionId);
}

// End Session
function endSession(sessionId) {
  showConfirm('Are you sure you want to end this session?').then((result) => {
    if (result.isConfirmed) {
    fetch(`{{ url('golf-services/driving-range/sessions') }}/${sessionId}/end`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json'
      }
    })
      fetch(`{{ url('golf-services/driving-range/sessions') }}/${sessionId}/end`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showSuccess('Session ended. Amount: TZS ' + data.session.amount.toLocaleString()).then(() => location.reload());
        } else {
          showError(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showError('Error ending session');
      });
    }
  });
}

// Cancel Session
function cancelSession(sessionId) {
  showConfirm('Are you sure you want to cancel this session? No charges will apply.').then((result) => {
    if (result.isConfirmed) {
    fetch(`{{ url('golf-services/driving-range/sessions') }}/${sessionId}/cancel`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json'
      }
    })
      fetch(`{{ url('golf-services/driving-range/sessions') }}/${sessionId}/cancel`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showSuccess('Session cancelled').then(() => location.reload());
        } else {
          showError(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showError('Error cancelling session');
      });
    }
  });
}


// Refresh data
function refreshData() {
  location.reload();
}

// Update duration every minute
setInterval(function() {
  document.querySelectorAll('.session-duration').forEach(function(el) {
    const startTime = parseInt(el.dataset.start) * 1000;
    const now = Date.now();
    const diff = Math.floor((now - startTime) / 60000);
    const hours = Math.floor(diff / 60);
    const mins = diff % 60;
    el.textContent = hours > 0 ? `${hours}h ${mins}m` : `${mins}m`;
  });
}, 60000);
</script>
@endpush
