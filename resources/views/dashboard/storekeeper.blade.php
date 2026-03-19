@extends('settings._layout-base')

@section('title', 'Storekeeper Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- Ball Status Card -->
    <div class="col-md-3">
        <div class="stats-card glassmorphism h-100" style="border-left: 4px solid var(--accent-red);">
            <div class="card-body">
                <h6 class="text-muted mb-2 text-uppercase fw-bold small">Available Balls</h6>
                <h2 class="mb-0 fw-bold">{{ number_format($ballStats['available']) }}</h2>
                <div class="mt-2 small text-muted">
                    Total Stock: {{ number_format($ballStats['total']) }}
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card glassmorphism h-100" style="border-left: 4px solid #f39c12;">
            <div class="card-body">
                <h6 class="text-muted mb-2 text-uppercase fw-bold small">Balls In Use</h6>
                <h2 class="mb-0 fw-bold">{{ number_format($ballStats['in_use']) }}</h2>
                <div class="mt-2 small text-muted">
                    Currently on range
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card glassmorphism h-100" style="border-left: 4px solid #27ae60;">
            <div class="card-body">
                <h6 class="text-muted mb-2 text-uppercase fw-bold small">Collected Today</h6>
                <h2 class="mb-0 fw-bold">{{ number_format($ballStats['collected_today']) }}</h2>
                <div class="mt-2 small text-muted">
                    Returned to stock
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card glassmorphism h-100" style="border-left: 4px solid #3498db;">
            <div class="card-body">
                <h6 class="text-muted mb-2 text-uppercase fw-bold small">Active Rentals</h6>
                <h2 class="mb-0 fw-bold">{{ number_format($equipmentStats['active_rentals']) }}</h2>
                <div class="mt-2 small text-muted">
                    Equipment out
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Ball Collection Assignment -->
    <div class="col-md-6">
        <div class="card glassmorphism border-0 h-100">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="mb-0 fw-bold">Ball Collection Assignment</h5>
            </div>
            <div class="card-body px-4">
                <form id="collectionForm" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Select Collector</label>
                        <select name="collector_id" class="form-select border-0 bg-light" required>
                            <option value="">Choose staff...</option>
                            @foreach($collectors as $collector)
                                <option value="{{ $collector->id }}">{{ $collector->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Target Quantity (Optional)</label>
                        <input type="number" name="target_quantity" class="form-select border-0 bg-light" placeholder="e.g. 500">
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="background: var(--accent-red); border: none;">
                            Assign Collector
                        </button>
                    </div>
                </form>

                <div class="mt-5">
                    <h6 class="text-muted text-uppercase fw-bold small mb-3">Today's Collections</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Collector</th>
                                    <th>Quantity</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCollections as $collection)
                                <tr>
                                    <td class="fw-bold">{{ $collection->collector->name }}</td>
                                    <td>{{ number_format($collection->quantity_collected) }}</td>
                                    <td>{{ $collection->created_at->format('H:i') }}</td>
                                    <td>
                                        <span class="badge rounded-pill {{ $collection->status === 'verified' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($collection->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($collection->status === 'pending')
                                        <button class="btn btn-sm btn-outline-success border-0 px-3" onclick="verifyCollection({{ $collection->id }})">
                                            Verify
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">No collections logged today</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Tools & Recent History -->
    <div class="col-md-6">
        <div class="row g-4">
            <!-- Quick Actions -->
            <div class="col-12">
                <div class="card glassmorphism border-0">
                    <div class="card-body p-4 text-center">
                        <div class="d-flex justify-content-between gap-3">
                            <a href="{{ route('golf-services.equipment-rental') }}" class="btn btn-light border-0 py-3 flex-grow-1 fw-bold">
                                <i class="bi bi-person-check-fill d-block mb-1 fs-4 text-primary"></i>
                                Equipment Rental
                            </a>
                            <a href="{{ route('golf-services.equipment-sales') }}" class="btn btn-light border-0 py-3 flex-grow-1 fw-bold">
                                <i class="bi bi-bag-check-fill d-block mb-1 fs-4 text-success"></i>
                                Pro Shop Sales
                            </a>
                            <a href="{{ route('golf-services.ball-management') }}" class="btn btn-light border-0 py-3 flex-grow-1 fw-bold">
                                <i class="bi bi-gear-fill d-block mb-1 fs-4 text-warning"></i>
                                Ball Management
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Ball Transactions -->
            <div class="col-12">
                <div class="card glassmorphism border-0">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold">Recent Ball Activity</h5>
                    </div>
                    <div class="card-body px-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Customer</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBallTransactions as $txn)
                                    <tr>
                                        <td class="fw-bold">{{ $txn->customer_name }}</td>
                                        <td>
                                            <span class="small text-muted text-uppercase">{{ $txn->type }}</span>
                                        </td>
                                        <td class="fw-bold">{{ number_format($txn->quantity) }}</td>
                                        <td class="small text-muted">{{ $txn->created_at->format('H:i') }}</td>
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

<!-- Log Collection Modal -->
<div class="modal fade" id="logCollectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glassmorphism">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Verify Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="verifyForm">
                    @csrf
                    <input type="hidden" name="collection_id" id="verify_collection_id">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Confirmed Quantity Collected</label>
                        <input type="number" name="quantity_collected" class="form-control" required placeholder="Enter final count">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="background: var(--accent-red); border: none;">
                        Confirm & Verify
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('collectionForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('{{ route("golf-services.ball-collection.assign") }}', {
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
            alert(data.message || 'Failed to assign collector');
        }
    });
});

function verifyCollection(id) {
    document.getElementById('verify_collection_id').value = id;
    new bootstrap.Modal(document.getElementById('logCollectionModal')).show();
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
.stats-card {
    border-radius: 12px;
    padding: 1.5rem;
    transition: transform 0.2s ease;
}
.stats-card:hover {
    transform: translateY(-5px);
}
.glassmorphism {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.18);
}
</style>
@endsection
