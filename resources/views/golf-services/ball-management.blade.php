@extends('settings._layout-base')

@section('title', 'Ball Management')
@section('description', 'Ball Management - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Golf Services /</span> Ball Management
</h4>

<!-- Visual Inventory & Quick Stats -->
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="card-body p-0">
        <div class="row g-0">
          <div class="col-md-8 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0 fw-bold"><i class="ri ri-inbox-archive-line me-2 text-primary"></i>Live Ball Inventory</h5>
               <div class="d-flex gap-2">
                <a href="{{ route('golf-services.ball-collection.index') }}" class="btn btn-sm btn-label-primary">
                  <i class="ri ri-user-follow-line me-1"></i> Manage Collection
                </a>
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Inventory Maintenance
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#maintenanceModal" data-tab="addStock"><i class="ri ri-add-line me-2"></i>Add New Stock</a></li>
                    <li><a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#maintenanceModal" data-tab="markDamaged"><i class="ri ri-error-warning-line me-2"></i>Mark Damaged</a></li>
                  </ul>
                </div>
              </div>
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
                <small class="d-block">Pending Return</small>
                <h5 class="mb-0 fw-bold">{{ number_format(($stats['issued_today'] ?? 0) - ($stats['returned_today'] ?? 0)) }} <span class="fs-6 fw-normal">Balls</span></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Primary Actions -->
<div class="row mb-6">
  <div class="col-md-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="ri ri-send-plane-fill me-2"></i>Quick Issue (Quick POS)</h5>
      </div>
      <div class="card-body">
        <form id="issueBallsForm">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="mb-4 position-relative">
                <label class="form-label fw-bold">1. Find Customer or Member</label>
                <div class="input-group input-group-lg border rounded shadow-sm">
                  <span class="input-group-text bg-white border-0"><i class="ri ri-search-2-line text-muted"></i></span>
                  <input type="text" class="form-control border-0 px-1" id="issue_customer_name" name="customer_name" placeholder="Search by name, card #, or phone..." required autocomplete="off" />
                </div>
                <div id="issueCustomerSuggestions" class="list-group position-absolute w-100 shadow-lg border-0 mt-1" style="z-index: 1000; display: none; max-height: 300px; overflow-y: auto; border-radius: 0 0 12px 12px;"></div>
                <input type="hidden" id="issue_member_id" name="member_id" />
              </div>

              <!-- Member Information Display -->
              <div class="card bg-label-primary border-0 mb-4" id="issueMemberInfo" style="display: none;">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                      <span class="avatar-initial rounded bg-primary"><i class="ri ri-user-star-line"></i></span>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0 fw-bold" id="issueMemberName">-</h6>
                      <small class="text-primary" id="issueMemberCard">-</small>
                    </div>
                    <div class="text-end" id="issueBalanceDisplay" style="display: none;">
                      <small class="text-muted d-block">Balance</small>
                      <h6 class="mb-0 fw-bold text-success" id="issueMemberBalance">TZS 0</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
              <label class="form-label fw-bold mt-4 mt-md-0">2. Select Quantity</label>
              <div class="row g-2 mb-4">
                <div class="col-6">
                  <button type="button" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" onclick="setIssueQuantity(50)">
                    <span class="fs-4 fw-bold">50</span>
                    <small>1 Bucket</small>
                  </button>
                </div>
                <div class="col-6">
                  <button type="button" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" onclick="setIssueQuantity(100)">
                    <span class="fs-4 fw-bold">100</span>
                    <small>2 Buckets</small>
                  </button>
                </div>
              </div>
              
              <div class="form-floating form-floating-outline mb-4">
                <input type="number" class="form-control form-control-lg" id="issue_quantity" name="quantity" value="50" min="1" required oninput="updateIssueAmount()" />
                <label>Custom Quantity</label>
              </div>
              
              <div class="mb-4">
                <label class="form-label fw-bold">3. Payment Method</label>
                <select class="form-select form-select-lg" id="issue_payment_method" name="payment_method" required onchange="updateIssueAmount()">
                  <option value="cash">CASH</option>
                  <option value="mobile_money">MOBILE MONEY</option>
                  <option value="balance">MEMBER BALANCE</option>
                  <option value="card">CARD (POS)</option>
                </select>
              </div>

              <div class="mb-4">
                <label class="form-label fw-bold text-success">4. Designated Collector</label>
                <select class="form-select form-select-lg border-success border-2" id="issue_collector_id" name="collector_id" required>
                  <option value="" selected disabled>-- Select Staff for Return --</option>
                  @foreach($collectors as $collector)
                    <option value="{{ $collector->id }}">{{ $collector->name }}</option>
                  @endforeach
                </select>
                <div class="form-text text-success"><i class="ri ri-information-line me-1"></i> This staff will be responsible for field recovery.</div>
              </div>

              <div class="alert alert-primary d-flex justify-content-between align-items-center p-3 mb-0 shadow-sm border-2">
                <h5 class="mb-0 fw-bold text-primary">Grand Total:</h5>
                <h3 class="mb-0 fw-bold text-primary" id="issueAmount">TZS 0</h3>
              </div>
            </div>
          </div>
          
          <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow-sm">
              <i class="ri ri-check-line me-2"></i> CONFIRM & ISSUE BALLS
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="mb-0 fw-bold text-success"><i class="ri ri-arrow-go-back-fill me-2"></i>Member Return</h5>
      </div>
      <div class="card-body">
        <div class="alert alert-info border-0 mb-4 py-2">
          <small><i class="ri ri-information-line me-1"></i> Scan member card or enter name to register return.</small>
        </div>
        <form id="returnBallsForm">
          @csrf
          <div class="form-floating form-floating-outline mb-4 position-relative">
            <input type="text" class="form-control form-control-lg text-dark fw-bold" id="return_customer_name" name="customer_name" placeholder="Member or Walk-in name" required autocomplete="off" />
            <label>Customer Name</label>
            <div id="returnCustomerSuggestions" class="list-group position-absolute w-100 shadow-lg border-0 mt-1 bg-white" style="z-index: 2000; display: none; max-height: 250px; overflow-y: auto; border-radius: 8px;"></div>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <input type="number" class="form-control form-control-lg" name="quantity" value="50" min="1" required />
            <label>Quantity to Return</label>
          </div>
          <div class="mb-4">
            <label class="form-label fw-bold"><i class="ri ri-user-received-2-line me-1 text-success"></i>Returning Collector</label>
            <select class="form-select form-select-lg border-success border-2" id="return_collector_id" name="collector_id">
              <option value="" selected>-- No Collector (Walk-in) --</option>
              @foreach($collectors as $collector)
                <option value="{{ $collector->id }}">{{ $collector->name }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-success btn-lg w-100 py-3">
            <i class="ri ri-check-double-line me-2"></i> CONFIRM RETURN
          </button>
        </form>
        
        <div class="mt-4 pt-2 border-top">
          <h6 class="fw-bold mb-3 small text-muted text-uppercase">Pending Recoveries (Collector Tasks)</h6>
          <div class="list-group list-group-flush">
            @forelse($pendingCollections as $log)
              <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                <div>
                  <h6 class="mb-0 small fw-bold text-success"><i class="ri ri-user-follow-line me-1"></i>{{ $log->collector->name }}</h6>
                  <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge bg-label-secondary" style="font-size: 0.65rem;">{{ $log->target_quantity }} balls</span>
                    <small class="text-muted" style="font-size: 0.7rem;">
                      For: {{ $log->ballTransaction ? $log->ballTransaction->customer_name : 'Guest' }}
                    </small>
                  </div>
                </div>
                <button class="btn btn-sm btn-label-success px-2 py-1" onclick="quickReturn('{{ $log->ballTransaction ? addslashes($log->ballTransaction->customer_name) : '' }}', {{ $log->target_quantity }}, {{ $log->collector_id }})">Return</button>
              </div>
            @empty
              <div class="text-center py-2 text-muted small">No pending recoveries reported.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filters & History -->
<div class="card mb-6 border-0 shadow-sm">
  <div class="card-body">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <form method="GET" action="{{ route('golf-services.ball-management') }}" id="filterForm" class="row g-3 flex-grow-1">
        <div class="col-md-4">
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ri ri-calendar-line"></i></span>
            <input type="date" class="form-control" name="from_date" id="from_date" value="{{ $fromDate->format('Y-m-d') }}" />
            <span class="input-group-text">to</span>
            <input type="date" class="form-control" name="to_date" id="to_date" value="{{ $toDate->format('Y-m-d') }}" />
          </div>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
        </div>
      </form>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">Today</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('yesterday')">Yesterday</button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('this_week')">This Week</button>
      </div>
    </div>
  </div>
</div>

<!-- Transactions Table -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0">Ball Transactions</h5>
          <small class="text-muted">{{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</small>
        </div>
        <div>
          <span class="badge bg-success me-2">Issued: {{ number_format($transactions->where('type', 'issued')->sum('quantity')) }}</span>
          <span class="badge bg-info">Returned: {{ number_format($transactions->where('type', 'returned')->sum('quantity')) }}</span>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive text-nowrap">
          <table class="table table-hover">
            <thead>
              <tr>
                <th class="text-uppercase small fw-bold">Time Issued</th>
                <th class="text-uppercase small fw-bold">Time Ret.</th>
                <th class="text-uppercase small fw-bold">Customer / Player</th>
                <th class="text-uppercase small fw-bold">Collector / Respons.</th>
                <th class="text-uppercase small fw-bold text-center">Issued</th>
                <th class="text-uppercase small fw-bold text-center">Ret.</th>
                <th class="text-uppercase small fw-bold text-center">Rem.</th>
                <th class="text-uppercase small fw-bold text-end">Amount</th>
                <th class="text-uppercase small fw-bold text-center">Action</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @forelse($ballSessions ?? [] as $session)
              @if($session)
              <tr>
                <td class="small">{{ $session->time_issued ?? '-' }}</td>
                <td class="small text-success">{{ $session->time_returned ?? '-' }}</td>
                <td class="fw-bold text-dark">
                  <div>{{ $session->customer_name ?? 'Guest' }}</div>
                  @if(isset($session->member_id) && $session->member_id)
                    <small class="text-primary fw-normal">Member</small>
                  @endif
                </td>
                <td>
                  @if(isset($session->returned_by) && $session->returned_by)
                    <div class="fw-bold text-success"><i class="ri ri-user-received-2-line me-1"></i>{{ $session->returned_by }}</div>
                    <small class="text-muted">Returner</small>
                  @elseif(isset($session->designated_collector) && $session->designated_collector)
                    <div class="fw-bold text-warning"><i class="ri ri-user-follow-line me-1"></i>{{ $session->designated_collector }}</div>
                    <small class="text-muted">Designated</small>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
                <td class="text-center">
                  <span class="badge bg-label-warning px-2">{{ $session->issued ?? 0 }}</span>
                </td>
                <td class="text-center">
                  <span class="badge bg-label-success px-2">{{ $session->returned ?? 0 }}</span>
                </td>
                <td class="text-center">
                  @if(isset($session->remaining) && $session->remaining > 0)
                    <span class="badge bg-danger rounded-pill px-2">{{ $session->remaining }}</span>
                  @else
                    <span class="badge bg-label-secondary rounded-pill px-2">0</span>
                  @endif
                </td>
                <td class="text-end fw-bold text-primary">
                  {{ (isset($session->amount) && $session->amount) ? 'TZS ' . number_format($session->amount) : '-' }}
                </td>
                <td class="text-center">
                   <button class="btn btn-sm btn-icon btn-label-primary border-0" onclick="editTransaction({{ $session->last_txn_id ?? 0 }}, {{ $session->issued ?? 0 }}, '{{ $session->type ?? '' }}', {{ $session->amount ?? 0 }}, '{{ $session->payment_method ?? '' }}', {{ $session->member_id ?? 'null' }}, {{ $session->remaining ?? 0 }}, '{{ addslashes($session->customer_name ?? '') }}')" title="Manage">
                      <i class="ri ri-settings-4-line"></i>
                    </button>
                </td>
              </tr>
              @endif
              @empty
              <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                  <i class="ri ri-inbox-line ri-2x d-block mb-2 opacity-25"></i>
                  No sessions recorded today
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

<!-- Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-label-secondary">
        <h5 class="modal-title fw-bold">Inventory Maintenance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-pills mb-4 nav-fill" id="maintenanceTabs" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" id="add-stock-tab" data-bs-toggle="pill" data-bs-target="#addStock" type="button">Add Stock</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" id="mark-damaged-tab" data-bs-toggle="pill" data-bs-target="#markDamaged" type="button">Mark Damaged</button>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="addStock">
            <form id="addStockForm">
              @csrf
              <div class="form-floating form-floating-outline mb-4">
                <input type="number" class="form-control" name="quantity" min="1" required />
                <label>Quantity to Add</label>
              </div>
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" class="form-control" name="notes" placeholder="Supplier or batch info" />
                <label>Notes</label>
              </div>
              <button type="submit" class="btn btn-primary w-100 py-2">Add New Stock</button>
            </form>
          </div>
          <div class="tab-pane fade" id="markDamaged">
            <form id="damagedForm">
              @csrf
              <div class="form-floating form-floating-outline mb-4">
                <input type="number" class="form-control" name="quantity" min="1" required />
                <label>Damaged Quantity</label>
              </div>
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" class="form-control" name="notes" placeholder="Reason for damage" />
                <label>Reason</label>
              </div>
              <button type="submit" class="btn btn-danger w-100 py-2">Confirm Damaged</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Transaction Modal -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-label-primary text-white">
        <h5 class="modal-title fw-bold">Edit Transaction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editTransactionForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="edit_transaction_id" name="transaction_id">
          <input type="hidden" id="edit_transaction_type" name="transaction_type">
          <input type="hidden" id="edit_old_quantity" name="old_quantity">
          <input type="hidden" id="edit_old_amount" name="old_amount">
          <input type="hidden" id="edit_payment_method" name="payment_method">
          <input type="hidden" id="edit_member_id" name="member_id">
          
          <div class="alert alert-info border-0 mb-4">
            <div class="row text-center">
              <div class="col-4 border-end">
                <small class="text-muted d-block">Issued</small>
                <strong id="edit_current_quantity_display">0</strong>
              </div>
              <div class="col-4 border-end">
                <small class="text-muted d-block text-danger">Remaining</small>
                <strong id="edit_remaining_display" class="text-danger">0</strong>
              </div>
              <div class="col-4">
                <small class="text-muted d-block">Amount</small>
                <strong class="text-primary">TZS <span id="edit_current_amount_display">0</span></strong>
              </div>
            </div>
          </div>
          
          <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs nav-fill" role="tablist">
              <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-return" aria-controls="tab-return" aria-selected="true">
                  <i class="ri ri-reply-all-line me-1"></i> Quick Return
                </button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-adjust" aria-controls="tab-adjust" aria-selected="false">
                  <i class="ri ri-edit-line me-1"></i> Edit Issue
                </button>
              </li>
            </ul>
            <div class="tab-content border-0 p-0 pt-4">
              <div class="tab-pane fade show active" id="tab-return" role="tabpanel">
                <div class="form-floating form-floating-outline mb-4">
                  <input type="number" class="form-control form-control-lg border-success" id="edit_return_quantity" name="return_quantity" min="1" placeholder="Quantity" />
                  <label class="text-success">Balls to Return Now</label>
                  <small class="text-muted">Enter quantity being returned to close this session.</small>
                </div>
                <button type="button" class="btn btn-success w-100 py-3" onclick="submitQuickReturn()">
                  <i class="ri ri-check-line me-2"></i> CONFIRM RETURN
                </button>
              </div>
              
              <div class="tab-pane fade" id="tab-adjust" role="tabpanel">
                <div class="form-floating form-floating-outline mb-4">
                  <input type="number" class="form-control" id="edit_new_quantity" name="new_quantity" min="1" required />
                  <label>Change Total Issued Quantity</label>
                  <small class="text-muted">Use this to correct mistakes in the original issuance.</small>
                </div>
                <div class="alert alert-warning border-0 mb-4" id="editQuantityWarning" style="display: none;">
                  <i class="ri ri-alert-line me-1"></i> <small id="editQuantityWarningText"></small>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3">SAVE ADJUSTMENT</button>
              </div>
            </div>
          </div>
          <input type="hidden" id="edit_session_customer" name="customer_name">
          <input type="hidden" id="edit_session_remaining" name="max_return">
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Register Member Modal (Quick Add from Ball Management) -->
<div class="modal fade" id="registerMemberModal" tabindex="-1" aria-hidden="true" style="z-index: 1100;">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success py-3">
        <h5 class="modal-title text-white fw-bold"><i class="ri-user-add-line me-2"></i>Quick Register Member</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="quickRegisterForm">
          <input type="hidden" name="has_full_access" value="0"> {{-- Registering as a Custom Member --}}
          <div class="form-floating form-floating-outline mb-4">
            <input type="text" class="form-control fw-bold" id="reg_name" name="name" placeholder="Full Name" required>
            <label>Full Name *</label>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <input type="tel" class="form-control fw-bold" id="reg_phone" name="phone" placeholder="Phone Number" required>
            <label>Phone Number *</label>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <input type="email" class="form-control" id="reg_email" name="email" placeholder="Email (optional)">
            <label>Email (optional)</label>
          </div>
          <div class="alert alert-info py-2 small mb-0">
            <i class="ri ri-information-line me-1"></i> This will register a new <b>Custom Member</b> who can maintain a balance for golf services.
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 bg-light py-3">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success px-4 fw-bold" id="confirmRegBtn" onclick="submitQuickRegistration()">
          <i class="ri ri-user-add-line me-1"></i> REGISTER MEMBER
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const costPerBall = {{ $inventory->cost_per_ball ?? 500 }};
let selectedIssueMemberId = null;
let selectedIssueMemberBalance = 0;

function setIssueQuantity(qty) {
  document.getElementById('issue_quantity').value = qty;
  updateIssueAmount();
  
  // Highlight the button briefly
  const buttons = document.querySelectorAll('[onclick^="setIssueQuantity"]');
  buttons.forEach(btn => {
    if (btn.innerText.includes(qty)) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });
}

function quickReturn(name, qty, collectorId) {
  const returnForm = document.getElementById('returnBallsForm');
  returnForm.querySelector('[name="customer_name"]').value = name;
  returnForm.querySelector('[name="quantity"]').value = qty;
  
  const collectorSelect = returnForm.querySelector('[name="collector_id"]');
  if (collectorId && collectorSelect) {
    collectorSelect.value = collectorId;
  } else if (collectorSelect) {
    collectorSelect.value = "";
  }
  
  // Scroll to form and highlight
  returnForm.closest('.card').scrollIntoView({ behavior: 'smooth' });
  returnForm.classList.add('shake-animation');
  setTimeout(() => returnForm.classList.remove('shake-animation'), 500);
}

// Maintenance modal tab switcher
document.getElementById('maintenanceModal')?.addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  const tab = button.getAttribute('data-tab');
  if (tab === 'markDamaged') {
    const tabEl = document.querySelector('#mark-damaged-tab');
    bootstrap.Tab.getOrCreateInstance(tabEl).show();
  } else {
    const tabEl = document.querySelector('#add-stock-tab');
    bootstrap.Tab.getOrCreateInstance(tabEl).show();
  }
});

// Customer search for issue form
let issueSearchTimeout;
document.getElementById('issue_customer_name')?.addEventListener('input', function() {
  const query = this.value.trim();
  const suggestions = document.getElementById('issueCustomerSuggestions');
  
  if (query.length < 2) {
    suggestions.style.display = 'none';
    if (query.length === 0) resetIssueMemberSelection();
    return;
  }
  
  clearTimeout(issueSearchTimeout);
  issueSearchTimeout = setTimeout(() => {
    fetch('{{ url("payments/members/search") }}?q=' + encodeURIComponent(query), {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(members => {
      // Create Register Shortcut
      const regShortcut = `
        <div class="list-group-item list-group-item-action bg-success-subtle border-success border-start border-4 mb-1" style="cursor:pointer" onclick="openRegistrationModal('${query.replace(/'/g, "\\'")}')">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm bg-success me-3">
              <span class="avatar-initial rounded"><i class="ri ri-user-add-line"></i></span>
            </div>
            <div>
              <h6 class="mb-0 text-success fw-bold">Register "${query}"?</h6>
              <small class="text-muted small">Add as new custom member</small>
            </div>
          </div>
        </div>`;

      if (members && members.length > 0) {
        suggestions.innerHTML = regShortcut + members.slice(0, 5).map(m => {
          const safeName = (m.name || '').replace(/'/g, "\\'");
          const safeCardNumber = (m.card_number || '').replace(/'/g, "\\'");
          const safePhone = (m.phone || '').replace(/'/g, "\\'");
          const hasFullAccess = m.has_full_access ? 1 : 0;
          return `
          <div class="list-group-item list-group-item-action p-3" style="cursor: pointer;" onclick="selectIssueMember(${m.id}, '${safeName}', '${safeCardNumber}', '${safePhone}', ${m.balance || 0}, ${hasFullAccess});">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong class="text-primary">${m.name || 'N/A'}</strong>
                <div class="small text-muted">${m.card_number || 'No card'} | ${m.phone || 'No phone'}</div>
              </div>
              <div class="text-end">
                <span class="badge bg-label-success">${m.balance ? 'TZS ' + parseFloat(m.balance).toLocaleString() : 'No balance'}</span>
              </div>
            </div>
          </div>
        `;
        }).join('');
        
        suggestions.innerHTML += `
          <div class="list-group-item list-group-item-action bg-light" style="cursor: pointer;" onclick="selectWalkInCustomer('${query.replace(/'/g, "\\'")}');">
            <div class="d-flex align-items-center">
              <i class="ri ri-user-add-line me-2 text-muted"></i>
              <div><strong>Use as Walk-in: "${query}"</strong></div>
            </div>
          </div>
        `;
        suggestions.style.display = 'block';
      } else {
        suggestions.innerHTML = regShortcut + `
          <div class="list-group-item list-group-item-action bg-light" style="cursor: pointer;" onclick="selectWalkInCustomer('${query.replace(/'/g, "\\'")}');">
            <div class="d-flex align-items-center">
              <i class="ri ri-user-add-line me-2 text-muted"></i>
              <div><strong>Use as Walk-in: "${query}"</strong></div>
            </div>
          </div>
        `;
        suggestions.style.display = 'block';
      }
    })
    .catch(() => suggestions.style.display = 'none');
  }, 300);
});

function selectIssueMember(memberId, name, cardNumber, phone, balance, hasFullAccess = 1) {
  document.getElementById('issue_member_id').value = memberId;
  document.getElementById('issue_customer_name').value = name;
  document.getElementById('issueCustomerSuggestions').style.display = 'none';
  
  selectedIssueMemberId = memberId;
  selectedIssueMemberBalance = parseFloat(balance || 0);
  
  document.getElementById('issueMemberName').textContent = name || '-';
  document.getElementById('issueMemberCard').textContent = cardNumber || '-';
  document.getElementById('issueMemberBalance').textContent = 'TZS ' + selectedIssueMemberBalance.toLocaleString();
  
  // Optimization: Hide balance if it is 0
  if (selectedIssueMemberBalance > 0) {
    document.getElementById('issueBalanceDisplay').style.display = 'block';
  } else {
    document.getElementById('issueBalanceDisplay').style.display = 'none';
  }

  document.getElementById('issueMemberInfo').style.display = 'block';
  
  // User Rule: Cardholders (hasFullAccess=1) MUST pay by balance.
  // Custom members (hasFullAccess=0) can pay by CASH.
  const paymentMethodSelect = document.getElementById('issue_payment_method');
  const balanceOption = paymentMethodSelect.querySelector('option[value="balance"]');
  const cashOption = paymentMethodSelect.querySelector('option[value="cash"]');
  
  if (hasFullAccess) {
    paymentMethodSelect.value = 'balance';
    if (cashOption) cashOption.disabled = true;
    if (balanceOption) balanceOption.disabled = false;
    // Show a small hint if balance is low
    const amount = parseInt(document.getElementById('issue_quantity').value) * costPerBall;
    if (selectedIssueMemberBalance < amount) {
      document.getElementById('issueMemberBalance').classList.add('text-danger');
      document.getElementById('issueMemberBalance').textContent += ' (Insufficient)';
    }
  } else {
    paymentMethodSelect.value = 'cash';
    if (cashOption) cashOption.disabled = false;
    // Walk-ins/Custom members shouldn't use card balance usually
    if (balanceOption) balanceOption.disabled = true;
  }
  
  updateIssueAmount();
}

function selectWalkInCustomer(name) {
  document.getElementById('issue_member_id').value = '';
  document.getElementById('issue_customer_name').value = name;
  document.getElementById('issueCustomerSuggestions').style.display = 'none';
  resetIssueMemberSelection();
  document.getElementById('issue_payment_method').value = 'cash';
  updateIssueAmount();
}

function resetIssueMemberSelection() {
  selectedIssueMemberId = null;
  selectedIssueMemberBalance = 0;
  document.getElementById('issueMemberInfo').style.display = 'none';
  document.getElementById('issue_member_id').value = '';
}

// Customer search for return form
let returnSearchTimeout;
document.getElementById('return_customer_name')?.addEventListener('input', function() {
  const query = this.value.trim();
  const suggestions = document.getElementById('returnCustomerSuggestions');
  
  if (query.length < 2) {
    suggestions.style.display = 'none';
    return;
  }
  
  clearTimeout(returnSearchTimeout);
  returnSearchTimeout = setTimeout(() => {
    fetch('{{ url("payments/members/search") }}?q=' + encodeURIComponent(query), {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(members => {
      if (members && members.length > 0) {
        suggestions.innerHTML = members.slice(0, 5).map(m => {
          const safeName = (m.name || '').replace(/'/g, "\\'");
          return `
          <div class="list-group-item list-group-item-action p-3" style="cursor: pointer;" onclick="selectReturnMember('${safeName}');">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong class="text-primary">${m.name || 'N/A'}</strong>
                <div class="small text-muted">${m.card_number || 'No card'}</div>
              </div>
            </div>
          </div>
        `;
        }).join('');
        suggestions.style.display = 'block';
      } else {
        suggestions.style.display = 'none';
      }
    })
    .catch(() => suggestions.style.display = 'none');
  }, 300);
});

function selectReturnMember(name) {
  document.getElementById('return_customer_name').value = name;
  document.getElementById('returnCustomerSuggestions').style.display = 'none';
}

function updateIssueAmount() {
  const quantityInput = document.getElementById('issue_quantity');
  const quantity = parseInt(quantityInput.value) || 0;
  const amount = quantity * costPerBall;
  
  const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'TZS',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });
  
  const formattedAmount = formatter.format(amount).replace('TZS', '').trim();
  document.getElementById('issueAmount').textContent = 'TZS ' + formattedAmount;
}

document.addEventListener('DOMContentLoaded', function() {
  updateIssueAmount();
  
  // Initialize tooltips
  const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltips.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});

// Form Submissions
const forms = {
  return: document.getElementById('returnBallsForm'),
  addStock: document.getElementById('addStockForm'),
  damaged: document.getElementById('damagedForm'),
  issue: document.getElementById('issueBallsForm')
};

Object.entries(forms).forEach(([key, form]) => {
  if (!form) return;
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';
    
    let url = '{{ route("golf-services.ball-management.issue") }}';
    if (key === 'return') url = '{{ route("golf-services.ball-management.return") }}';
    if (key === 'addStock') url = '{{ route("golf-services.ball-management.add-stock") }}';
    if (key === 'damaged') url = '{{ route("golf-services.ball-management.damaged") }}';
    
    fetch(url, {
      method: 'POST',
      body: new FormData(this),
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.innerHTML = originalText;
      if (data.success) {
        showSuccess(data.message).then(() => location.reload());
      } else {
        showError(data.message);
      }
    })
    .catch(error => {
      btn.disabled = false;
      btn.innerHTML = originalText;
      showError('Error processing request');
    });
  });
});

// Close suggestions when clicking outside
document.addEventListener('click', function(e) {
  if (!e.target.closest('#issue_customer_name') && !e.target.closest('#issueCustomerSuggestions')) {
    document.getElementById('issueCustomerSuggestions').style.display = 'none';
  }
  if (!e.target.closest('#return_customer_name') && !e.target.closest('#returnCustomerSuggestions')) {
    document.getElementById('returnCustomerSuggestions').style.display = 'none';
  }
});

function editTransaction(transactionId, currentQuantity, transactionType, currentAmount, paymentMethod, memberId, remaining, customerName) {
  document.getElementById('edit_transaction_id').value = transactionId;
  document.getElementById('edit_transaction_type').value = transactionType;
  document.getElementById('edit_old_quantity').value = currentQuantity;
  document.getElementById('edit_new_quantity').value = currentQuantity;
  document.getElementById('edit_old_amount').value = currentAmount;
  document.getElementById('edit_payment_method').value = paymentMethod;
  document.getElementById('edit_member_id').value = memberId || '';
  
  document.getElementById('edit_session_customer').value = customerName;
  document.getElementById('edit_session_remaining').value = remaining;
  
  document.getElementById('edit_current_quantity_display').textContent = currentQuantity;
  document.getElementById('edit_remaining_display').textContent = remaining;
  document.getElementById('edit_current_amount_display').textContent = parseFloat(currentAmount).toLocaleString();
  
  // Pre-fill return field with remaining balance
  document.getElementById('edit_return_quantity').value = remaining;
  
  // Show return tab by default if there are remaining balls
  if (remaining > 0) {
    const tabEl = document.querySelector('[data-bs-target="#tab-return"]');
    bootstrap.Tab.getOrCreateInstance(tabEl).show();
  } else {
    const tabEl = document.querySelector('[data-bs-target="#tab-adjust"]');
    bootstrap.Tab.getOrCreateInstance(tabEl).show();
  }
  
  new bootstrap.Modal(document.getElementById('editTransactionModal')).show();
}

function submitQuickReturn() {
  const qtyInput = document.getElementById('edit_return_quantity');
  const qty = parseInt(qtyInput.value) || 0;
  const max = parseInt(document.getElementById('edit_session_remaining').value) || 0;
  const name = document.getElementById('edit_session_customer').value;
  
  if (qty <= 0) {
    showError('Please enter a valid quantity');
    return;
  }
  
  if (qty > max) {
    showError('Cannot return more than ' + max + ' balls for this session');
    return;
  }
  
  const formData = new FormData();
  formData.append('customer_name', name);
  formData.append('quantity', qty);
  formData.append('_token', '{{ csrf_token() }}');
  
  const btn = document.querySelector('#tab-return button');
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';
  
  fetch('{{ route("golf-services.ball-management.return") }}', {
    method: 'POST',
    body: formData,
    headers: { 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showSuccess(data.message).then(() => location.reload());
    } else {
      showError(data.message);
      btn.disabled = false;
      btn.innerHTML = originalText;
    }
  });
}

function openRegistrationModal(name) {
  document.getElementById('reg_name').value = name;
  document.getElementById('reg_phone').value = '';
  document.getElementById('reg_email').value = '';
  document.getElementById('issueCustomerSuggestions').style.display = 'none';
  new bootstrap.Modal(document.getElementById('registerMemberModal')).show();
}

function submitQuickRegistration() {
  const form = document.getElementById('quickRegisterForm');
  const formData = new FormData(form);
  const btn = document.getElementById('confirmRegBtn');
  const origText = btn.innerHTML;

  if (!form.checkValidity()) { form.reportValidity(); return; }

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registering...';

  fetch('{{ route("payments.members.store") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success && data.member) {
      showSuccess('Member registered successfully!');
      bootstrap.Modal.getInstance(document.getElementById('registerMemberModal')).hide();
      // Auto-select the new member (Custom Member = no full access)
      selectIssueMember(data.member.id, data.member.name, data.member.card_number, data.member.balance, 0);
    } else {
      showError(data.message || 'Registration failed');
    }
  })
  .catch(err => {
    showError('Network error during registration');
  })
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = origText;
  });
}

function setDateRange(range) {
  const today = new Date();
  const from  = document.getElementById('from_date');
  const to    = document.getElementById('to_date');
  let f, t;

  const formatDate = (d) => {
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  switch(range) {
    case 'today':       f = new Date(today); t = new Date(today); break;
    case 'yesterday':   f = new Date(today); f.setDate(f.getDate()-1); t = new Date(f); break;
    case 'this_week':   f = new Date(today); f.setDate(f.getDate()-f.getDay()); t = new Date(today); break;
  }
  
  if(f && t) {
    from.value = formatDate(f);
    to.value   = formatDate(t);
    document.getElementById('filterForm').submit();
  }
}

function showError(msg) {
  if (typeof Swal !== 'undefined') {
    return Swal.fire('Error!', msg, 'error');
  }
  alert(msg);
}

function showSuccess(msg) {
  if (typeof Swal !== 'undefined') {
    return Swal.fire('Success!', msg, 'success');
  }
  return new Promise(resolve => {
    alert(msg);
    resolve();
  });
}
</script>

<style>
.shake-animation {
  animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}
@keyframes shake {
  10%, 90% { transform: translate3d(-1px, 0, 0); }
  20%, 80% { transform: translate3d(2px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
  40%, 60% { transform: translate3d(4px, 0, 0); }
}
.active[onclick^="setIssueQuantity"] {
  background-color: var(--bs-primary) !important;
  color: white !important;
}
.progress-white { background-color: rgba(255,255,255,0.2) !important; }
.bg-label-info { background-color: #e1f5fe !important; color: #0288d1 !important; }

@media (max-width: 767.98px) {
    .border-md-start {
        border-left: none !important;
        border-top: 1px solid rgba(0,0,0,0.05);
        padding-top: 2rem;
        margin-top: 1rem;
    }
}
.alert-white { background-color: white !important; border: 0; }
</style>
@endpush
