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
                <th>Counter Name</th>
                <th>Location</th>
                <th>Type</th>
                <th>Tier</th>
                <th>Assigned To</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($counters ?? [] as $counter)
              <tr>
                <td><strong>{{ $counter->name }}</strong></td>
                <td>{{ $counter->location ?? '-' }}</td>
                <td><span class="badge bg-label-info">{{ ucfirst($counter->type) }}</span></td>
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
                    <button class="btn btn-sm btn-label-primary" onclick="editCounter({{ $counter->id }}, '{{ $counter->name }}', '{{ $counter->location ?? '' }}', '{{ $counter->type }}', '{{ $counter->tier ?? 'normal' }}', {{ $counter->is_active ? 'true' : 'false' }})">
                      <i class="icon-base ri ri-pencil-line me-1"></i>Edit
                    </button>
                    <button class="btn btn-sm btn-label-info" onclick="assignUser({{ $counter->id }}, '{{ $counter->name }}', {{ $counter->assigned_user_id ?? 'null' }})">
                      <i class="icon-base ri ri-user-add-line me-1"></i>Assign
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
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white" id="counterModalTitle">Add Counter</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="addCounterForm">
        @csrf
        <input type="hidden" id="counter_id" name="counter_id" />
        <div class="modal-body">
          <div class="form-floating form-floating-outline mb-4">
            <input type="text" class="form-control" id="counter_name" name="name" required />
            <label>Counter Name *</label>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <input type="text" class="form-control" id="counter_location" name="location" />
            <label>Location</label>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <select class="form-select" id="counter_type" name="type" required>
              <option value="food">Food</option>
              <option value="beverage">Beverage</option>
              <option value="equipment">Equipment</option>
              <option value="general">General</option>
            </select>
            <label>Type *</label>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <select class="form-select" id="counter_tier" name="tier">
              <option value="normal">Normal (Standard Tables)</option>
              <option value="vip">VIP (Premium Tables)</option>
            </select>
            <label>Tier</label>
          </div>
          <div class="form-check form-switch mb-4">
            <input type="checkbox" class="form-check-input" id="counter_is_active" name="is_active" checked />
            <label class="form-check-label" for="counter_is_active">Active</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="counterSubmitBtn">Add Counter</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Assign User Modal -->
<div class="modal fade" id="assignUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign User to Counter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="assignUserForm">
        @csrf
        <input type="hidden" id="assign_counter_id" name="counter_id" />
        <div class="modal-body">
          <p class="mb-3">Assigning user to: <strong id="assign_counter_name"></strong></p>
          <div class="form-floating form-floating-outline mb-4">
            <select class="form-select" id="assign_user_id" name="user_id">
              <option value="">-- Select User --</option>
              @foreach(\App\Models\User::all() as $user)
              <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
              @endforeach
            </select>
            <label>Select User</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Assign User</button>
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
  
  // Add is_active checkbox value
  formData.set('is_active', document.getElementById('counter_is_active').checked ? '1' : '0');
  
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
function editCounter(id, name, location, type, tier, isActive) {
  document.getElementById('counter_id').value = id;
  document.getElementById('counter_name').value = name;
  document.getElementById('counter_location').value = location || '';
  document.getElementById('counter_type').value = type;
  document.getElementById('counter_tier').value = tier || 'normal';
  document.getElementById('counter_is_active').checked = isActive;
  document.getElementById('counterModalTitle').textContent = 'Edit Counter';
  document.getElementById('counterSubmitBtn').textContent = 'Update Counter';
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
  document.getElementById('counterModalTitle').textContent = 'Add Counter';
  document.getElementById('counterSubmitBtn').textContent = 'Add Counter';
  document.getElementById('counter_is_active').checked = true;
});
</script>
@endpush


