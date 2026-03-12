@extends('settings._layout-base')

@section('title', 'Orders')
@section('description', 'Orders Management - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Club Services /</span> Orders
</h4>

<div class="row mb-6">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Orders</h5>
        <button class="btn btn-label-secondary btn-sm" onclick="location.reload()">
          <i class="ri ri-refresh-line me-1"></i> Refresh
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Table</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Time</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($todayOrders ?? [] as $order)
              <tr>
                <td><strong>{{ $order->order_number }}</strong></td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->table_number ?? '-' }}</td>
                <td>
                  @foreach($order->items as $item)
                    <small>{{ $item->quantity }}x {{ $item->menuItem->name ?? 'Item' }}</small><br>
                  @endforeach
                </td>
                <td>TZS {{ number_format($order->total_amount) }}</td>
                <td>
                  @if($order->payment_method === 'balance')
                    <span class="badge bg-label-success">Member Balance</span>
                  @else
                    <span class="badge bg-label-secondary">{{ strtoupper($order->payment_method) }}</span>
                  @endif
                  @if($order->member_id && $order->member)
                    <br><small class="text-muted">{{ $order->member->card_number ?? 'Member #' . $order->member_id }}</small>
                  @endif
                </td>
                <td>
                  @switch($order->status)
                    @case('pending')
                      <span class="badge bg-label-warning">Pending</span>
                      @break
                    @case('preparing')
                      <span class="badge bg-label-info">Preparing</span>
                      @break
                    @case('ready')
                      <span class="badge bg-label-success">Ready</span>
                      @break
                    @case('served')
                      <span class="badge bg-label-primary">Served</span>
                      @break
                  @case('saved')
                      <span class="badge bg-label-warning">Saved</span>
                      @break
                  @case('complete')
                  @case('completed')
                      <span class="badge bg-label-success">Completed</span>
                      @break
                  @case('cancelled')
                      <span class="badge bg-label-danger">Cancelled</span>
                      @break
                  @endswitch
                </td>
                <td>{{ $order->created_at->format('H:i') }}</td>
                <td>
                  @if($order->status === 'saved')
                    <button class="btn btn-sm btn-primary" onclick="updateStatus({{ $order->id }}, 'complete')">Mark Complete</button>
                    <button class="btn btn-sm btn-danger ms-1" onclick="showConfirm('Cancel this order and refund to member balance?').then(r => { if(r.isConfirmed) updateStatus({{ $order->id }}, 'cancelled'); })">Cancel</button>
                  @elseif($order->status === 'complete' || $order->status === 'completed')
                    <span class="text-muted small">Order completed</span>
                  @elseif($order->status === 'cancelled')
                    <span class="text-muted small">Cancelled & refunded</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center py-4 text-body-secondary">No orders today</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(orderId, status) {
  fetch(`{{ url('services/orders') }}/${orderId}/status`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ status: status })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showSuccess(data.message || 'Status updated successfully').then(() => location.reload());
    } else {
      showError(data.message || 'Error updating status');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showError('An error occurred. Please try again.');
  });
}
</script>
@endpush


