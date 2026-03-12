@extends('settings._layout-base')

@section('title', 'Equipment Rentals')
@section('description', 'Equipment Rental Management - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Golf Services /</span> Equipment Rentals
</h4>

{{-- ============================================================ --}}
{{-- HERO INVENTORY BANNER (Ball Management style) --}}
{{-- ============================================================ --}}
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="card-body p-0">
        <div class="row g-0">

          {{-- Left: Inventory Overview --}}
          <div class="col-md-8 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0 fw-bold"><i class="ri ri-inbox-archive-line me-2 text-primary"></i>Live Equipment Inventory</h5>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#inventorySettingsModal">
                  <i class="ri ri-stack-line me-1"></i> Manage Inventory
                </button>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newRentalModal">
                  <i class="ri ri-add-line me-1"></i> New Rental
                </button>
              </div>
            </div>

            @php
              $statAvail  = $stats['available_items'] ?? 0;
              $statActive = $stats['active_rentals'] ?? 0;
              $statMaint  = $stats['under_maintenance'] ?? 0;
              $statTotal  = $statAvail + $statActive + $statMaint;
              $availPct   = $statTotal > 0 ? ($statAvail / $statTotal) * 100 : 0;
              $activePct  = $statTotal > 0 ? ($statActive / $statTotal) * 100 : 0;
              $maintPct   = $statTotal > 0 ? ($statMaint / $statTotal) * 100 : 0;
            @endphp

            {{-- Inventory progress bar --}}
            <div class="progress mb-4" style="height: 42px; border-radius: 12px; background-color: #f1f3f9;">
              <div class="progress-bar bg-success" role="progressbar" style="width: {{ $availPct }}%">
                <span class="fw-bold">{{ number_format($statAvail) }} Available</span>
              </div>
              <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $activePct }}%">
                <span class="fw-bold">{{ number_format($statActive) }} Rented</span>
              </div>
              <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $maintPct }}%"></div>
            </div>

            <div class="row g-4">
              <div class="col-md-4">
                <div class="p-3 border rounded bg-light-subtle">
                  <small class="text-muted d-block mb-1">Total Equipment</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($statTotal) }} <span class="fs-6 fw-normal text-muted">Items</span></h4>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 border border-success rounded bg-success-subtle">
                  <small class="text-success d-block mb-1">Available Now</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($statAvail) }}</h4>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 border border-warning rounded bg-warning-subtle text-warning">
                  <small class="d-block mb-1">Currently Rented</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($statActive) }}</h4>
                </div>
              </div>
            </div>
          </div>

          {{-- Right: Revenue summary --}}
          <div class="col-md-4 bg-primary text-white p-4">
            <h5 class="text-white fw-bold mb-4">Today's Rental Summary</h5>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>Active Rentals</span>
                <strong>{{ $statActive }}</strong>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-white" style="width: 70%"></div>
              </div>
            </div>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>Under Maintenance</span>
                <strong>{{ $statMaint }}</strong>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: 30%"></div>
              </div>
            </div>
            <div class="pt-2">
              <div class="alert bg-white text-primary border-0 mb-0 py-3">
                <small class="d-block fw-semibold mb-1">Revenue Today</small>
                <h4 class="mb-0 fw-bold">TZS {{ number_format($stats['revenue_today'] ?? 0) }}</h4>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================ --}}
{{-- PRIMARY ACTIONS: New Rental Form (col-8) + Active Rentals (col-4) --}}
{{-- ============================================================ --}}
<div class="row mb-6">

  {{-- New Rental Form (col-8) --}}
  <div class="col-md-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="ri ri-send-plane-fill me-2"></i>Issue New Rental</h5>
      </div>
      <div class="card-body">
        <form id="newRentalForm">
          @csrf
          <div class="row">

            {{-- Left half: Member search --}}
            <div class="col-md-6">
              <div class="mb-4 position-relative">
                <label class="form-label fw-bold">1. Find Member</label>
                <div class="input-group input-group-lg border rounded shadow-sm">
                  <span class="input-group-text bg-white border-0"><i class="ri ri-search-2-line text-muted"></i></span>
                  <input type="text" class="form-control border-0 px-1" id="rental_customer_search"
                         placeholder="Search by name or card #..." autocomplete="off">
                </div>
                <div id="rentalCustomerSuggestions" class="list-group position-absolute w-100 shadow-lg border-0 mt-1"
                     style="z-index:1000; display:none; max-height:260px; overflow-y:auto; border-radius:0 0 12px 12px;"></div>
                <input type="hidden" id="rental_member_id" name="member_id">
              </div>

              {{-- Member info card --}}
              <div class="card bg-label-primary border-0 mb-4" id="memberBalanceAlert" style="display:none;">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                      <span class="avatar-initial rounded bg-primary"><i class="ri ri-user-star-line"></i></span>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0 fw-bold" id="rentalMemberName">-</h6>
                      <small class="text-primary" id="rentalMemberCard">-</small>
                    </div>
                    <div class="text-end">
                      <small class="text-muted d-block">Balance</small>
                      <h6 class="mb-0 fw-bold text-success" id="memberBalanceDisplay" data-balance="0">TZS 0</h6>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="rental_notes" name="notes" style="height:70px" placeholder="Additional details"></textarea>
                <label>Internal Notes (Optional)</label>
              </div>
            </div>

            {{-- Right half: Equipment + type + dates --}}
            <div class="col-md-6 border-start">
              <label class="form-label fw-bold">2. Select Equipment &amp; Plan</label>
              <select class="form-select form-select-lg mb-3" id="rental_equipment" name="equipment_id" required>
                <option value="">Select Equipment...</option>
                @foreach($equipment ?? [] as $eq)
                <option value="{{ $eq->id }}" data-hourly="{{ $eq->rental_hourly_rate }}" data-daily="{{ $eq->rental_daily_rate }}" data-available="{{ $eq->available_quantity }}">
                  {{ $eq->name }} ({{ $eq->available_quantity }} available)
                </option>
                @endforeach
              </select>

              <div class="row g-2 mb-3">
                <div class="col-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control fw-bold" id="rental_quantity" name="quantity" value="1" min="1" required>
                    <label>Qty</label>
                  </div>
                </div>
                <div class="col-8">
                  <div class="form-floating form-floating-outline">
                    <select class="form-select fw-bold text-primary" id="rental_type" name="rental_type" required>
                      <option value="hourly">HOURLY (Short Term)</option>
                      <option value="daily" selected>DAILY (Full Session)</option>
                    </select>
                    <label>Rate Plan</label>
                  </div>
                </div>
              </div>

              <div class="form-floating form-floating-outline mb-3">
                <input type="datetime-local" class="form-control" id="rental_expected_return" name="expected_return" required>
                <label>Expected Return</label>
              </div>

              {{-- Cost preview --}}
              <div class="p-3 bg-label-info rounded-3" id="rentalCostPreview">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="mb-1 fw-bold">Estimated Cost</h6>
                    <small id="selectedRateLabel" class="text-muted fw-bold d-block">Select equipment first</small>
                  </div>
                  <div class="text-end">
                    <h3 class="mb-0 fw-bold text-primary" id="estimatedCost">TZS -</h3>
                    <small class="text-muted">Payable on return</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <button type="button" class="btn btn-primary btn-lg w-100 py-3 shadow-sm fw-bold" onclick="submitRental()">
              <i class="ri ri-check-line me-2"></i> CONFIRM &amp; CREATE RENTAL
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Active Rentals Panel (col-4) --}}
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-success">
          <i class="ri ri-external-link-line me-2"></i>Active Rentals
          @if(($stats['active_rentals'] ?? 0) > 0)
          <span class="badge bg-danger rounded-pill ms-1">{{ $stats['active_rentals'] }}</span>
          @endif
        </h5>
        <button class="btn btn-sm btn-label-secondary" onclick="location.reload()">
          <i class="ri ri-refresh-line"></i>
        </button>
      </div>
      <div class="card-body p-0">
        <div class="alert alert-info border-0 mb-0 py-2 px-4 rounded-0">
          <small><i class="ri ri-information-line me-1"></i>Click RETURN when equipment is handed back.</small>
        </div>
        <div class="list-group list-group-flush">
          @forelse($activeRentals ?? [] as $rental)
          <div class="list-group-item px-4 py-3">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <div>
                <h6 class="mb-0 fw-bold small">{{ $rental->member->name ?? $rental->customer_name }}</h6>
                <small class="text-muted">{{ $rental->quantity }}x {{ $rental->equipment->name ?? '-' }}</small>
              </div>
              @if(now()->gt($rental->expected_return))
                <span class="badge bg-label-danger animate-pulse">OVERDUE</span>
              @else
                <span class="badge bg-label-success">ACTIVE</span>
              @endif
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <small class="text-muted"><i class="ri ri-time-line me-1"></i>Return by {{ $rental->expected_return->format('d M H:i') }}</small>
              <button class="btn btn-sm btn-success px-3 py-1 fw-bold" onclick="returnRental({{ $rental->id }})">
                <i class="ri ri-reply-fill me-1"></i>RETURN
              </button>
            </div>
          </div>
          @empty
          <div class="text-center py-5 text-muted">
            <i class="ri ri-inbox-line ri-2x d-block mb-2 opacity-25"></i>
            <p class="mb-0 fw-semibold">No active rentals</p>
            <small>Create a rental to get started.</small>
          </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================ --}}
{{-- RENTAL HISTORY TABLE (full-width) --}}
{{-- ============================================================ --}}
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold"><i class="ri ri-history-line me-2 text-primary"></i>Recent Rental History</h5>
        <button class="btn btn-sm btn-label-secondary" onclick="location.reload()">
          <i class="ri ri-refresh-line me-1"></i> Refresh
        </button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4 text-uppercase small fw-bold">ID</th>
                <th class="text-uppercase small fw-bold">Customer</th>
                <th class="text-uppercase small fw-bold">Equipment</th>
                <th class="text-uppercase small fw-bold">Duration</th>
                <th class="text-end text-uppercase small fw-bold">Amount</th>
                <th class="pe-4 text-center text-uppercase small fw-bold">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rentalHistory ?? [] as $rental)
              <tr>
                <td class="ps-4"><small class="text-muted">#{{ $rental->id }}</small></td>
                <td>
                  <h6 class="mb-0 fw-bold">{{ $rental->member->name ?? $rental->customer_name }}</h6>
                  <small class="text-muted">{{ $rental->member->card_number ?? 'Walk-in' }}</small>
                </td>
                <td>
                  <span class="fw-bold">{{ $rental->quantity }}×</span> {{ $rental->equipment->name ?? '-' }}
                  <br><small class="text-muted badge bg-label-secondary">{{ $rental->rental_type }}</small>
                </td>
                <td>
                  <small class="text-muted">
                    {{ $rental->start_time->format('d M H:i') }} →
                    {{ $rental->actual_return ? $rental->actual_return->format('H:i') : '-' }}
                  </small>
                </td>
                <td class="text-end">
                  <strong class="text-primary">TZS {{ number_format($rental->total_amount) }}</strong>
                </td>
                <td class="pe-4 text-center">
                  <span class="badge bg-label-secondary">RETURNED</span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="ri ri-history-line ri-2x d-block mb-2 opacity-25"></i>
                  No rental history found
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================ --}}
{{-- MODALS --}}
{{-- ============================================================ --}}

{{-- Inventory Settings Modal --}}
<div class="modal fade" id="inventorySettingsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-label-secondary p-4">
        <h5 class="modal-title fw-bold"><i class="ri ri-stack-line me-2"></i>Equipment Inventory Management</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <div class="nav-align-left h-100">
          <ul class="nav nav-tabs border-0 flex-column w-25 h-100 bg-light" role="tablist">
            <li class="nav-item">
              <button class="nav-link active py-3 border-0 rounded-0" data-bs-toggle="tab" data-bs-target="#tab-equipment-list">
                <i class="ri ri-list-check me-2"></i>All Equipment
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link py-3 border-0 rounded-0" data-bs-toggle="tab" data-bs-target="#tab-add-equipment">
                <i class="ri ri-add-circle-line me-2"></i>Add New Item
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link py-3 border-0 rounded-0" data-bs-toggle="tab" data-bs-target="#tab-pricing">
                <i class="ri ri-money-dollar-circle-line me-2"></i>Pricing &amp; Rates
              </button>
            </li>
          </ul>
          <div class="tab-content border-0 p-4 w-75 h-100">
            {{-- List Tab --}}
            <div class="tab-pane fade show active" id="tab-equipment-list" role="tabpanel">
              <div class="table-responsive">
                <table class="table table-sm table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Name / SKU</th>
                      <th class="text-center">Total</th>
                      <th class="text-center">Available</th>
                      <th class="text-center">Status</th>
                      <th class="text-end">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($equipment as $eq)
                    <tr>
                      <td>
                        <div class="fw-bold">{{ $eq->name }}</div>
                        <small class="text-muted">{{ $eq->sku }}</small>
                      </td>
                      <td class="text-center">{{ $eq->total_quantity }}</td>
                      <td class="text-center">
                        <span class="badge bg-label-{{ $eq->available_quantity > 5 ? 'success' : 'warning' }}">{{ $eq->available_quantity }}</span>
                      </td>
                      <td class="text-center">
                        <span class="badge bg-{{ $eq->status === 'active' ? 'success' : 'danger' }} p-1 rounded-circle" title="{{ ucfirst($eq->status) }}"></span>
                      </td>
                      <td class="text-end">
                        <button class="btn btn-icon btn-sm btn-label-primary" onclick="editEquipment({{ $eq->id }})"><i class="ri ri-pencil-line"></i></button>
                        <button class="btn btn-icon btn-sm btn-label-danger" onclick="deleteEquipment({{ $eq->id }}, '{{ addslashes($eq->name) }}')"><i class="ri ri-delete-bin-line"></i></button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            {{-- Add Tab --}}
            <div class="tab-pane fade" id="tab-add-equipment" role="tabpanel">
              <form id="equipmentForm">
                @csrf
                <div class="row g-3">
                  <div class="col-md-8">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control" id="equipment_name" placeholder="Full Name" required>
                      <label>Equipment Name *</label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control" id="equipment_sku" placeholder="SKU">
                      <label>SKU (Auto-generated if empty)</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                      <select class="form-select" id="equipment_category" required>
                        <option value="clubs">Clubs</option>
                        <option value="bags">Bags</option>
                        <option value="carts">Carts</option>
                        <option value="shoes">Shoes</option>
                        <option value="other">Other</option>
                      </select>
                      <label>Category *</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                      <input type="number" class="form-control" id="equipment_quantity" value="1" min="0" required>
                      <label>In Inventory *</label>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-floating form-floating-outline">
                      <textarea class="form-control" id="equipment_description" style="height:60px"></textarea>
                      <label>Brief Description</label>
                    </div>
                  </div>
                  <div class="col-12 mt-2">
                    <button type="button" class="btn btn-primary btn-lg w-100 py-3" onclick="saveEquipment()">
                      <i class="ri ri-save-line me-2"></i> CREATE EQUIPMENT RECORD
                    </button>
                  </div>
                </div>
              </form>
            </div>

            {{-- Pricing Tab --}}
            <div class="tab-pane fade" id="tab-pricing" role="tabpanel">
              <div class="alert alert-info border-0 mb-4">
                <i class="ri ri-information-line me-2"></i>Update rates and deposits for all rentable equipment.
              </div>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr><th>Equipment</th><th>Hourly Rate</th><th>Daily Rate</th><th>Deposit</th><th class="text-end">Save</th></tr>
                  </thead>
                  <tbody>
                    @foreach($equipment as $eq)
                    <tr>
                      <td class="fw-bold">{{ $eq->name }}</td>
                      <td><input type="number" class="form-control form-control-sm" id="rate_h_{{ $eq->id }}" value="{{ $eq->rental_hourly_rate }}"></td>
                      <td><input type="number" class="form-control form-control-sm" id="rate_d_{{ $eq->id }}" value="{{ $eq->rental_daily_rate }}"></td>
                      <td><input type="number" class="form-control form-control-sm" id="deposit_{{ $eq->id }}" value="{{ $eq->deposit_amount }}"></td>
                      <td class="text-end">
                        <button class="btn btn-icon btn-sm btn-label-success" onclick="quickUpdatePricing({{ $eq->id }})"><i class="ri ri-check-line"></i></button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
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
.bg-white-transparent { background-color: rgba(255,255,255,0.2); }
.bg-success-subtle  { background-color: #d1f2c0 !important; }
.bg-warning-subtle  { background-color: #fff2cc !important; }
.cursor-pointer { cursor: pointer; }
.hover-bg-light:hover { background-color: #f8f9fa; }
@keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.5; } }
.animate-pulse { animation: pulse 2s cubic-bezier(0.4,0,0.6,1) infinite; }
</style>
@endpush
@endsection

@push('scripts')
<script>
// ============================================================
// Return rental
// ============================================================
function returnRental(id) {
  showConfirm('Confirm equipment return? Late fees will be charged if applicable.').then(result => {
    if (result.isConfirmed) executeReturn(id);
  });
}
function executeReturn(id) {
  fetch('{{ route("golf-services.equipment-rental.return", ":id") }}'.replace(':id', id), {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => { data.success ? showSuccess(data.message).then(() => location.reload()) : showError(data.message); });
}

// ============================================================
// Member search
// ============================================================
let rentalSearchTimeout = null;
const rentalSearchInput = document.getElementById('rental_customer_search');
const rentalSuggestions = document.getElementById('rentalCustomerSuggestions');

rentalSearchInput?.addEventListener('input', function() {
  const query = this.value;
  if (query.length < 2) { rentalSuggestions.style.display = 'none'; return; }
  clearTimeout(rentalSearchTimeout);
  rentalSearchTimeout = setTimeout(() => {
    fetch('{{ url("payments/members/search") }}?q=' + encodeURIComponent(query), {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(members => {
      rentalSuggestions.innerHTML = '';
      if (!members.length) { rentalSuggestions.style.display = 'none'; return; }
      members.forEach(m => {
        const div = document.createElement('div');
        div.className = 'list-group-item list-group-item-action';
        div.style.cursor = 'pointer';
        div.innerHTML = `
          <div class="fw-bold">${m.name}</div>
          <div class="small text-muted d-flex justify-content-between">
            <span>${m.card_number || ''}</span>
            <span class="text-primary fw-bold">TZS ${parseFloat(m.balance).toLocaleString()}</span>
          </div>`;
        div.onclick = () => {
          document.getElementById('rental_member_id').value = m.id;
          rentalSearchInput.value = m.name;
          rentalSuggestions.style.display = 'none';

          document.getElementById('rentalMemberName').textContent = m.name;
          document.getElementById('rentalMemberCard').textContent = m.card_number || '-';
          const balEl = document.getElementById('memberBalanceDisplay');
          balEl.textContent = 'TZS ' + parseFloat(m.balance).toLocaleString();
          balEl.dataset.balance = m.balance;
          document.getElementById('memberBalanceAlert').style.display = 'block';
          updateCostPreview();
        };
        rentalSuggestions.appendChild(div);
      });
      rentalSuggestions.style.display = 'block';
    });
  }, 300);
});

document.addEventListener('click', e => {
  if (!e.target.closest('#rental_customer_search') && !e.target.closest('#rentalCustomerSuggestions')) {
    rentalSuggestions.style.display = 'none';
  }
});

// ============================================================
// Cost preview
// ============================================================
function updateCostPreview() {
  const eqSelect = document.getElementById('rental_equipment');
  const type = document.getElementById('rental_type').value;
  const qty = parseInt(document.getElementById('rental_quantity').value) || 1;
  const opt = eqSelect.options[eqSelect.selectedIndex];
  const balance = parseFloat(document.getElementById('memberBalanceDisplay').dataset.balance) || 0;

  if (opt?.value) {
    const rate = type === 'hourly' ? parseFloat(opt.dataset.hourly) : parseFloat(opt.dataset.daily);
    const cost = rate * qty;
    const available = parseInt(opt.dataset.available) || 0;
    document.getElementById('selectedRateLabel').textContent = `Rate: TZS ${rate.toLocaleString()} per ${type}`;
    document.getElementById('estimatedCost').textContent = 'TZS ' + cost.toLocaleString();
    const preview = document.getElementById('rentalCostPreview');
    preview.className = 'p-3 rounded-3 ' + (balance > 0 && balance < cost ? 'bg-label-danger' : available < qty ? 'bg-label-warning' : 'bg-label-info');
  } else {
    document.getElementById('selectedRateLabel').textContent = 'Select equipment first';
    document.getElementById('estimatedCost').textContent = 'TZS -';
    document.getElementById('rentalCostPreview').className = 'p-3 bg-label-secondary rounded-3';
  }
}

document.getElementById('rental_equipment')?.addEventListener('change', updateCostPreview);
document.getElementById('rental_type')?.addEventListener('change', updateCostPreview);
document.getElementById('rental_quantity')?.addEventListener('input', updateCostPreview);

// ============================================================
// Submit rental
// ============================================================
function submitRental() {
  const memberId = document.getElementById('rental_member_id').value;
  const equipmentId = document.getElementById('rental_equipment').value;
  const quantity = document.getElementById('rental_quantity').value;
  const rentalType = document.getElementById('rental_type').value;
  const expectedReturn = document.getElementById('rental_expected_return').value;

  if (!memberId || !equipmentId || !expectedReturn) { showError('Please search for a member and select equipment.'); return; }

  const btn = document.querySelector('button[onclick="submitRental()"]');
  const origText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

  fetch('{{ route("golf-services.equipment-rental.store") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify({ member_id: memberId, equipment_id: equipmentId, quantity, rental_type: rentalType, expected_return: expectedReturn, notes: document.getElementById('rental_notes').value })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) showSuccess(data.message).then(() => location.reload());
    else { showError(data.message); btn.disabled = false; btn.innerHTML = origText; }
  })
  .catch(() => { btn.disabled = false; btn.innerHTML = origText; showError('Network error while creating rental'); });
}

// Auto-set 4 hours for return
const expectedInput = document.getElementById('rental_expected_return');
if (expectedInput && !expectedInput.value) {
  const now = new Date();
  now.setHours(now.getHours() + 4);
  expectedInput.value = now.toISOString().slice(0, 16);
}

// ============================================================
// Equipment management
// ============================================================
function saveEquipment() {
  const formData = {
    name: document.getElementById('equipment_name').value,
    sku: document.getElementById('equipment_sku').value,
    category: document.getElementById('equipment_category').value,
    total_quantity: document.getElementById('equipment_quantity').value,
    description: document.getElementById('equipment_description').value,
    _token: '{{ csrf_token() }}'
  };
  fetch('{{ route("golf-services.equipment.store") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify(formData)
  }).then(r => r.json()).then(data => data.success ? showSuccess(data.message).then(() => location.reload()) : showError(data.message));
}

function quickUpdatePricing(id) {
  const data = {
    rental_hourly_rate: document.getElementById('rate_h_' + id).value,
    rental_daily_rate: document.getElementById('rate_d_' + id).value,
    deposit_amount: document.getElementById('deposit_' + id).value,
    name: 'price_update_bypass', sku: 'price_update_bypass', category: 'price_update_bypass'
  };
  fetch('{{ route("golf-services.equipment.update", ":id") }}'.replace(':id', id), {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify(data)
  }).then(r => r.json()).then(data => data.success ? showSuccess('Pricing updated!') : showError(data.message));
}
</script>
@endpush
