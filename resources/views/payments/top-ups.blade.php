@extends('settings._layout-base')

@section('title', 'Top-ups')
@section('description', 'Top-ups - Golf Club Management System')

@push('styles')
<style>
  .strike-through { text-decoration: line-through; opacity: 0.6; }
  .printer-ticket {
    border: 1px dashed #ddd;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    background-image: radial-gradient(#f1f1f1 1px, transparent 1px);
    background-size: 20px 20px;
  }
  .bg-light-subtle { background-color: #f8f9fa !important; }
  .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
  
  .custom-option {
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .custom-option:hover { border-color: var(--bs-primary) !important; background-color: rgba(var(--bs-primary-rgb), 0.05); }
  .custom-option .form-check-input:checked + .custom-option-content {
    background-color: var(--bs-primary);
    color: white;
  }
  .custom-option .form-check-input:checked + .custom-option-content .text-primary { color: white !important; }
  .custom-option .form-check-input:checked + .custom-option-content .small { color: white !important; }

  #memberInfo.card {
     animation: slideInDown 0.3s ease-out;
  }
  @keyframes slideInDown {
    from { transform: translateY(-10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  #memberSuggestions {
    background: #ffffff !important;
    background-color: #ffffff !important;
    border: 1px solid rgba(0,0,0,0.1) !important;
    z-index: 10000 !important; /* Higher than almost everything */
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
  }
  #memberSuggestions .list-group-item {
    border-bottom: 1px solid #f1f1f1;
    transition: background 0.1s;
  }
  #memberSuggestions .list-group-item:hover {
    background-color: #f8f9ff !important;
  }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0">
    <span class="text-muted fw-light">Payments /</span> Quick Top-up
  </h4>
  <div class="d-flex gap-2">
    <button class="btn btn-label-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#bulkTopupModal">
      <i class="ri ri-group-line me-1"></i> Bulk Top-up
    </button>
  </div>
</div>

<!-- Premium Hero Banner -->
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="card-body p-0">
        <div class="row g-0">
          <!-- Metrics Summary -->
          <div class="col-md-8 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="mb-0 fw-bold"><i class="ri ri-wallet-3-line me-2 text-primary"></i>Wallet Top-up Statistics</h5>
              <span class="badge bg-label-primary rounded-pill">Real-time Data</span>
            </div>
            
            <div class="row g-3 g-md-4">
              <div class="col-6 col-md-3">
                <div class="p-2 p-md-3 border rounded bg-light-subtle h-100">
                  <small class="text-muted d-block mb-1 small">Today</small>
                  <h4 class="mb-0 fw-bold" id="topupsToday">{{ number_format($stats['topups_today'] ?? 0) }}</h4>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="p-2 p-md-3 border border-success rounded bg-success-subtle h-100">
                  <small class="text-success d-block mb-1 small">Amount</small>
                  <h4 class="mb-0 fw-bold text-success fs-6 fs-md-5" id="amountToday">TZS {{ number_format($stats['amount_today'] ?? 0) }}</h4>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="p-2 p-md-3 border border-info rounded bg-info-subtle h-100">
                  <small class="text-info d-block mb-1 small">Balance</small>
                  <h4 class="mb-0 fw-bold text-info fs-6 fs-md-5" id="totalBalance">TZS {{ number_format($stats['total_balance'] ?? 0) }}</h4>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="p-2 p-md-3 border border-warning rounded bg-warning-subtle h-100">
                  <small class="text-warning d-block mb-1 small">Active</small>
                  <h4 class="mb-0 fw-bold text-warning" id="activeMembers">{{ number_format($stats['active_members'] ?? 0) }}</h4>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Performance Highlights -->
          <div class="col-md-4 bg-primary text-white p-4 d-flex flex-column justify-content-between">
            <div>
              <h5 class="text-white fw-bold mb-4">Today's Performance</h5>
              <div class="mb-4">
                <div class="d-flex justify-content-between mb-1 small">
                  <span>Target Collection</span>
                  <strong>100%</strong>
                </div>
                <div class="progress progress-white" style="height: 8px; background: rgba(255,255,255,0.2);">
                  <div class="progress-bar bg-white" style="width: 85%"></div>
                </div>
                <small class="mt-1 d-block opacity-75">Growing steadily vs yesterday</small>
              </div>
            </div>
            <div class="mt-auto">
              <div class="alert alert-white bg-white text-primary border-0 mb-0 py-2 d-flex align-items-center">
                <div class="avatar avatar-sm me-2 bg-primary-subtle rounded">
                  <i class="ri ri-line-chart-line text-primary"></i>
                </div>
                <div>
                  <small class="d-block lh-1">Peak Time</small>
                  <strong class="fs-6">09:00 AM - 11:00 AM</strong>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Operations Layout -->
<div class="row">
  <!-- Recent History (Left) -->
  <div class="col-md-8 order-2 order-md-1">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="ri ri-history-line me-2"></i>Recent Top-ups</h5>
        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
          <div class="input-group input-group-sm w-100 w-sm-auto" style="min-width: 150px;">
            <span class="input-group-text bg-light border-end-0"><i class="ri ri-calendar-line text-muted"></i></span>
            <input type="date" class="form-control bg-light border-start-0 ps-0" id="filterDate" onchange="filterTopups()" value="{{ date('Y-m-d') }}">
          </div>
          <div class="input-group input-group-sm w-100 w-sm-auto" style="min-width: 130px;">
            <span class="input-group-text bg-light border-end-0"><i class="ri ri-filter-3-line text-muted"></i></span>
            <select class="form-select bg-light border-start-0 ps-0" id="filterMethod" onchange="filterTopups()">
              <option value="">All Methods</option>
              <option value="cash">CASH</option>
              <option value="mobile_money">MOBILE MONEY</option>
              <option value="bank">BANK</option>
              <option value="card">CARD (POS)</option>
            </select>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr class="text-uppercase small fw-bold text-muted">
                <th class="ps-4">Member Info</th>
                <th class="text-end">Amount</th>
                <th class="text-end">Balance Step</th>
                <th class="text-center">Method</th>
                <th class="pe-4 text-center">Actions</th>
              </tr>
            </thead>
            <tbody id="topupsTableBody">
              @php $displayItems = method_exists($topups, 'items') ? $topups->items() : $topups; @endphp
              @forelse(($displayItems ?? []) as $topup)
              @php
                $method = $topup->payment_method === 'mobile' ? 'mobile_money' : $topup->payment_method;
                $badgeClass = $method === 'cash' ? 'success' : ($method === 'mobile_money' ? 'warning' : 'info');
              @endphp
              <tr class="border-transparent">
                <td class="ps-4 py-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                      <div class="avatar-initial rounded bg-label-primary">
                        {{ strtoupper(substr($topup->member->name ?? 'M', 0, 2)) }}
                      </div>
                    </div>
                    <div>
                      <strong>{{ $topup->member->name ?? 'N/A' }}</strong>
                      <div class="small text-muted">
                        <i class="ri ri-time-line me-1"></i>{{ $topup->created_at->format('M d, h:i A') }}
                        @if($topup->member && $topup->member->card_number)
                          <span class="mx-1">•</span> <code class="text-primary">{{ $topup->member->card_number }}</code>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td class="text-end py-3">
                  <h6 class="mb-0 fw-bold text-success">+TZS {{ number_format($topup->amount) }}</h6>
                </td>
                <td class="text-end py-3">
                  <div class="d-flex flex-column align-items-end">
                    <small class="text-muted strike-through" style="font-size: 10px;">Before: {{ number_format($topup->balance_before) }}</small>
                    <strong class="text-dark">After: {{ number_format($topup->balance_after) }}</strong>
                  </div>
                </td>
                <td class="text-center py-3">
                  <span class="badge bg-label-{{ $badgeClass }} rounded-pill">
                    {{ ucfirst(str_replace('_', ' ', $method)) }}
                  </span>
                </td>
                <td class="pe-4 text-center py-3">
                  <div class="dropdown">
                    <button class="btn btn-sm btn-icon btn-label-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="icon-base ri ri-more-2-fill"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                      <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="viewReceipt({{ $topup->id }})">
                        <i class="ri ri-file-list-3-line me-2 text-primary"></i>View Receipt
                      </a></li>
                      <li><a class="dropdown-item py-2" href="{{ route('payments.top-ups.receipt', $topup->id) }}" target="_blank">
                        <i class="ri ri-printer-line me-2 text-secondary"></i>Print Receipt
                      </a></li>
                    </ul>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted opacity-50">
                  <i class="ri ri-history-line ri-3x d-block mb-2"></i>
                  <p class="mb-0 fw-semibold">No recent top-ups found</p>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer border-top bg-transparent py-3">
        <div class="d-flex justify-content-between align-items-center">
          <small class="text-muted">Currently showing <strong id="showingCount">{{ count(($topups->items() ?? $topups ?? [])) }}</strong> transactions</small>
          <button class="btn btn-sm btn-outline-secondary" onclick="exportTopups()">
            <i class="ri ri-download-line me-1"></i> Export Data
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Top-up Processor (Right) -->
  <div class="col-md-4 order-1 order-md-2 mb-4 mb-md-0">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0 text-white"><i class="ri ri-add-circle-line me-2"></i>Process New Top-up</h5>
      </div>
      <div class="card-body">
        <form id="topupForm" onsubmit="processTopup(event)">
          <!-- Member Search -->
          <div class="mb-4 position-relative" style="z-index: 1060;">
            <label class="form-label fw-bold">1. Find Member</label>
            <div class="input-group input-group-lg border rounded shadow-sm">
              <span class="input-group-text bg-white border-0"><i class="ri ri-search-2-line text-muted"></i></span>
              <input type="text" class="form-control border-0 ps-1" id="card_number_search" placeholder="Search name or card..." onkeyup="searchMember(this.value)" onfocus="searchMember(this.value)" autocomplete="off">
            </div>
            <div id="memberSuggestions" class="list-group position-absolute w-100 shadow-lg border-0 mt-1" style="z-index: 9999; display: none; max-height: 300px; overflow-y: auto; background: white;"></div>
          </div>
          
          <!-- Member Info Display -->
          <div id="memberInfo" class="card bg-label-info border-0 mb-4 d-none">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                  <span class="avatar-initial rounded bg-info" id="memberInitial">M</span>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                  <h6 class="mb-0 fw-bold text-truncate" id="memberName">-</h6>
                  <small id="memberCard" class="text-info opacity-75">-</small>
                </div>
                <div class="text-end">
                  <small class="text-muted d-block lh-1 mb-1">Current Balance</small>
                  <strong class="text-dark fs-6" id="memberBalance">TZS 0</strong>
                </div>
              </div>
            </div>
          </div>
          
          <input type="hidden" id="selected_member_id" name="member_id">
          
          <!-- Amount Section -->
          <div class="mb-4">
            <label class="form-label fw-bold">2. Top-Up Amount (TZS)</label>
            <div class="btn-group w-100 mb-2" role="group">
              <button type="button" class="btn btn-outline-primary btn-sm py-1" onclick="setAmount(20000)">20K</button>
              <button type="button" class="btn btn-outline-primary btn-sm py-1" onclick="setAmount(50000)">50K</button>
              <button type="button" class="btn btn-outline-primary btn-sm py-1" onclick="setAmount(100000)">100K</button>
              <button type="button" class="btn btn-outline-primary btn-sm py-1" onclick="setAmount(200000)">200K</button>
            </div>
            <div class="form-floating form-floating-outline">
              <input type="number" class="form-control form-control-lg fw-bold text-primary" id="amount" name="amount" min="1000" step="1000" required onchange="updateNewBalance()" placeholder="Enter amount">
              <label>Amount TZS *</label>
            </div>
          </div>
          
          <!-- New Balance Preview -->
          <div class="alert alert-success border-0 px-3 py-2 mb-4" id="newBalancePreview" style="display: none;">
            <div class="d-flex justify-content-between align-items-center">
              <span class="small fw-semibold">Future Balance:</span>
              <strong id="newBalanceAmount" class="fs-5">TZS 0</strong>
            </div>
          </div>
          
          <!-- Payment Selection -->
          <div class="mb-4">
            <label class="form-label fw-bold">3. Payment & Options</label>
            <div class="form-floating form-floating-outline mb-3">
              <select class="form-select border-primary-subtle" id="payment_method" name="payment_method" required>
                <option value="cash">CASH</option>
                <option value="mobile_money">MOBILE MONEY (LIPA NAMBA)</option>
                <option value="bank">BANK TRANSFER</option>
                <option value="card">CARD (POS)</option>
              </select>
              <label>Payment Method *</label>
            </div>
            
            <div class="form-floating form-floating-outline mb-3">
              <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="Ref/Transaction ID">
              <label>Reference # / TxID</label>
            </div>
            
            <div class="row g-2">
              <div class="col-6">
                <div class="form-check custom-option custom-option-icon border rounded p-2 text-center h-100">
                  <label class="form-check-label custom-option-content" for="send_sms">
                    <span class="custom-option-body">
                      <i class="ri ri-message-3-line d-block mb-1"></i>
                      <span class="small fw-semibold">SMS Notice</span>
                    </span>
                    <input class="form-check-input d-none" type="checkbox" name="send_sms" id="send_sms" checked />
                  </label>
                </div>
              </div>
              <div class="col-6">
                <div class="form-check custom-option custom-option-icon border rounded p-2 text-center h-100">
                  <label class="form-check-label custom-option-content" for="print_receipt">
                    <span class="custom-option-body">
                      <i class="ri ri-printer-line d-block mb-1"></i>
                      <span class="small fw-semibold">Receipt</span>
                    </span>
                    <input class="form-check-input d-none" type="checkbox" name="print_receipt" id="print_receipt" checked />
                  </label>
                </div>
              </div>
            </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm py-3 fw-bold" id="submitBtn">
            <i class="ri ri-check-double-line me-1"></i> Process Top-up
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
          
</div>

<!-- Bulk Top-up Modal -->
<div class="modal fade" id="bulkTopupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white py-4">
        <h5 class="modal-title text-white fw-bold">
          <i class="ri ri-group-line me-2"></i>Bulk Top-up Operations
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="bulkTopupForm" onsubmit="processBulkTopup(event)">
        <div class="modal-body p-4">
          <div class="alert alert-info border-0 shadow-sm mb-4">
            <i class="ri ri-information-line me-1"></i>
            Select multiple members to apply a uniform top-up amount to all.
          </div>
          
          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline border rounded">
                <input type="number" class="form-control border-0" id="bulk_amount" name="amount" min="1000" step="1000" required placeholder="0">
                <label>Amount per Member (TZS) *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline border rounded">
                <select class="form-select border-0" id="bulk_payment_method" name="payment_method" required>
                  <option value="cash">CASH</option>
                  <option value="mobile_money">MOBILE MONEY</option>
                  <option value="bank">BANK TRANSFER</option>
                </select>
                <label>Payment Method *</label>
              </div>
            </div>
          </div>
          
          <div class="form-floating form-floating-outline mb-4 border rounded">
            <input type="text" class="form-control border-0" id="bulk_reference" name="reference" placeholder="Ref #">
            <label>Common Reference Number (Optional)</label>
          </div>
          
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label fw-bold mb-0">Select Target Members</label>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-xs btn-outline-primary" onclick="selectAllMembers()">Select All</button>
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="deselectAllMembers()">Clear</button>
              </div>
            </div>
            <div class="border rounded-3 bg-light p-3" style="max-height: 250px; overflow-y: auto;" id="bulkMembersList">
              <!-- Members will be loaded here -->
              <div class="text-center py-4 opacity-50">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <small class="d-block mt-2">Loading member list...</small>
              </div>
            </div>
            <div class="mt-2 d-flex justify-content-between align-items-center">
              <span class="text-muted small"><span id="selectedCount" class="fw-bold">0</span> members selected</span>
            </div>
          </div>
          
          <div class="alert alert-warning border-0 mt-3 mb-0 py-2 d-none" id="bulkSummary">
            <div class="d-flex justify-content-between align-items-center">
              <span>Estimated Total Distribution:</span>
              <strong id="bulkTotalAmount" class="fs-5">TZS 0</strong>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light py-3 border-0">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
            <i class="ri ri-check-double-line me-1"></i>Apply Bulk Top-up
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Premium Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
    <div class="modal-content border-0 shadow-lg" style="background-color: #f8f9fa;">
      <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between">
        <h5 class="modal-title fw-bold text-dark"><i class="ri ri-file-list-3-line me-2 text-primary"></i>Transaction Receipt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white printer-ticket">
          <div class="card-body p-4">
             <div id="receiptContent">
               <!-- Receipt content dynamically generated -->
             </div>
          </div>
          <div class="bg-light-subtle py-2 px-4 border-top border-dashed text-center">
            <small class="text-muted fw-bold italic">*** System Generated ***</small>
          </div>
        </div>
        
        <div class="d-grid gap-2">
          <a href="#" id="receiptPdfLink" class="btn btn-primary py-2 fw-bold rounded-3" target="_blank">
            <i class="ri ri-printer-line me-2"></i>Print / Download receipt
          </a>
          <button type="button" class="btn btn-label-secondary py-2 rounded-3" data-bs-dismiss="modal">
            Close Panel
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Initialize with data from Laravel
let members = @json($members ?? []);
let topups = @json($topups->items() ?? []);
let selectedMemberId = null;
let currentMemberBalance = 0;
let currentReceiptTopup = null;
let searchCache = {}; // Cache for server search results
let searchTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
  updateStats();
  loadMembers();
  document.getElementById('filterDate').value = new Date().toISOString().split('T')[0];
});function loadMembers() {
  fetch('{{ route("payments.members.search") }}', {
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  })
  .then(r => r.json())
  .then(data => {
    members = Array.isArray(data) ? data : [];
    renderBulkMembersList();
  })
  .catch(err => {
    console.error('Error preload members:', err);
    members = [];
  });
}

function loadTopups() {
  const date = document.getElementById('filterDate')?.value;
  const method = document.getElementById('filterMethod')?.value;
  
  let url = '{{ route("payments.top-ups.data") }}?';
  if (date) url += 'date=' + date + '&';
  if (method) url += 'method=' + method;
  
  fetch(url, {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(r => r.json())
  .then(data => {
    topups = data || [];
    renderTopups();
  })
  .catch(err => {
    console.error('Error loading topups:', err);
  });
}

function renderRecentMembers() {
  const list = document.getElementById('recentMembersList');
  if (!list) return;
  const recentMembers = members.filter(m => m.status === 'active').slice(0, 5);
  
  list.innerHTML = recentMembers.map(m => `
    <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="selectMember(${m.id})">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <strong>${m.name}</strong>
          <br><small class="text-muted">${m.card_number}</small>
        </div>
        <span class="badge bg-label-primary">TZS ${m.balance.toLocaleString()}</span>
      </div>
    </li>
  `).join('');
}

function renderBulkMembersList() {
  const container = document.getElementById('bulkMembersList');
  if (!container) return;
  container.innerHTML = members.filter(m => m.status === 'active').map(m => `
    <div class="form-check custom-option border rounded p-2 mb-2">
      <input class="form-check-input ms-0 bulk-member-check" type="checkbox" value="${m.id}" id="bulk_member_${m.id}" onchange="updateBulkSummary()">
      <label class="form-check-label d-flex justify-content-between align-items-center w-100 ps-2" for="bulk_member_${m.id}">
        <div>
          <span class="fw-bold d-block">${m.name}</span>
          <small class="text-muted">${m.card_number || 'No Card'}</small>
        </div>
        <span class="badge bg-label-secondary small">TZS ${m.balance.toLocaleString()}</span>
      </label>
    </div>
  `).join('');
}

function updateBulkSummary() {
  const amount = parseInt(document.getElementById('bulk_amount').value) || 0;
  const checked = document.querySelectorAll('.bulk-member-check:checked');
  const count = checked.length;
  
  document.getElementById('selectedCount').textContent = count;
  
  const summary = document.getElementById('bulkSummary');
  if (count > 0 && amount > 0) {
    document.getElementById('bulkTotalAmount').textContent = 'TZS ' + (count * amount).toLocaleString();
    summary.classList.remove('d-none');
  } else {
    summary.classList.add('d-none');
  }
}

function renderTopups(filteredTopups = null) {
  const tbody = document.getElementById('topupsTableBody');
  if (!tbody) return;
  const displayTopups = filteredTopups || topups;
  
  if (displayTopups.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted opacity-50"><i class="ri-history-line ri-2x d-block mb-2"></i>No data found</td></tr>';
    document.getElementById('showingCount').textContent = '0';
    return;
  }
  
  tbody.innerHTML = displayTopups.map(t => {
    const date = new Date(t.created_at);
    const method = t.payment_method === 'mobile' ? 'mobile_money' : t.payment_method;
    const badgeClass = method === 'cash' ? 'success' : (method === 'mobile_money' ? 'warning' : 'info');
    
    return `
      <tr class="border-transparent">
        <td class="ps-4 py-3">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-2">
              <div class="avatar-initial rounded bg-label-primary">
                ${(t.member_name || 'M').substring(0, 2).toUpperCase()}
              </div>
            </div>
            <div>
              <strong>${t.member_name}</strong>
              <div class="small text-muted">
                <i class="ri ri-time-line me-1"></i>${date.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}, ${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                ${t.card_number ? `<span class="mx-1">•</span> <code class="text-primary">${t.card_number}</code>` : ''}
              </div>
            </div>
          </div>
        </td>
        <td class="text-end py-3">
          <h6 class="mb-0 fw-bold text-success">+TZS ${t.amount.toLocaleString()}</h6>
        </td>
        <td class="text-end py-3">
          <div class="d-flex flex-column align-items-end">
            <small class="text-muted strike-through" style="font-size: 10px;">Before: ${t.balance_before.toLocaleString()}</small>
            <strong class="text-dark">After: ${t.balance_after.toLocaleString()}</strong>
          </div>
        </td>
        <td class="text-center py-3">
          <span class="badge bg-label-${badgeClass} rounded-pill">
            ${method.replace('_', ' ').charAt(0).toUpperCase() + method.replace('_', ' ').slice(1)}
          </span>
        </td>
        <td class="pe-4 text-center py-3">
          <div class="dropdown">
            <button class="btn btn-sm btn-icon btn-label-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
              <i class="icon-base ri ri-more-2-fill"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
              <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="viewReceipt(${t.id})">
                <i class="ri ri-file-list-3-line me-2 text-primary"></i>View Receipt
              </a></li>
              <li><a class="dropdown-item py-2" href="{{ url('payments/top-ups') }}/${t.id}/receipt" target="_blank">
                <i class="ri ri-printer-line me-2 text-secondary"></i>Print Receipt
              </a></li>
            </ul>
          </div>
        </td>
      </tr>
    `;
  }).join('');
  
  if (document.getElementById('showingCount')) {
    document.getElementById('showingCount').textContent = displayTopups.length;
  }
}

function updateStats() {
  const stats = @json($stats ?? []);
  if (document.getElementById('topupsToday')) document.getElementById('topupsToday').textContent = (stats.topups_today || 0).toLocaleString();
  if (document.getElementById('amountToday')) document.getElementById('amountToday').textContent = 'TZS ' + (stats.amount_today || 0).toLocaleString();
  if (document.getElementById('totalBalance')) document.getElementById('totalBalance').textContent = 'TZS ' + (stats.total_balance || 0).toLocaleString();
  if (document.getElementById('activeMembers')) document.getElementById('activeMembers').textContent = (stats.active_members || 0).toLocaleString();
}

function searchMember(query) {
  const suggestionList = document.getElementById('memberSuggestions');
  if (!suggestionList) return;

  // Trim query to handle leading/trailing spaces
  const qClean = query.trim();

  if (qClean.length < 2) {
    suggestionList.innerHTML = '';
    suggestionList.style.display = 'none';
    document.getElementById('memberInfo').classList.add('d-none');
    selectedMemberId = null;
    return;
  }
  
  // 1. Try local filter first
  const qLower = qClean.toLowerCase();
  let filtered = members.filter(m => 
    (m.card_number && m.card_number.toLowerCase().includes(qLower)) || 
    (m.name && m.name.toLowerCase().includes(qLower)) ||
    (m.phone && m.phone.includes(qLower))
  ).slice(0, 10);
  
  if (filtered.length > 0) {
    renderSuggestions(filtered);
  } else {
    // 2. Fallback to server search with debounce
    if (searchTimeout) clearTimeout(searchTimeout);
    
    suggestionList.innerHTML = '<div class="list-group-item text-muted small py-3"><span class="spinner-border spinner-border-sm me-2 text-primary"></span>Finding member...</div>';
    suggestionList.style.display = 'block';

    searchTimeout = setTimeout(() => {
      fetch('{{ route("payments.members.search") }}?q=' + encodeURIComponent(qClean), {
        headers: { 'Accept': 'application/json' }
      })
      .then(r => r.json())
      .then(data => {
        if (data && data.length > 0) {
          // Add newly found members to cache
          data.forEach(m => { searchCache[m.id] = m; });
          renderSuggestions(data.slice(0, 10));
        } else {
          suggestionList.innerHTML = '<div class="list-group-item text-danger small py-3"><i class="ri-error-warning-line me-1"></i>No members found for "' + qClean + '"</div>';
          suggestionList.style.display = 'block';
        }
      })
      .catch(err => {
        console.error('Search error:', err);
        suggestionList.style.display = 'none';
      });
    }, 400);
  }
}

function renderSuggestions(list) {
  const suggestionList = document.getElementById('memberSuggestions');
  suggestionList.innerHTML = list.map(m => `
    <a href="javascript:void(0)" class="list-group-item list-group-item-action py-2 px-3 border-bottom" onclick="selectMember(${m.id})">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold text-dark mb-0">${m.name}</div>
          <small class="text-muted d-block" style="font-size: 0.75rem;">${m.card_number || 'No Card'} • ${m.phone || 'No Phone'}</small>
        </div>
        <div class="text-end">
          <span class="badge bg-label-primary rounded-pill">TZS ${parseFloat(m.balance || 0).toLocaleString()}</span>
        </div>
      </div>
    </a>
  `).join('');
  suggestionList.style.display = 'block';
}

function selectMember(id) {
  // Look in preloaded members or search cache
  let member = members.find(m => m.id == id) || searchCache[id];
  
  if (!member) {
    // Last resort: fetch if still not found
    fetch('{{ url("payments/members") }}/' + id, {
      headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
      if (data.member) {
        searchCache[data.member.id] = data.member;
        selectMemberDetails(data.member);
      }
    });
    return;
  }
  
  selectMemberDetails(member);
}

function selectMemberDetails(member) {
  selectedMemberId = member.id;
  currentMemberBalance = parseFloat(member.balance || 0);
  
  // Update UI
  const searchInput = document.getElementById('card_number_search');
  if (searchInput) searchInput.value = member.name;
  
  const suggestionList = document.getElementById('memberSuggestions');
  if (suggestionList) suggestionList.style.display = 'none';
  
  document.getElementById('selected_member_id').value = member.id;
  document.getElementById('memberInitial').textContent = (member.name || 'M').charAt(0).toUpperCase();
  document.getElementById('memberName').textContent = member.name || 'N/A';
  document.getElementById('memberCard').textContent = member.card_number || 'No Card';
  document.getElementById('memberBalance').textContent = 'TZS ' + currentMemberBalance.toLocaleString();
  
  document.getElementById('memberInfo').classList.remove('d-none');
  updateNewBalance();
}

function setAmount(amount) {
  const amountInput = document.getElementById('amount');
  if (amountInput) {
    amountInput.value = amount;
    // Trigger the update
    updateNewBalance();
  }
}

// Global listener to hide suggestions when clicking outside
document.addEventListener('click', function(e) {
  const suggestions = document.getElementById('memberSuggestions');
  const searchInput = document.getElementById('card_number_search');
  if (suggestions && !suggestions.contains(e.target) && e.target !== searchInput) {
    suggestions.style.display = 'none';
  }
});


function updateNewBalance() {
  const amountInput = document.getElementById('amount');
  const amount = parseInt(amountInput.value) || 0;
  const preview = document.getElementById('newBalancePreview');
  
  if (selectedMemberId && amount > 0) {
    const newBalance = currentMemberBalance + amount;
    document.getElementById('newBalanceAmount').textContent = 'TZS ' + newBalance.toLocaleString();
    preview.style.display = 'block';
  } else {
    preview.style.display = 'none';
  }
}

function processTopup(e) {
  e.preventDefault();
  if (!selectedMemberId) return showWarning('Please select a member first');
  
  const amount = parseInt(document.getElementById('amount').value);
  if (!amount || amount < 1000) return showWarning('Minimum TZS 1,000 required');

  const submitBtn = e.target.querySelector('button[type="submit"]');
  const originalText = submitBtn ? submitBtn.innerHTML : '';
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';
  }
  
  const formData = {
    member_id: parseInt(selectedMemberId),
    amount: amount,
    payment_method: document.getElementById('payment_method').value,
    reference_number: document.getElementById('reference_number').value || null,
    send_sms: document.getElementById('send_sms').checked
  };
  
  fetch('{{ route("payments.top-ups.store") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify(formData)
  })
  .then(r => r.json())
  .then(data => {
    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalText; }
    
    if (data.success) {
      const topup = data.topup;
      if (document.getElementById('print_receipt').checked) {
        showReceipt({
          id: topup.id,
          member_name: topup.member?.name || 'N/A',
          card_number: topup.member?.card_number || 'N/A',
          amount: parseFloat(topup.amount),
          balance_before: parseFloat(topup.balance_before),
          balance_after: parseFloat(topup.balance_after),
          payment_method: topup.payment_method,
          reference: topup.reference_number || '',
          created_at: topup.created_at
        });
      }
      
      document.getElementById('topupForm').reset();
      document.getElementById('memberInfo').classList.add('d-none');
      document.getElementById('newBalancePreview').style.display = 'none';
      selectedMemberId = null;
      loadTopups();
      showSuccess('Top-up processed successfully');
    } else showError(data.message || 'Error processing top-up');
  });
}

function showReceipt(topup) {
  const receiptContent = document.getElementById('receiptContent');
  const createdDate = topup.created_at ? new Date(topup.created_at) : new Date();
  
  receiptContent.innerHTML = `
    <div class="text-center mb-4">
      <h4 class="mb-1 fw-bold">GOLF CLUB</h4>
      <p class="mb-0 text-muted small text-uppercase letter-spacing-1">Wallet Top-up Slip</p>
    </div>
    <div class="d-flex justify-content-between mb-2 small">
      <span class="text-muted">Serial ID:</span>
      <span class="fw-bold">#TEMP-${topup.id}</span>
    </div>
    <div class="d-flex justify-content-between mb-4 small">
      <span class="text-muted">Date:</span>
      <span>${createdDate.toLocaleString()}</span>
    </div>
    
    <div class="py-3 border-top border-bottom border-dashed mb-4">
      <div class="d-flex justify-content-between mb-2">
        <span class="text-muted">Member</span>
        <span class="fw-bold text-end">${topup.member_name}</span>
      </div>
      <div class="d-flex justify-content-between mb-0">
        <span class="text-muted">Card No.</span>
        <code>${topup.card_number}</code>
      </div>
    </div>
    
    <div class="mb-4 text-center">
      <small class="text-muted d-block mb-1">Top-up Amount</small>
      <h3 class="fw-bold text-success mb-0">TZS ${parseFloat(topup.amount).toLocaleString()}</h3>
    </div>
    
    <div class="bg-light p-3 rounded-3 mb-4">
      <div class="d-flex justify-content-between mb-1 small">
        <span class="text-muted">Prev Balance:</span>
        <span>TZS ${parseFloat(topup.balance_before).toLocaleString()}</span>
      </div>
      <div class="d-flex justify-content-between small">
        <span class="text-dark fw-bold">New Balance:</span>
        <span class="fw-bold">TZS ${parseFloat(topup.balance_after).toLocaleString()}</span>
      </div>
    </div>
    
    <div class="small mb-2">
      <span class="text-muted">Payment:</span>
      <span class="fw-semibold ms-1">${topup.payment_method.toUpperCase()}</span>
    </div>
    ${topup.reference ? `<div class="small"><span class="text-muted">Ref:</span> <span class="ms-1">${topup.reference}</span></div>` : ''}
    
    <div class="text-center mt-4 pt-3 border-top border-dashed">
      <p class="mb-0 small text-muted">Thank you for your patronage</p>
    </div>
  `;
  
  const pdfLink = document.getElementById('receiptPdfLink');
  if (pdfLink) pdfLink.href = '{{ route("payments.top-ups.receipt", ":id") }}'.replace(':id', topup.id);
  new bootstrap.Modal(document.getElementById('receiptModal')).show();
}

function viewReceipt(id) {
  const topup = topups.find(t => t.id == id);
  if (topup) {
    showReceipt({
      id: topup.id,
      member_name: topup.member_name || topup.member?.name || 'N/A',
      card_number: topup.card_number || topup.member?.card_number || 'N/A',
      amount: parseFloat(topup.amount || 0),
      balance_before: parseFloat(topup.balance_before || 0),
      balance_after: parseFloat(topup.balance_after || 0),
      payment_method: topup.payment_method || 'cash',
      reference: topup.reference || topup.reference_number || '',
      created_at: topup.created_at
    });
  } else {
    fetch('{{ url("payments/top-ups") }}/' + id, {
      headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
      if (data.success && data.topup) {
        showReceipt({
          id: data.topup.id,
          member_name: data.topup.member?.name || 'N/A',
          card_number: data.topup.member?.card_number || 'N/A',
          amount: parseFloat(data.topup.amount || 0),
          balance_before: parseFloat(data.topup.balance_before || 0),
          balance_after: parseFloat(data.topup.balance_after || 0),
          payment_method: data.topup.payment_method || 'cash',
          reference: data.topup.reference_number || '',
          created_at: data.topup.created_at
        });
      }
    })
    .catch(err => {
      console.error('Error loading receipt:', err);
      showError('Failed to load receipt details');
    });
  }
}



// SMS resend would need to be implemented via API endpoint

function filterTopups() {
  // Reload topups from server with filters
  loadTopups();
}

function selectAllMembers() {
  document.querySelectorAll('.bulk-member-check').forEach(cb => cb.checked = true);
  updateBulkSummary();
}

function deselectAllMembers() {
  document.querySelectorAll('.bulk-member-check').forEach(cb => cb.checked = false);
  updateBulkSummary();
}

function updateBulkSummary() {
  const checked = document.querySelectorAll('.bulk-member-check:checked');
  const amount = parseInt(document.getElementById('bulk_amount').value) || 0;
  const total = checked.length * amount;
  
  if (document.getElementById('selectedCount')) document.getElementById('selectedCount').textContent = checked.length;
  if (document.getElementById('bulkTotalAmount')) document.getElementById('bulkTotalAmount').textContent = 'TZS ' + total.toLocaleString();
  const summary = document.getElementById('bulkSummary');
  if (summary) summary.style.display = checked.length > 0 && amount > 0 ? 'block' : 'none';
}

document.getElementById('bulk_amount')?.addEventListener('input', updateBulkSummary);

function processBulkTopup(e) {
  e.preventDefault();
  
  const checked = document.querySelectorAll('.bulk-member-check:checked');
  if (checked.length === 0) {
    showWarning('Please select at least one member');
    return;
  }
  
  const amount = parseInt(document.getElementById('bulk_amount').value);
  if (!amount || amount < 1000) {
    showWarning('Please enter a valid amount (minimum TZS 1,000)');
    return;
  }
  
  const paymentMethod = document.getElementById('bulk_payment_method').value;
  const reference = document.getElementById('bulk_reference').value;
  
  const submitBtn = e.target.querySelector('button[type="submit"]');
  const originalText = submitBtn ? submitBtn.innerHTML : '';
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';
  }
  
  // Process each member topup sequentially
  const memberIds = Array.from(checked).map(cb => parseInt(cb.value));
  let processed = 0;
  let failed = 0;
  
  memberIds.forEach((memberId, index) => {
    const formData = {
      member_id: memberId,
      amount: amount,
      payment_method: paymentMethod,
      reference_number: reference || null,
      notes: 'Bulk top-up',
      send_sms: false
    };
    
    fetch('{{ route("payments.top-ups.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
      processed++;
      if (data.success) {
        // Success - continue
      } else {
        failed++;
      }
      
      // When all requests are done
      if (processed + failed >= memberIds.length) {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
        
        bootstrap.Modal.getInstance(document.getElementById('bulkTopupModal')).hide();
        document.getElementById('bulkTopupForm').reset();
        deselectAllMembers();
        
        // Reload data
        loadTopups();
        setTimeout(() => location.reload(), 1000);
        
        if (failed > 0) {
          showWarning(`Bulk top-up completed with ${failed} error(s).\n\n${processed} members topped up successfully with TZS ${amount.toLocaleString()} each.`);
        } else {
          showSuccess(`${processed} members topped up successfully with TZS ${amount.toLocaleString()} each.`, 'Bulk Top-up Successful!');
        }
      }
    })
    .catch(error => {
      console.error('Error:', error);
      failed++;
      processed++;
      
      if (processed + failed >= memberIds.length) {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
        
        bootstrap.Modal.getInstance(document.getElementById('bulkTopupModal')).hide();
        document.getElementById('bulkTopupForm').reset();
        deselectAllMembers();
        
        loadTopups();
        setTimeout(() => location.reload(), 1000);
        
        showWarning(`Bulk top-up completed with ${failed} error(s).\n\n${processed - failed} members topped up successfully.`);
      }
    });
  });
}

function exportTopups() {
  const date = document.getElementById('filterDate')?.value;
  const method = document.getElementById('filterMethod')?.value;
  
  let url = '{{ route("payments.top-ups.data") }}?';
  if (date) url += 'date=' + date + '&';
  if (method) url += 'method=' + method;
  
  fetch(url, {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (!data || data.length === 0) {
      showInfo('No top-up records to export.');
      return;
    }
    
    let csv = 'Date,Time,Member,Card Number,Amount,Balance Before,Balance After,Payment Method,Reference,SMS Sent\n';
    
    data.forEach(t => {
      const date = new Date(t.created_at);
      csv += `"${date.toLocaleDateString()}","${date.toLocaleTimeString()}","${t.member_name || 'N/A'}","${t.card_number || 'N/A'}",${t.amount},${t.balance_before},${t.balance_after},"${t.payment_method || ''}","${t.reference || ''}","${t.sms_sent ? 'Yes' : 'No'}"\n`;
    });
    
    try {
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'topups_' + new Date().toISOString().split('T')[0] + '.csv';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
    } catch (error) {
      console.error('Export error:', error);
      showError('Error exporting top-ups. Please try again.');
    }
  })
  .catch(error => {
    console.error('Export error:', error);
    showError('Error exporting top-ups. Please try again.');
  });
}
</script>
@endpush
@endsection
