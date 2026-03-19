<nav class="layout-navbar container-fluid navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-2 me-xl-6" href="javascript:void(0)">
      <i class="ri ri-menu-line" style="font-size: 32px !important;"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center me-auto">
      <div class="nav-item d-flex align-items-center">
        <div class="search-input-wrapper d-flex align-items-center">
          <i class="ri ri-search-line ri-20px me-1"></i>
          <input type="text" class="form-control border-0 shadow-none bg-transparent ps-1" placeholder="Search operations..." aria-label="Search..." />
        </div>
      </div>
    </div>

    <ul class="navbar-nav flex-row align-items-center ms-md-auto">
      <!-- Notifications -->
      <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
        <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" id="notificationsDropdown">
          <i class="ri ri-notification-line ri-22px"></i>
          <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border" id="notificationBadge" style="display: none;"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0" style="width: 380px;">
          <li class="dropdown-menu-header border-bottom py-3">
            <div class="dropdown-header d-flex align-items-center">
              <h6 class="mb-0 me-auto">Notifications</h6>
              <span class="badge rounded-pill bg-label-primary" id="notificationCount">0 New</span>
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container" style="max-height: 400px; overflow-y: auto;">
            <ul class="list-group list-group-flush" id="notificationsList">
              <li class="list-group-item list-group-item-action dropdown-notifications-item">
                <div class="d-flex">
                  <div class="flex-grow-1">
                    <p class="mb-0 text-body-secondary text-center py-3">
                      <span class="spinner-border spinner-border-sm me-2"></span>Loading notifications...
                    </p>
                  </div>
                </div>
              </li>
            </ul>
          </li>
          <li class="dropdown-menu-footer border-top p-2">
            <a href="{{ route('notifications.index') }}" class="btn btn-primary d-flex justify-content-center">View all notifications</a>
          </li>
        </ul>
      </li>

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            @if(Auth::check() && Auth::user()->avatar)
              <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
            @else
              <span class="avatar-initial rounded-circle bg-primary">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
            @endif
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="{{ route('profile') }}">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    @if(Auth::check() && Auth::user()->avatar)
                      <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                    @else
                      <span class="avatar-initial rounded-circle bg-primary">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                    @endif
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block">{{ Auth::user()->name ?? 'User' }}</span>
                  <small class="text-muted">{{ Auth::user()->email ?? '' }}</small>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('profile') }}">
              <i class="ri ri-user-settings-line me-2 ri-22px"></i>
              <span class="align-middle">Account Settings</span>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item">
                <i class="ri ri-logout-box-line me-2 ri-22px"></i>
                <span class="align-middle">Log Out</span>
              </button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
