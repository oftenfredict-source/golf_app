@extends('settings._layout-base')

@section('title', 'Counter Management')
@section('description', 'Counter Management - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Club Services /</span> Counter Management
</h4>

<!-- Statistics Summary -->
<div class="row mb-6">
  <div class="col-md-3 col-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-primary rounded">
              <i class="icon-base ri ri-store-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Active Counters</p>
            <h5 class="mb-0">{{ $activeCounters->count() ?? 0 }}</h5>
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
              <i class="icon-base ri ri-user-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Assigned Staff</p>
            <h5 class="mb-0">{{ $counters->whereNotNull('assigned_user_id')->count() ?? 0 }}</h5>
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
              <i class="icon-base ri ri-shopping-cart-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Total Counters</p>
            <h5 class="mb-0">{{ $counters->count() ?? 0 }}</h5>
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
              <i class="icon-base ri ri-building-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Locations</p>
            <h5 class="mb-0">{{ $counters->pluck('location')->unique()->count() ?? 0 }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-6">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Service Counters</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCounterModal">
          <i class="ri ri-add-line me-1"></i> Add Counter
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Station Name</th>
                <th>Location</th>
                <th>Duty Category</th>
                <th>Tier</th>
                <th>Staff On Duty</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($counters ?? [] as $counter)
              <tr>
                <td><strong>{{ $counter->name }}</strong></td>
                <td>{{ $counter->location ?? '-' }}</td>
                <td>
                  <span class="badge bg-label-info">{{ ucfirst($counter->type) }}</span>
                  @if($counter->is_alcohol)
                    <span class="badge bg-label-danger ms-1" title="Alcoholic Drinks Duty"><i class="ri ri-goblet-line"></i> Alcohol Duty</span>
                  @else
                    <span class="badge bg-label-success ms-1" title="Non-Alcoholic Drinks Duty"><i class="ri ri-cup-line"></i> Soft Drinks Only</span>
                  @endif
                </td>
                <td>
                  @if($counter->tier === 'vip')
                    <span class="badge bg-label-warning text-warning border border-warning">
                      <i class="ri ri-vip-crown-line me-1"></i>VIP
                    </span>
                  @else
                    <span class="badge bg-label-secondary">Normal</span>
                  @endif
                </td>
                <td>{{ $counter->assignedUser->name ?? 'Unassigned' }}</td>
                <td>
                  @if($counter->is_active)
                    <span class="badge bg-label-success">Active</span>
                  @else
                    <span class="badge bg-label-danger">Inactive</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-label-primary" onclick="editCounter({{ $counter->id }}, '{{ $counter->name }}', '{{ $counter->location ?? '' }}', '{{ $counter->type }}', '{{ $counter->tier ?? 'normal' }}', {{ $counter->is_active ? 'true' : 'false' }}, {{ $counter->is_alcohol ? 'true' : 'false' }}, {{ $counter->assigned_user_id ?? 'null' }})">
                      <i class="icon-base ri ri-pencil-line me-1"></i>Edit
                    </button>
                    <button class="btn btn-sm btn-label-info" onclick="assignUser({{ $counter->id }}, '{{ $counter->name }}', {{ $counter->assigned_user_id ?? 'null' }})">
                      <i class="icon-base ri ri-user-settings-line me-1"></i>Assign Duty
                    </button>
                    <button class="btn btn-sm btn-label-danger" onclick="deleteCounter({{ $counter->id }}, '{{ $counter->name }}')">
                      <i class="icon-base ri ri-delete-bin-line me-1"></i>Delete
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-body-secondary">No counters configured</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Counter Modal -->
<div class="modal fade" id="addCounterModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #0d6efd 0%, #004085 100%); color: white;">
        <h5 class="modal-title text-white" id="counterModalTitle"><i class="ri ri-store-3-line me-2"></i>Register Duty Station</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="addCounterForm">
        @csrf
        <input type="hidden" id="counter_id" name="counter_id" />
        <div class="modal-body">
          <div class="row g-4">
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="counter_name" name="name" placeholder="e.g. Main Bar" required />
                <label>Station / Point Name *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="counter_type" name="type" required>
                  <option value="beverage">Beverage</option>
                  <option value="food">Food / Kitchen</option>
                  <option value="equipment">Equipment</option>
                  <option value="general">General Service</option>
                </select>
                <label>Service Type *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="counter_tier" name="tier">
                  <option value="normal">Standard (Normal)</option>
                  <option value="vip">Premium (VIP)</option>
                </select>
                <label>Service Tier</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="counter_assigned_user_id" name="assigned_user_id">
                  <option value="">-- No Staff Assigned Yet --</option>
                  @foreach(\App\Models\User::whereIn('role', ['counter', 'admin', 'manager'])->orderBy('role')->orderBy('name')->get() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                  @endforeach
                </select>
                <label>Current Staff On Duty</label>
                <div class="form-text text-primary small mt-1"><i class="ri ri-information-line me-1"></i>Only registered staff appear here.</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="counter_location" name="location" placeholder="e.g. Ground Floor, North" />
                <label>Physical Location</label>
              </div>
            </div>
            <div class="col-12 mt-2">
              <div class="d-flex align-items-center gap-4 p-3 rounded bg-light border">
                <div class="form-check form-switch mb-0">
                  <input type="checkbox" class="form-check-input" id="counter_is_active" name="is_active" checked />
                  <label class="form-check-label fw-bold" for="counter_is_active">Station Active</label>
                </div>
                <div class="form-check form-switch mb-0">
                  <input type="checkbox" class="form-check-input" id="counter_is_alcohol" name="is_alcohol" />
                  <label class="form-check-label fw-bold" for="counter_is_alcohol">Permit Alcoholic Drinks</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4" id="counterSubmitBtn">
            <i class="ri ri-save-line me-1"></i> Register Station
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Assign User Modal -->
<div class="modal fade" id="assignUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-label-info">
        <h5 class="modal-title">Staff Duty Assignment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="assignUserForm">
        @csrf
        <input type="hidden" id="assign_counter_id" name="counter_id" />
        <div class="modal-body">
          <p class="mb-3">Assigning staff to duty at station: <strong id="assign_counter_name"></strong></p>
          <div class="form-floating form-floating-outline mb-4">
            <select class="form-select" id="assign_user_id" name="user_id">
              <option value="">-- Remove Assignment --</option>
              @foreach(\App\Models\User::where('role', 'counter')->orWhere('role', 'admin')->get() as $user)
              <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
              @endforeach
            </select>
            <label>Select Staff Member</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Duty Assignment</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Add/Edit Counter
document.getElementById('addCounterForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const counterId = document.getElementById('counter_id').value;
  const url = counterId 
    ? '{{ route("services.counters.update", ":id") }}'.replace(':id', counterId)
    : '{{ route("services.counters.store") }}';
  const method = counterId ? 'PUT' : 'POST';
  
  // Add checkbox and select values
  formData.set('is_active', document.getElementById('counter_is_active').checked ? '1' : '0');
  formData.set('is_alcohol', document.getElementById('counter_is_alcohol').checked ? '1' : '0');
  formData.set('assigned_user_id', document.getElementById('counter_assigned_user_id').value);
  
  fetch(url, {
    method: method,
    body: formData,
    headers: { 
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    }
  })
  .then(r => {
    if (!r.ok) throw new Error('Network response was not ok');
    return r.json();
  })
  .then(data => {
    if (data.success) {
      showSuccess(data.message || 'Counter saved successfully').then(() => location.reload());
    } else {
      showError(data.message || 'Failed to save counter');
    }
  })
  .catch(err => {
    console.error(err);
    showError('Error saving counter: ' + err.message);
  });
});

// Edit Counter
function editCounter(id, name, location, type, tier, isActive, isAlcohol, assignedUserId) {
  document.getElementById('counter_id').value = id;
  document.getElementById('counter_name').value = name;
  document.getElementById('counter_location').value = location || '';
  document.getElementById('counter_type').value = type;
  document.getElementById('counter_tier').value = tier || 'normal';
  document.getElementById('counter_is_active').checked = isActive;
  document.getElementById('counter_is_alcohol').checked = isAlcohol;
  document.getElementById('counter_assigned_user_id').value = assignedUserId || '';
  document.getElementById('counterModalTitle').innerHTML = '<i class="ri ri-edit-line me-2"></i>Edit Duty Station';
  document.getElementById('counterSubmitBtn').innerHTML = '<i class="ri ri-save-line me-1"></i> Update Station';
  new bootstrap.Modal(document.getElementById('addCounterModal')).show();
}

// Assign User
function assignUser(counterId, counterName, currentUserId) {
  document.getElementById('assign_counter_id').value = counterId;
  document.getElementById('assign_counter_name').textContent = counterName;
  document.getElementById('assign_user_id').value = currentUserId || '';
  new bootstrap.Modal(document.getElementById('assignUserModal')).show();
}

// Assign User Form
document.getElementById('assignUserForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const counterId = document.getElementById('assign_counter_id').value;
  const userId = document.getElementById('assign_user_id').value;
  
  fetch('{{ url("services/counters") }}/' + counterId + '/assign', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ user_id: userId || null })
  })
  .then(async r => {
    if (!r.ok) {
      let errorMessage = 'Network response was not ok';
      try {
        const errorData = await r.json();
        if (errorData.message) {
          errorMessage = errorData.message;
        } else if (errorData.errors) {
          const errorMessages = Object.values(errorData.errors).flat();
          errorMessage = errorMessages.join(', ');
        }
      } catch (e) {
        // Use default message
      }
      throw new Error(errorMessage);
    }
    return r.json();
  })
  .then(data => {
    if (data.success) {
      showSuccess(data.message || 'User assigned successfully').then(() => location.reload());
    } else {
      showError(data.message || 'Failed to assign user');
    }
  })
  .catch(err => {
    console.error(err);
    showError('Error assigning user: ' + err.message);
  });
});

// Delete Counter
function deleteCounter(id, name) {
  showConfirm('Are you sure you want to delete counter "' + name + '"? This action cannot be undone.').then((result) => {
    if (!result.isConfirmed) {
      return;
    }
  
  fetch('{{ route("services.counters.update", ":id") }}'.replace(':id', id), {
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ _method: 'DELETE' })
  })
  .then(r => {
    if (!r.ok) throw new Error('Network response was not ok');
    return r.json();
  })
    .then(data => {
      if (data.success) {
        showSuccess(data.message || 'Counter deleted successfully').then(() => location.reload());
      } else {
        showError(data.message || 'Failed to delete counter');
      }
    })
    .catch(err => {
      console.error(err);
      showError('Error deleting counter: ' + err.message);
    });
  });
}

// Reset form when modal is closed
document.getElementById('addCounterModal')?.addEventListener('hidden.bs.modal', function() {
  document.getElementById('addCounterForm').reset();
  document.getElementById('counter_id').value = '';
  document.getElementById('counterModalTitle').innerHTML = '<i class="ri ri-store-3-line me-2"></i>Register Duty Station';
  document.getElementById('counterSubmitBtn').innerHTML = '<i class="ri ri-save-line me-1"></i> Register Station';
  document.getElementById('counter_is_active').checked = true;
  document.getElementById('counter_is_alcohol').checked = false;
  document.getElementById('counter_assigned_user_id').value = '';
});
</script>
@endpush


