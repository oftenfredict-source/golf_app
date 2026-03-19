@extends('settings._layout-base')

@section('title', 'Member Details')
@section('description', 'Member Details - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Payments /</span> Member Details
</h4>

<!-- Header Card -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%);">
      <div class="card-body text-white p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="mb-2 text-white fw-bold">
              <i class="icon-base ri ri-user-line me-2"></i>{{ $member->name }}
            </h4>
            <p class="mb-0 opacity-75">Member ID: {{ $member->member_id }} | Card: {{ $member->card_number }}</p>
          </div>
          <div class="d-flex gap-2 mt-3 mt-md-0 flex-wrap">
            <a href="{{ route('payments.generate-card', $member->ulid ?? $member->id) }}" class="btn btn-light">
              <i class="icon-base ri ri-id-card-line me-1"></i>View Card
            </a>
            <a href="{{ route('payments.members.transactions', $member->id) }}" class="btn btn-light">
              <i class="icon-base ri ri-history-line me-1"></i>Transactions
            </a>
            <a href="{{ route('payments.upi-management') }}" class="btn btn-outline-light">
              <i class="icon-base ri ri-arrow-left-line me-1"></i>Back to Members
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Member Information -->
<div class="row mb-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-information-line me-2"></i>Member Information</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-muted">Full Name</label>
            <div class="fw-semibold">{{ $member->name }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Member ID</label>
            <div class="fw-semibold"><code>{{ $member->member_id }}</code></div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Card Number</label>
            <div class="fw-semibold"><code class="text-primary">{{ $member->card_number }}</code></div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">UPI ID</label>
            <div class="fw-semibold">{{ $member->upi_id ?? 'N/A' }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Phone</label>
            <div class="fw-semibold">{{ $member->phone ?? 'N/A' }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Email</label>
            <div class="fw-semibold">{{ $member->email ?? 'N/A' }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Membership Type</label>
            <div>
              <span class="badge bg-{{ $member->membership_type === 'premium' ? 'warning' : ($member->membership_type === 'vip' ? 'dark' : 'secondary') }}">
                {{ ucfirst($member->membership_type ?? 'standard') }}
              </span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Status</label>
            <div>
              <span class="badge bg-{{ $member->status === 'active' ? 'success' : 'warning' }}">
                {{ ucfirst($member->status ?? 'inactive') }}
              </span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Valid Until</label>
            <div class="fw-semibold">{{ $member->valid_until ? \Carbon\Carbon::parse($member->valid_until)->format('d M Y') : 'N/A' }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-muted">Registered</label>
            <div class="fw-semibold">{{ $member->created_at->format('d M Y') }}</div>
          </div>
          @if($member->notes)
          <div class="col-12">
            <label class="form-label text-muted">Notes</label>
            <div class="fw-semibold">{{ $member->notes }}</div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  
  <!-- Balance & Quick Actions -->
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-wallet-line me-2"></i>Account Balance</h5>
      </div>
      <div class="card-body text-center">
        <h2 class="mb-0 text-{{ $member->balance > 0 ? 'success' : 'danger' }}">
          TZS {{ number_format($member->balance, 0) }}
        </h2>
        <small class="text-muted">Current Balance</small>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-settings-3-line me-2"></i>Quick Actions</h5>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
          <a href="{{ route('payments.top-ups') }}?member_id={{ $member->id }}" class="btn btn-success">
            <i class="icon-base ri ri-add-line me-1"></i>Top Up Balance
          </a>
          @if(auth()->user()->role === 'admin')
          <button class="btn btn-primary" onclick="adjustBalance({{ $member->id }}, '{{ $member->name }}', {{ $member->balance }})">
            <i class="icon-base ri ri-edit-line me-1"></i>Adjust Balance
          </button>
          @endif
          <a href="{{ route('payments.members.transactions.pdf', $member->id) }}" class="btn btn-outline-primary" target="_blank">
            <i class="icon-base ri ri-file-download-line me-1"></i>Download Transactions PDF
          </a>
          <button class="btn btn-outline-secondary" onclick="editMember({{ $member->id }})">
            <i class="icon-base ri ri-edit-2-line me-1"></i>Edit Member
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Transactions -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="icon-base ri ri-history-line me-2"></i>Recent Transactions</h5>
        <a href="{{ route('payments.members.transactions', $member->id) }}" class="btn btn-sm btn-outline-primary">
          View All
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Transaction ID</th>
                <th>Type</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Balance After</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody id="transactionsTableBody">
              <tr>
                <td colspan="6" class="text-center py-4">
                  <div class="spinner-border text-primary" role="status"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Adjust Balance Modal -->
<div class="modal fade" id="adjustBalanceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-edit-line me-2"></i>Adjust Balance</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="adjustBalanceForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="adjust_member_id" name="member_id">
          <div class="mb-3">
            <label class="form-label">Member</label>
            <div class="fw-semibold" id="adjust_member_name">-</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Current Balance</label>
            <div class="fw-semibold text-success" id="adjust_current_balance">-</div>
          </div>
          <div class="mb-3">
            <label class="form-label">New Balance <span class="text-danger">*</span></label>
            <div class="form-floating form-floating-outline">
              <input type="number" step="0.01" class="form-control" id="adjust_new_balance" name="new_balance" required>
              <label for="adjust_new_balance">Enter new balance</label>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Reason <span class="text-danger">*</span></label>
            <div class="form-floating form-floating-outline">
              <textarea class="form-control" id="adjust_reason" name="reason" rows="3" required></textarea>
              <label for="adjust_reason">Reason for adjustment</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-save-line me-1"></i>Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-edit-2-line me-2"></i>Edit Member</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editMemberForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="edit_member_id" name="member_id">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="edit_name" name="name" required>
                <label for="edit_name">Full Name <span class="text-danger">*</span></label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="email" class="form-control" id="edit_email" name="email">
                <label for="edit_email">Email</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="edit_phone" name="phone">
                <label for="edit_phone">Phone</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="edit_membership_type" name="membership_type" required>
                  <option value="standard">Standard</option>
                  <option value="silver">Silver</option>
                  <option value="vip">VIP</option>
                  <option value="premium">Premium</option>
                </select>
                <label for="edit_membership_type">Membership Type <span class="text-danger">*</span></label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="date" class="form-control" id="edit_valid_until" name="valid_until">
                <label for="edit_valid_until">Valid Until</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="edit_status" name="status" required>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="suspended">Suspended</option>
                </select>
                <label for="edit_status">Status <span class="text-danger">*</span></label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                <label for="edit_notes">Notes</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-save-line me-1"></i>Update Member
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Load transactions on page load
document.addEventListener('DOMContentLoaded', function() {
  loadTransactions();
});

function loadTransactions() {
  fetch('{{ route("payments.members.transactions", $member->id) }}', {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(r => r.json())
  .then(data => {
    const tbody = document.getElementById('transactionsTableBody');
    if (!tbody) return;
    
    if (data.success && data.transactions && data.transactions.length > 0) {
      tbody.innerHTML = data.transactions.slice(0, 10).map(txn => {
        const category = (txn.category || '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        const amount = parseFloat(txn.amount || 0);
        const balanceAfter = parseFloat(txn.balance_after || 0);
        const typeClass = txn.type === 'payment' ? 'text-danger' : 'text-success';
        const typeSign = txn.type === 'payment' ? '-' : '+';
        const badgeClass = txn.type === 'payment' ? 'bg-label-danger' : (txn.type === 'topup' ? 'bg-label-success' : 'bg-label-warning');
        const badgeText = txn.type === 'payment' ? 'Payment' : (txn.type === 'topup' ? 'Top-up' : 'Refund');
        const date = new Date(txn.created_at);
        
        return `
          <tr>
            <td><code>${txn.transaction_id || '-'}</code></td>
            <td><span class="badge ${badgeClass}">${badgeText}</span></td>
            <td>${category}</td>
            <td><strong class="${typeClass}">${typeSign}TZS ${amount.toLocaleString()}</strong></td>
            <td>TZS ${balanceAfter.toLocaleString()}</td>
            <td>${date.toLocaleString()}</td>
          </tr>
        `;
      }).join('');
    } else {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No transactions found</td></tr>';
    }
  })
  .catch(err => {
    console.error('Error loading transactions:', err);
    document.getElementById('transactionsTableBody').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Error loading transactions</td></tr>';
  });
}

function adjustBalance(memberId, memberName, currentBalance) {
  document.getElementById('adjust_member_id').value = memberId;
  document.getElementById('adjust_member_name').textContent = memberName;
  document.getElementById('adjust_current_balance').textContent = 'TZS ' + parseFloat(currentBalance).toLocaleString();
  document.getElementById('adjust_new_balance').value = currentBalance;
  document.getElementById('adjust_reason').value = '';
  new bootstrap.Modal(document.getElementById('adjustBalanceModal')).show();
}

function editMember(memberId) {
  fetch('{{ route("payments.members.show", ":id") }}'.replace(':id', memberId), {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success && data.member) {
      const m = data.member;
      document.getElementById('edit_member_id').value = m.id;
      document.getElementById('edit_name').value = m.name || '';
      document.getElementById('edit_email').value = m.email || '';
      document.getElementById('edit_phone').value = m.phone || '';
      document.getElementById('edit_membership_type').value = m.membership_type || 'standard';
      document.getElementById('edit_status').value = m.status || 'active';
      document.getElementById('edit_notes').value = m.notes || '';
      if (m.valid_until) {
        document.getElementById('edit_valid_until').value = m.valid_until.split(' ')[0];
      }
      new bootstrap.Modal(document.getElementById('editMemberModal')).show();
    }
  })
  .catch(err => {
    console.error('Error loading member:', err);
    showError('Error loading member data');
  });
}

// Adjust Balance Form
document.getElementById('adjustBalanceForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const memberId = document.getElementById('adjust_member_id').value;
  const formData = new FormData(this);
  
  fetch('{{ route("payments.members.adjust-balance", ":id") }}'.replace(':id', memberId), {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showSuccess('Balance adjusted successfully').then(() => location.reload());
    } else {
      showError(data.message || 'Error adjusting balance');
    }
  })
  .catch(err => {
    console.error('Error:', err);
    showError('Error adjusting balance');
  });
});

// Edit Member Form
document.getElementById('editMemberForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const memberId = document.getElementById('edit_member_id').value;
  const formData = new FormData(this);
  
  fetch('{{ route("payments.members.update", ":id") }}'.replace(':id', memberId), {
    method: 'PUT',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showSuccess('Member updated successfully').then(() => location.reload());
    } else {
      showError(data.message || 'Error updating member');
    }
  })
  .catch(err => {
    console.error('Error:', err);
    showError('Error updating member');
  });
});
</script>
@endpush


