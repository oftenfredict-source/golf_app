@extends('settings._layout-base')

@section('title', 'Table Management')
@section('description', 'Table Management - Golf Club Management System')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark text-white border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h3 class="text-white fw-bold mb-1">
                                <i class="ri-table-alt-line me-2"></i>Table Management
                            </h3>
                            <p class="text-white-50 mb-0">Configure your restaurant floor plan</p>
                        </div>
                        <button class="btn btn-primary rounded-pill px-4 w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#addTableModal">
                            <i class="ri-add-line me-1"></i> Add New Table
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 g-md-4">
        @forelse($tables as $table)
        <div class="col-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm h-100 table-card {{ $table->status === 'occupied' ? 'bg-label-warning' : ($table->status === 'reserved' ? 'bg-label-info' : 'bg-label-success') }}" style="border-radius: 15px; transition: transform 0.2s;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar avatar-md">
                            <span class="avatar-initial rounded-circle bg-white text-dark fw-bold" style="font-size: 1.2rem;">
                                {{ $table->table_number }}
                            </span>
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-more-2-fill text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="editTable({{ $table->id }}, '{{ $table->table_number }}', '{{ $table->type }}', '{{ $table->status }}', '{{ $table->notes }}')">
                                    <i class="ri-pencil-line me-1"></i> Edit Details
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteTable({{ $table->id }}, '{{ $table->table_number }}')">
                                    <i class="ri-delete-bin-line me-1"></i> Delete Table
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="fw-bold mb-1">Table #{{ $table->table_number }}</h5>
                    <div class="d-flex align-items-center mb-3">
                        @if($table->type === 'vip')
                        <span class="badge bg-warning text-dark me-2">
                            <i class="ri-vip-crown-line me-1"></i>VIP
                        </span>
                        @else
                        <span class="badge bg-secondary me-2">Standard</span>
                        @endif
                        <span class="badge bg-white text-capitalize text-dark">{{ $table->status }}</span>
                    </div>

                    @if($table->notes)
                    <p class="text-muted small mb-0"><i class="ri-sticky-note-line me-1"></i>{{ Str::limit($table->notes, 40) }}</p>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="avatar avatar-xl bg-label-secondary mx-auto mb-3" style="width: 100px; height: 100px;">
                <i class="ri-restaurant-line" style="font-size: 3rem;"></i>
            </div>
            <h4>No tables found</h4>
            <p class="text-muted">Get started by creating your first restaurant table.</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTableModal">
                Create First Table
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Add/Edit Table Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-primary text-white p-4" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title text-white" id="modalTitle"><i class="ri-add-circle-line me-2"></i>Create New Table</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tableForm">
                @csrf
                <input type="hidden" name="table_id" id="table_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Table Number / Label</label>
                            <input type="text" class="form-control form-control-lg border-2" name="table_number" id="table_number" placeholder="e.g. 5 or VIP-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Table Type</label>
                            <select class="form-select border-2" name="type" id="table_type" required>
                                <option value="normal">Standard / Normal</option>
                                <option value="vip">VIP / Premium</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Initial Status</label>
                            <select class="form-select border-2" name="status" id="table_status" required>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="reserved">Reserved</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Notes / Description (Optional)</label>
                            <textarea class="form-control border-2" name="notes" id="table_notes" rows="3" placeholder="Additional details about the table..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 border-0">
                    <button type="button" class="btn btn-label-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="submitBtn">Create Table</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.table-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; border: 1px solid #71dd3733; }
.bg-label-warning { background-color: #fff2e2 !important; color: #ffab00 !important; border: 1px solid #ffab0033; }
.bg-label-info { background-color: #e7e7ff !important; color: #696cff !important; border: 1px solid #696cff33; }
</style>

@endsection

@push('scripts')
<script>
function editTable(id, number, type, status, notes) {
    document.getElementById('modalTitle').innerHTML = '<i class="ri-pencil-line me-2"></i>Update Table Details';
    document.getElementById('submitBtn').innerText = 'Update Table';
    document.getElementById('table_id').value = id;
    document.getElementById('table_number').value = number;
    document.getElementById('table_type').value = type;
    document.getElementById('table_status').value = status;
    document.getElementById('table_notes').value = notes || '';
    
    new bootstrap.Modal(document.getElementById('addTableModal')).show();
}

// Reset modal on close
document.getElementById('addTableModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('modalTitle').innerHTML = '<i class="ri-add-circle-line me-2"></i>Create New Table';
    document.getElementById('submitBtn').innerText = 'Create Table';
    document.getElementById('tableForm').reset();
    document.getElementById('table_id').value = '';
});

// Handle Form Submission
document.getElementById('tableForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const tableId = document.getElementById('table_id').value;
    const url = tableId ? `{{ url('counters/tables') }}/${tableId}` : `{{ route('counters.tables.store') }}`;
    const method = tableId ? 'PUT' : 'POST';
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            table_number: document.getElementById('table_number').value,
            type: document.getElementById('table_type').value,
            status: document.getElementById('table_status').value,
            notes: document.getElementById('table_notes').value
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message).then(() => location.reload());
        } else {
            showError(data.message || 'Something went wrong');
            submitBtn.disabled = false;
            submitBtn.innerText = tableId ? 'Update Table' : 'Create Table';
        }
    })
    .catch(err => {
        showError('Internal server error');
        submitBtn.disabled = false;
        submitBtn.innerText = tableId ? 'Update Table' : 'Create Table';
    });
});

function deleteTable(id, number) {
    showConfirm(`Are you sure you want to delete Table #${number}?`, 'This action cannot be undone.')
    .then(result => {
        if (result.isConfirmed) {
            fetch(`{{ url('counters/tables') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message).then(() => location.reload());
                } else {
                    showError(data.message);
                }
            });
        }
    });
}
</script>
@endpush
