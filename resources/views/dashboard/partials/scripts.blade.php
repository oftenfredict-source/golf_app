<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- SweetAlert Helper Functions -->
<style>
/* Ensure SweetAlert modals appear on top of everything */
.swal2-container {
  z-index: 99999 !important;
}
.swal2-popup {
  z-index: 100000 !important;
}
.swal2-backdrop-show {
  z-index: 99998 !important;
}
</style>
<script>
// Global SweetAlert helper functions for consistent styling
window.showSuccess = function(message, title = 'Success!') {
  return Swal.fire({
    icon: 'success',
    title: title,
    text: message,
    confirmButtonColor: '#940000',
    confirmButtonText: 'OK',
    customClass: {
      popup: 'rounded-lg',
      confirmButton: 'btn btn-primary'
    },
    didOpen: () => {
      // Ensure the modal appears on top of everything
      const swalContainer = document.querySelector('.swal2-container');
      if (swalContainer) {
        swalContainer.style.zIndex = '99999';
      }
      const swalPopup = document.querySelector('.swal2-popup');
      if (swalPopup) {
        swalPopup.style.zIndex = '100000';
      }
    }
  });
};

window.showError = function(message, title = 'Error!') {
  return Swal.fire({
    icon: 'error',
    title: title,
    text: message,
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'OK',
    customClass: {
      popup: 'rounded-lg',
      confirmButton: 'btn btn-danger'
    },
    didOpen: () => {
      // Ensure the modal appears on top of everything
      const swalContainer = document.querySelector('.swal2-container');
      if (swalContainer) {
        swalContainer.style.zIndex = '99999';
      }
      const swalPopup = document.querySelector('.swal2-popup');
      if (swalPopup) {
        swalPopup.style.zIndex = '100000';
      }
    }
  });
};

window.showWarning = function(message, title = 'Warning!') {
  return Swal.fire({
    icon: 'warning',
    title: title,
    text: message,
    confirmButtonColor: '#ffc107',
    confirmButtonText: 'OK',
    customClass: {
      popup: 'rounded-lg',
      confirmButton: 'btn btn-warning'
    },
    didOpen: () => {
      // Ensure the modal appears on top of everything
      const swalContainer = document.querySelector('.swal2-container');
      if (swalContainer) {
        swalContainer.style.zIndex = '99999';
      }
      const swalPopup = document.querySelector('.swal2-popup');
      if (swalPopup) {
        swalPopup.style.zIndex = '100000';
      }
    }
  });
};

window.showInfo = function(message, title = 'Information') {
  return Swal.fire({
    icon: 'info',
    title: title,
    text: message,
    confirmButtonColor: '#0dcaf0',
    confirmButtonText: 'OK',
    customClass: {
      popup: 'rounded-lg',
      confirmButton: 'btn btn-info'
    },
    didOpen: () => {
      // Ensure the modal appears on top of everything
      const swalContainer = document.querySelector('.swal2-container');
      if (swalContainer) {
        swalContainer.style.zIndex = '99999';
      }
      const swalPopup = document.querySelector('.swal2-popup');
      if (swalPopup) {
        swalPopup.style.zIndex = '100000';
      }
    }
  });
};

window.showConfirm = function(message, title = 'Confirm', confirmText = 'Yes', cancelText = 'No') {
  return Swal.fire({
    title: title,
    text: message,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#940000',
    cancelButtonColor: '#6c757d',
    confirmButtonText: confirmText,
    cancelButtonText: cancelText,
    customClass: {
      popup: 'rounded-lg',
      confirmButton: 'btn btn-primary',
      cancelButton: 'btn btn-secondary'
    },
    didOpen: () => {
      // Ensure the modal appears on top of everything
      const swalContainer = document.querySelector('.swal2-container');
      if (swalContainer) {
        swalContainer.style.zIndex = '99999';
      }
      const swalPopup = document.querySelector('.swal2-popup');
      if (swalPopup) {
        swalPopup.style.zIndex = '100000';
      }
    }
  });
};

// Enhanced success message with details (for transactions, top-ups, etc.)
window.showTransactionSuccess = function(data) {
  let html = '<div class="text-start">';
  
  if (data.member) {
    html += '<p class="mb-2"><strong>Member:</strong> ' + (data.member.name || data.member) + '</p>';
  }
  
  if (data.order_number) {
    html += '<p class="mb-2"><strong>Order Number:</strong> <code>' + data.order_number + '</code></p>';
  }
  
  if (data.amount !== undefined && data.amount !== null) {
    html += '<p class="mb-2"><strong>Amount:</strong> TZS ' + parseFloat(data.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</p>';
  }
  
  if (data.new_balance !== undefined) {
    html += '<p class="mb-0"><strong>New Balance:</strong> <span class="text-success fw-bold">TZS ' + parseFloat(data.new_balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></p>';
  }
  
  html += '</div>';
  
  return Swal.fire({
    icon: 'success',
    title: data.title || 'Transaction Successful!',
    html: html,
    confirmButtonColor: '#940000',
    confirmButtonText: 'OK',
    customClass: {
      popup: 'rounded-lg',
      confirmButton: 'btn btn-primary',
      container: 'swal-z-index-fixed'
    },
    didOpen: () => {
      // Ensure the modal appears on top of everything
      const swalContainer = document.querySelector('.swal2-container');
      if (swalContainer) {
        swalContainer.style.zIndex = '99999';
      }
      const swalPopup = document.querySelector('.swal2-popup');
      if (swalPopup) {
        swalPopup.style.zIndex = '100000';
      }
    }
  });
};

// Notifications System
(function() {
  function loadNotifications() {
    fetch('{{ route("notifications.fetch") }}', {
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      const listEl = document.getElementById('notificationsList');
      const countEl = document.getElementById('notificationCount');
      const badgeEl = document.getElementById('notificationBadge');
      
      if (!listEl || !countEl || !badgeEl) return;
      
      // Update count
      countEl.textContent = data.unread_count + ' New';
      
      // Show/hide badge
      if (data.unread_count > 0) {
        badgeEl.style.display = 'block';
      } else {
        badgeEl.style.display = 'none';
      }
      
      // Update list
      if (data.notifications && data.notifications.length > 0) {
        listEl.innerHTML = data.notifications.map(function(notif) {
          const isRead = notif.read_at !== null;
          const icon = notif.type.includes('success') || notif.type.includes('Success') ? 'ri-checkbox-circle-line text-success' : 
                      notif.type.includes('error') || notif.type.includes('Error') ? 'ri-error-warning-line text-danger' : 
                      'ri-information-line text-info';
          const data = notif.data || {};
          const title = data.title || 'Notification';
          const message = data.message || data.description || '';
          
          return '<li class="list-group-item list-group-item-action dropdown-notifications-item' + (isRead ? '' : ' bg-light') + '" data-id="' + notif.id + '">' +
            '<div class="d-flex">' +
              '<div class="flex-shrink-0 me-3">' +
                '<div class="avatar"><div class="avatar-initial rounded bg-label-primary"><i class="ri ' + icon + '"></i></div></div>' +
              '</div>' +
              '<div class="flex-grow-1">' +
                '<h6 class="mb-1">' + title + '</h6>' +
                '<p class="mb-0 text-body-secondary small">' + message + '</p>' +
                '<small class="text-muted">' + notif.created_at + '</small>' +
              '</div>' +
              (!isRead ? '<div class="flex-shrink-0 ms-2"><span class="badge badge-dot bg-danger"></span></div>' : '') +
            '</div>' +
          '</li>';
        }).join('');
        
        // Add click handlers
        listEl.querySelectorAll('[data-id]').forEach(function(item) {
          item.addEventListener('click', function() {
            const notifId = this.getAttribute('data-id');
            markNotificationAsRead(notifId);
          });
        });
      } else {
        listEl.innerHTML = '<li class="list-group-item list-group-item-action dropdown-notifications-item">' +
          '<div class="d-flex"><div class="flex-grow-1">' +
          '<p class="mb-0 text-body-secondary text-center py-3">No new notifications</p>' +
          '</div></div></li>';
      }
    })
    .catch(function(err) {
      console.error('Error loading notifications:', err);
    });
  }
  
  function markNotificationAsRead(notifId) {
    fetch('{{ route("notifications.read", ":id") }}'.replace(':id', notifId), {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json'
      }
    })
    .then(function(r) { return r.json(); })
    .then(function() {
      loadNotifications(); // Reload after marking as read
    })
    .catch(function(err) {
      console.error('Error marking notification as read:', err);
    });
  }
  
  // Load notifications on page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadNotifications);
  } else {
    loadNotifications();
  }
  
  // Reload notifications every 30 seconds
  setInterval(loadNotifications, 30000);
  
  // Reload when dropdown is shown
  const dropdown = document.getElementById('notificationsDropdown');
  if (dropdown) {
    dropdown.addEventListener('shown.bs.dropdown', loadNotifications);
  }
})();
</script>

