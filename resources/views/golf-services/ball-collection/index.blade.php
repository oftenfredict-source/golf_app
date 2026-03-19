@extends('settings._layout-base')

@section('title', 'Ball Collection Management')

@section('content')
<div class="row g-4">
    <!-- Stats Header -->
    <div class="col-12">
        <div class="card glassmorphism border-0 mb-4">
            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="mb-1 fw-bold text-center text-md-start">Ball Collection</h4>
                    <p class="text-muted mb-0 text-center text-md-start small">Manage collectors and track field returns</p>
                </div>
                <button class="btn btn-primary px-4 py-2 fw-bold w-100 w-md-auto" style="background: var(--accent-red); border: none;" data-bs-toggle="modal" data-bs-target="#addCollectorModal">
                    <i class="ri-user-add-line me-1"></i> Add Collector
                </button>
            </div>
        </div>
    </div>

    <!-- Collector List -->
    <div class="col-md-5">
        <div class="card glassmorphism border-0 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="mb-0 fw-bold">Field Collectors</h5>
            </div>
            <div class="card-body px-4">
                <div class="list-group list-group-flush">
                    @forelse($collectors as $collector)
                    <div class="list-group-item bg-transparent border-light px-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-initial rounded-circle bg-light text-primary me-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    {{ substr($collector->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $collector->name }}</h6>
                                    <small class="text-muted">{{ $collector->phone ?? 'No phone' }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge rounded-pill {{ $collector->status === 'active' ? 'bg-label-success' : 'bg-label-secondary' }} mb-2">
                                    {{ ucfirst($collector->status) }}
                                </span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon border-0" data-bs-toggle="dropdown">
                                        <i class="ri-more-2-fill"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end glassmorphism border-0 shadow">
                                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="editCollector({{ json_encode($collector) }})">Edit Details</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('golf-services.ball-collection.collectors.destroy', $collector->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this collector?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Remove Staff</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="ri-user-unfollow-line fs-1 text-muted"></i>
                        <p class="mt-2 text-muted">No collectors registered yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Collection History -->
    <div class="col-md-7">
        <div class="card glassmorphism border-0 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Today's Collection Logs</h5>
                <span class="badge bg-light text-dark">{{ count($todaysLogs) }} entries</span>
            </div>
            <div class="card-body px-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th>Collector</th>
                                <th>Quantity</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todaysLogs as $log)
                            <tr>
                                <td class="fw-bold">{{ $log->collector->name }}</td>
                                <td>
                                    <span class="fs-6">{{ number_format($log->quantity_collected) }}</span>
                                    @if($log->target_quantity)
                                    <br><small class="text-muted">Target: {{ number_format($log->target_quantity) }}</small>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $log->created_at->format('H:i') }}
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $log->status === 'verified' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->status === 'pending')
                                    <button class="btn btn-sm btn-primary border-0 px-3" onclick="verifyCollection({{ $log->id }})">
                                        Verify
                                    </button>
                                    @else
                                    <i class="ri-checkbox-circle-fill text-success fs-4"></i>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No collection activity recorded today
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

<!-- Add Collector Modal -->
<div class="modal fade" id="addCollectorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glassmorphism">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Register New Collector</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('golf-services.ball-collection.collectors.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Full Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="Name of staff">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="Optional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Operating Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="background: var(--accent-red); border: none;">Save Collector</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Collector Modal -->
<div class="modal fade" id="editCollectorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glassmorphism">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Collector Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCollectorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Full Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Phone Number</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Operating Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="background: var(--accent-red); border: none;">Update Details</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Verify Collection Modal -->
<div class="modal fade" id="verifyCollectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glassmorphism">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Verify Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="verifyForm">
                @csrf
                <input type="hidden" name="collection_id" id="verify_collection_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Actual Quantity Collected</label>
                        <input type="number" name="quantity_collected" class="form-control" required placeholder="Enter final count">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="background: #27ae60; border: none;">Confirm & Verify</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCollector(collector) {
    document.getElementById('edit_name').value = collector.name;
    document.getElementById('edit_phone').value = collector.phone || '';
    document.getElementById('edit_status').value = collector.status;
    document.getElementById('editCollectorForm').action = `{{ url('golf-services/ball-collection/collectors') }}/${collector.id}`;
    new bootstrap.Modal(document.getElementById('editCollectorModal')).show();
}

function verifyCollection(id) {
    document.getElementById('verify_collection_id').value = id;
    new bootstrap.Modal(document.getElementById('verifyCollectionModal')).show();
}

document.getElementById('verifyForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const id = formData.get('collection_id');

    fetch(`{{ url('golf-services/ball-collection') }}/${id}/verify`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Verification failed');
        }
    });
});
</script>

<style>
.glassmorphism {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.18);
}
.avatar-initial {
    font-weight: bold;
    font-size: 1.1rem;
}
.bg-label-success {
    background-color: #e8fadf;
    color: #71dd37;
}
.bg-label-secondary {
    background-color: #f1f0f2;
    color: #8592a3;
}
</style>
@endsection
