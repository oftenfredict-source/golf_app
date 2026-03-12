@extends('settings._layout-base')

@section('title', 'Activity Logs')
@section('description', 'Activity Logs - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Logs /</span> Activity Logs
</h4>

<!-- Statistics -->
<div class="row mb-4">
  <div class="col-md-4 col-6 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-primary rounded">
              <i class="icon-base ri ri-file-list-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary small">Total Logs</p>
            <h5 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-6 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-success rounded">
              <i class="icon-base ri ri-calendar-today-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary small">Today</p>
            <h5 class="mb-0">{{ number_format($stats['today'] ?? 0) }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-6 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-info rounded">
              <i class="icon-base ri ri-time-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary small">This Week</p>
            <h5 class="mb-0">{{ number_format($stats['this_week'] ?? 0) }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0"><i class="icon-base ri ri-filter-line me-2"></i>Filters</h5>
  </div>
  <div class="card-body">
    <form method="GET" action="{{ route('logs.activity-logs') }}" class="row g-4" id="filterForm">
      <div class="col-md-3">
        <div class="form-floating form-floating-outline">
          <input type="date" class="form-control" id="log_from" name="from_date" value="{{ request('from_date') }}" />
          <label for="log_from">From Date</label>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-floating form-floating-outline">
          <input type="date" class="form-control" id="log_to" name="to_date" value="{{ request('to_date') }}" />
          <label for="log_to">To Date</label>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-floating form-floating-outline">
          <select class="form-select" id="log_module" name="module">
            <option value="">All Modules</option>
            <option value="payments" {{ request('module') == 'payments' ? 'selected' : '' }}>Payments</option>
            <option value="golf-services" {{ request('module') == 'golf-services' ? 'selected' : '' }}>Golf Services</option>
            <option value="services" {{ request('module') == 'services' ? 'selected' : '' }}>Club Services</option>
            <option value="auth" {{ request('module') == 'auth' ? 'selected' : '' }}>Authentication</option>
            <option value="inventory" {{ request('module') == 'inventory' ? 'selected' : '' }}>Inventory</option>
            @foreach($modules ?? [] as $module)
              @if(!in_array($module, ['payments', 'golf-services', 'services', 'auth', 'inventory']))
              <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ ucfirst($module) }}</option>
              @endif
            @endforeach
          </select>
          <label for="log_module">Module</label>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-floating form-floating-outline">
          <select class="form-select" id="log_action" name="action">
            <option value="">All Actions</option>
            @foreach($actions ?? [] as $action)
            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
            @endforeach
          </select>
          <label for="log_action">Action</label>
        </div>
      </div>
      <div class="col-md-12">
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-search-line me-1"></i> Apply Filters
          </button>
          <a href="{{ route('logs.activity-logs') }}" class="btn btn-label-secondary">
            <i class="icon-base ri ri-refresh-line me-1"></i> Reset
          </a>
          <a href="{{ route('logs.activity-logs.export', request()->all()) }}" class="btn btn-label-success">
            <i class="icon-base ri ri-download-line me-1"></i> Export CSV
          </a>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Logs table -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="icon-base ri ri-file-list-line me-2"></i>Activity Logs</h5>
    <div class="text-muted small">
      Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs ?? [] as $log)
          <tr>
            <td>
              <div>{{ $log->created_at->format('d M Y, H:i') }}</div>
            </td>
            <td>
              @if($log->user)
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-2">
                    <div class="avatar-initial bg-primary rounded">
                      {{ substr($log->user->name, 0, 1) }}
                    </div>
                  </div>
                  <div class="fw-medium">{{ $log->user->name }}</div>
                </div>
              @else
                <span class="text-muted">System</span>
              @endif
            </td>
            <td>
              @php
                $actionColors = [
                  'created' => 'success',
                  'updated' => 'info',
                  'deleted' => 'danger',
                  'completed' => 'success',
                  'cancelled' => 'warning',
                  'topup' => 'success',
                  'payment' => 'primary',
                  'refund' => 'warning',
                ];
                $color = $actionColors[$log->action] ?? 'secondary';
              @endphp
              <span class="badge bg-label-{{ $color }}">{{ ucfirst($log->action) }}</span>
            </td>
            <td>
              <div class="text-wrap" style="max-width: 400px;">{{ strlen($log->description) > 80 ? substr($log->description, 0, 80) . '...' : $log->description }}</div>
            </td>
            <td>
              <button class="btn btn-sm btn-label-primary" onclick="viewLogDetails({{ $log->id }})" title="View Details">
                <i class="icon-base ri ri-eye-line"></i> View More
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center py-5 text-body-secondary">
              <i class="icon-base ri ri-file-list-line" style="font-size: 48px; opacity: 0.3;"></i>
              <p class="mt-2 mb-0">No activity logs found</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($logs->hasPages())
  <div class="card-footer">
    {{ $logs->links() }}
  </div>
  @endif
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="icon-base ri ri-file-list-line me-2"></i>Activity Log Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="logDetailsContent">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Store log data for quick access
const logsData = @json($logs->items() ?? []);

function viewLogDetails(logId) {
  const log = logsData.find(l => l.id === logId);
  if (!log) {
    alert('Log details not found');
    return;
  }
  
  const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
  const contentDiv = document.getElementById('logDetailsContent');
  
  // Build details HTML
  let html = '<div class="row g-3">';
  
  // Time
  html += '<div class="col-12"><div class="border-bottom pb-2 mb-2"><small class="text-muted d-block mb-1">Time</small><strong>' + 
    new Date(log.created_at).toLocaleString() + '</strong></div></div>';
  
  // User
  html += '<div class="col-md-6"><small class="text-muted d-block mb-1">User</small>';
  if (log.user) {
    html += '<div class="d-flex align-items-center"><div class="avatar avatar-sm me-2"><div class="avatar-initial bg-primary rounded">' + 
      (log.user.name ? log.user.name.charAt(0).toUpperCase() : 'U') + '</div></div><div><div class="fw-medium">' + 
      (log.user.name || 'Unknown') + '</div><small class="text-muted">' + (log.user.email || '') + '</small></div></div>';
  } else {
    html += '<span class="text-muted">System</span>';
  }
  html += '</div>';
  
  // Module
  html += '<div class="col-md-6"><small class="text-muted d-block mb-1">Module</small><span class="badge bg-label-primary">' + 
    (log.module ? log.module.charAt(0).toUpperCase() + log.module.slice(1) : '-') + '</span></div>';
  
  // Action
  const actionColors = {
    'created': 'success',
    'updated': 'info',
    'deleted': 'danger',
    'completed': 'success',
    'cancelled': 'warning',
    'topup': 'success',
    'payment': 'primary',
    'refund': 'warning'
  };
  const actionColor = actionColors[log.action] || 'secondary';
  html += '<div class="col-md-6"><small class="text-muted d-block mb-1">Action</small><span class="badge bg-label-' + actionColor + '">' + 
    (log.action ? log.action.charAt(0).toUpperCase() + log.action.slice(1) : '-') + '</span></div>';
  
  // Entity
  html += '<div class="col-md-6"><small class="text-muted d-block mb-1">Entity</small>';
  if (log.entity_type && log.entity_id) {
    html += '<code>' + log.entity_type + ' #' + log.entity_id + '</code>';
  } else {
    html += '<span class="text-muted">-</span>';
  }
  html += '</div>';
  
  // Description
  html += '<div class="col-12"><small class="text-muted d-block mb-1">Description</small><div class="p-2 bg-light rounded">' + 
    (log.description || '-') + '</div></div>';
  
  // Additional Data
  if (log.data && Object.keys(log.data).length > 0) {
    html += '<div class="col-12"><small class="text-muted d-block mb-1">Additional Details</small><div class="p-2 bg-light rounded">';
    const data = log.data;
    const details = [];
    if (data.amount !== undefined) details.push('<strong>Amount:</strong> TZS ' + parseFloat(data.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    if (data.balance_before !== undefined) details.push('<strong>Balance Before:</strong> TZS ' + parseFloat(data.balance_before).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    if (data.balance_after !== undefined) details.push('<strong>Balance After:</strong> TZS ' + parseFloat(data.balance_after).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    if (data.quantity !== undefined) details.push('<strong>Quantity:</strong> ' + data.quantity);
    if (data.payment_method) details.push('<strong>Payment Method:</strong> ' + data.payment_method);
    if (details.length > 0) {
      html += '<div class="row g-2">';
      details.forEach(function(detail) {
        html += '<div class="col-md-6">' + detail + '</div>';
      });
      html += '</div>';
    } else {
      html += '<pre class="mb-0 small">' + JSON.stringify(data, null, 2) + '</pre>';
    }
    html += '</div></div>';
  }
  
  // IP Address
  html += '<div class="col-md-6"><small class="text-muted d-block mb-1">IP Address</small><code>' + (log.ip_address || '-') + '</code></div>';
  
  // User Agent
  if (log.user_agent) {
    html += '<div class="col-md-6"><small class="text-muted d-block mb-1">User Agent</small><small class="text-break">' + log.user_agent + '</small></div>';
  }
  
  html += '</div>';
  
  contentDiv.innerHTML = html;
  modal.show();
}
</script>
@endpush
@endsection
