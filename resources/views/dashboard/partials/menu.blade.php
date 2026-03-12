<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <i class="ri ri-golf-ball-line" style="font-size: 28px; color: #940000;"></i>
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2" style="color: #940000;">GOLF CLUB</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="ri ri-close-line align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a href="{{ route('dashboard') }}" class="menu-link">
        <i class="menu-icon ri ri-home-line"></i>
        <div>Dashboard</div>
      </a>
    </li>

    <li class="menu-header small text-uppercase"><span class="menu-header-text">Operations</span></li>

    <!-- Daily Operations -->
    <li class="menu-item {{ request()->routeIs('golf-services.driving-range', 'golf-services.equipment-*', 'services.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon ri ri-flashlight-line"></i>
        <div>Daily Operations</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('golf-services.driving-range') ? 'active' : '' }}">
          <a href="{{ route('golf-services.driving-range') }}" class="menu-link">
            <div>Driving Range</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('golf-services.ball-management') ? 'active' : '' }}">
          <a href="{{ route('golf-services.ball-management') }}" class="menu-link">
            <div>Ball Management</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('golf-services.equipment-rental') ? 'active' : '' }}">
          <a href="{{ route('golf-services.equipment-rental') }}" class="menu-link">
            <div>Equipment Rental</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('golf-services.equipment-sales') ? 'active' : '' }}">
          <a href="{{ route('golf-services.equipment-sales') }}" class="menu-link">
            <div>Pro Shop Sales</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('services.food-beverage') ? 'active' : '' }}">
          <a href="{{ route('services.food-beverage') }}" class="menu-link">
            <div>Food & Beverage</div>
          </a>
        </li>
      </ul>
    </li>

    <!-- Member Center -->
    <li class="menu-item {{ request()->routeIs('payments.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon ri ri-user-settings-line"></i>
        <div>Member Center</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('payments.upi-management') ? 'active' : '' }}">
          <a href="{{ route('payments.upi-management') }}" class="menu-link">
            <div>Member Directory</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('payments.top-ups') ? 'active' : '' }}">
          <a href="{{ route('payments.top-ups') }}" class="menu-link">
            <div>Quick Top-up</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('payments.generate-card') ? 'active' : '' }}">
          <a href="{{ route('payments.generate-card') }}" class="menu-link">
            <div>Member Cards</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('payments.transactions') ? 'active' : '' }}">
          <a href="{{ route('payments.transactions') }}" class="menu-link">
            <div>All Transactions</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-header small text-uppercase"><span class="menu-header-text">Management</span></li>

    <!-- Admin Tools -->
    <li class="menu-item {{ request()->routeIs('reports.*', 'inventory.*', 'access-control.*', 'logs.*', 'settings.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon ri ri-settings-4-line"></i>
        <div>Admin & Reports</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
          <a href="{{ route('reports.index') }}" class="menu-link">
            <div>Reports Overview</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
          <a href="{{ route('inventory.index') }}" class="menu-link">
            <div>Inventory Stock</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('access-control.*') ? 'active' : '' }}">
          <a href="{{ route('access-control.entry-gates') }}" class="menu-link">
            <div>Gate Access Logs</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
          <a href="{{ route('logs.activity-logs') }}" class="menu-link">
            <div>Activity Logs</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
          <a href="{{ route('settings.configuration') }}" class="menu-link">
            <div>System Settings</div>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</aside>
