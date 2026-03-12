@extends('settings._layout-base')

@section('title', 'Notifications')
@section('description', 'Notifications - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Notifications</span>
</h4>

<!-- Statistics -->
<div class="row mb-4">
  <div class="col-md-4 col-6 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-primary rounded">
              <i class="icon-base ri ri-notification-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary small">Total Notifications</p>
            <h5 class="mb-0">{{ $notifications->total() }}</h5>
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
            <div class="avatar-initial bg-warning rounded">
              <i class="icon-base ri ri-time-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary small">Unread</p>
            <h5 class="mb-0">{{ auth()->user()->unreadNotifications()->count() }}</h5>
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
              <i class="icon-base ri ri-checkbox-circle-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary small">Read</p>
            <h5 class="mb-0">{{ auth()->user()->readNotifications()->count() }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Notifications List -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="icon-base ri ri-notification-line me-2"></i>All Notifications</h5>
    @if(auth()->user()->unreadNotifications()->count() > 0)
    <button class="btn btn-sm btn-primary" onclick="markAllAsRead()">
      <i class="icon-base ri ri-check-double-line me-1"></i> Mark All as Read
    </button>
    @endif
  </div>
  <div class="card-body p-0">
    <div class="list-group list-group-flush">
      @forelse($notifications as $notification)
      <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'bg-light' }}" data-id="{{ $notification->id }}">
        <div class="d-flex align-items-start">
          <div class="flex-shrink-0 me-3">
            @php
              $icon = 'ri-information-line';
              $iconClass = 'text-info';
              if (isset($notification->data['type']) || (isset($notification->data['title']) && str_contains(strtolower($notification->data['title'] ?? ''), 'success'))) {
                $icon = 'ri-checkbox-circle-line';
                $iconClass = 'text-success';
              } elseif (isset($notification->data['title']) && str_contains(strtolower($notification->data['title'] ?? ''), 'error')) {
                $icon = 'ri-error-warning-line';
                $iconClass = 'text-danger';
              }
            @endphp
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="icon-base ri {{ $icon }} {{ $iconClass }}"></i>
              </div>
            </div>
          </div>
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <h6 class="mb-0">{{ $notification->data['title'] ?? 'Notification' }}</h6>
              @if(!$notification->read_at)
              <span class="badge badge-dot bg-danger"></span>
              @endif
            </div>
            <p class="mb-1 text-body-secondary">{{ $notification->data['message'] ?? $notification->data['description'] ?? '' }}</p>
            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
            @if(isset($notification->data['link']) && $notification->data['link'])
            <div class="mt-2">
              <a href="{{ $notification->data['link'] }}" class="btn btn-sm btn-label-primary">
                <i class="icon-base ri ri-arrow-right-line me-1"></i> View Details
              </a>
            </div>
            @endif
          </div>
        </div>
      </div>
      @empty
      <div class="list-group-item">
        <div class="text-center py-5 text-body-secondary">
          <i class="icon-base ri ri-notification-off-line" style="font-size: 48px; opacity: 0.3;"></i>
          <p class="mt-2 mb-0">No notifications found</p>
        </div>
      </div>
      @endforelse
    </div>
  </div>
  @if($notifications->hasPages())
  <div class="card-footer">
    {{ $notifications->links() }}
  </div>
  @endif
</div>

@push('scripts')
<script>
function markAllAsRead() {
  if (!confirm('Mark all notifications as read?')) return;
  
  fetch('{{ route("notifications.read-all") }}', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    }
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.success) {
      location.reload();
    }
  })
  .catch(function(err) {
    console.error('Error:', err);
    alert('Error marking notifications as read');
  });
}

// Mark as read when clicking on notification
document.querySelectorAll('.list-group-item[data-id]').forEach(function(item) {
  item.addEventListener('click', function(e) {
    // Don't trigger if clicking on a link
    if (e.target.closest('a')) return;
    
    const notifId = this.getAttribute('data-id');
    if (this.classList.contains('bg-light')) { // Only if unread
      fetch('{{ route("notifications.read", ":id") }}'.replace(':id', notifId), {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json'
        }
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          location.reload();
        }
      })
      .catch(function(err) {
        console.error('Error:', err);
      });
    }
  });
});
</script>
@endpush
@endsection


