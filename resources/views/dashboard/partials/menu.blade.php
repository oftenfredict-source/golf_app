<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo py-4 mb-2">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo me-1">
        <i class="ri ri-golf-ball-line" style="font-size: 32px; color: #940000;"></i>
      </span>
      <span class="app-brand-text demo menu-text fw-bolder ms-1" style="color: #940000; letter-spacing: -0.5px; font-size: 1.25rem;">GOLF<span class="fw-light">CLUB</span></span>
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
        <i class="menu-icon ri ri-dashboard-line"></i>
        <div>Main Dashboard</div>
      </a>
    </li>

    @if(auth()->user()->role === 'admin')
    <li class="menu-header"><span class="menu-header-text">MANAGEMENT</span></li>
    
    <li class="menu-item {{ request()->routeIs('settings.users') ? 'active' : '' }}">
      <a href="{{ route('settings.users') }}" class="menu-link">
        <i class="menu-icon ri ri-group-line"></i>
        <div>Staff Management</div>
      </a>
    </li>
    
    <li class="menu-item {{ request()->routeIs('settings.configuration') ? 'active' : '' }}">
      <a href="{{ route('settings.configuration') }}" class="menu-link">
        <i class="menu-icon ri ri-settings-5-line"></i>
        <div>System Settings</div>
      </a>
    </li>

    <li class="menu-item {{ request()->routeIs('logs.activity-logs') ? 'active' : '' }}">
      <a href="{{ route('logs.activity-logs') }}" class="menu-link">
        <i class="menu-icon ri ri-article-line"></i>
        <div>Activity Logs</div>
      </a>
    </li>
    @endif

    <!-- Member Center -->
    @if(in_array(auth()->user()->role, ['admin', 'reception', 'storekeeper']))
    <li class="menu-header">
      <span class="menu-header-text">MEMBERSHIP</span>
    </li>
    
    <li class="menu-item {{ request()->routeIs('payments.upi-management') ? 'active' : '' }}">
      <a href="{{ route('payments.upi-management') }}" class="menu-link">
        <i class="menu-icon ri ri-group-2-line"></i>
        <div>Member Directory</div>
      </a>
    </li>
    
    @if(auth()->user()->role !== 'storekeeper')
    <li class="menu-item {{ request()->routeIs('payments.top-ups') ? 'active' : '' }}">
      <a href="{{ route('payments.top-ups') }}" class="menu-link">
        <i class="menu-icon ri ri-bank-card-line"></i>
        <div>Account Top-ups</div>
      </a>
    </li>
    
    <li class="menu-item {{ request()->routeIs('payments.generate-card') ? 'active' : '' }}">
      <a href="{{ route('payments.generate-card') }}" class="menu-link">
        <i class="menu-icon ri ri-id-card-line"></i>
        <div>Member Cards</div>
      </a>
    </li>
    @endif
    @endif

    {{-- Counter Dashboard hidden per user request --}}

    <!-- Daily Operations -->
    @if(in_array(auth()->user()->role, ['admin', 'manager', 'storekeeper', 'counter']))
    <li class="menu-item {{ request()->routeIs('golf-services.*', 'services.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon ri ri-flashlight-line"></i>
        <div>Daily Operations</div>
      </a>
      <ul class="menu-sub">
        @if(auth()->user()->role !== 'counter')
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
        @endif
        @if(in_array(auth()->user()->role, ['counter', 'admin']))
        <li class="menu-item {{ request()->routeIs('services.counter.dashboard') ? 'active' : '' }}">
          <a href="{{ route('services.counter.dashboard') }}" class="menu-link">
            <div>Station Dashboard</div>
          </a>
        </li>
        @endif
        @if(in_array(auth()->user()->role, ['chef', 'admin']))
        <li class="menu-item {{ request()->routeIs('kitchen.dashboard') ? 'active' : '' }}">
          <a href="{{ route('kitchen.dashboard') }}" class="menu-link">
            <div>Kitchen Dashboard</div>
          </a>
        </li>
        @endif
        @if(in_array(auth()->user()->role, ['waiter', 'admin']))
        <li class="menu-item {{ request()->routeIs('waiter.dashboard') ? 'active' : '' }}">
          <a href="{{ route('waiter.dashboard') }}" class="menu-link">
            <div>Table Service</div>
          </a>
        </li>
        @endif
        @if(!in_array(auth()->user()->role, ['storekeeper', 'counter']))
        <li class="menu-item {{ request()->routeIs('services.food-beverage') ? 'active' : '' }}">
          <a href="{{ route('services.food-beverage') }}" class="menu-link">
            <div>Food & Beverage</div>
          </a>
        </li>
        @endif
        @if(auth()->user()->role === 'admin')
        <li class="menu-item {{ request()->routeIs('services.counter-management') ? 'active' : '' }}">
          <a href="{{ route('services.counter-management') }}" class="menu-link">
            <div>Counter Management</div>
          </a>
        </li>
        <li class="menu-item {{ request()->routeIs('counters.tables.*') ? 'active' : '' }}">
          <a href="{{ route('counters.tables.index') }}" class="menu-link">
            <div>Table Management</div>
          </a>
        </li>
        @endif
        @if(auth()->user()->role !== 'counter')
        <li class="menu-item {{ request()->routeIs('golf-services.ball-collection.*') ? 'active' : '' }}">
          <a href="{{ route('golf-services.ball-collection.index') }}" class="menu-link">
            <div>Ball Collection</div>
          </a>
        </li>
        @endif
      </ul>
    </li>
    @endif

    @if(in_array(auth()->user()->role, ['admin', 'storekeeper', 'counter']))
    <li class="menu-header"><span class="menu-header-text">REPORTS & INVENTORY</span></li>
    
    @if(auth()->user()->role !== 'counter')
    <li class="menu-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
      <a href="{{ route('reports.index') }}" class="menu-link">
        <i class="menu-icon ri ri-pie-chart-2-line"></i>
        <div>Reports Overview</div>
      </a>
    </li>
    @endif

    <li class="menu-item {{ request()->routeIs('payments.transactions') ? 'active' : '' }}">
      <a href="{{ route('payments.transactions') }}" class="menu-link">
        <i class="menu-icon ri ri-history-line"></i>
        <div>{{ auth()->user()->role === 'counter' ? 'Sales Reports' : 'Transaction Logs' }}</div>
      </a>
    </li>

    @if(auth()->user()->role !== 'counter')
    <li class="menu-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
      <a href="{{ route('inventory.index') }}" class="menu-link">
        <i class="menu-icon ri ri-archive-line"></i>
        <div>Inventory Stock</div>
      </a>
    </li>
    @endif

    @if(!in_array(auth()->user()->role, ['storekeeper', 'counter']))
    <li class="menu-item {{ request()->routeIs('access-control.*') ? 'active' : '' }}">
      <a href="{{ route('access-control.entry-gates') }}" class="menu-link">
        <i class="menu-icon ri ri-door-open-line"></i>
        <div>Gate Access Logs</div>
      </a>
    </li>
    @endif
    @endif

    <!-- Account Settings -->
    <li class="menu-header"><span class="menu-header-text">USER SETTINGS</span></li>
    <li class="menu-item {{ request()->routeIs('profile') ? 'active' : '' }}">
      <a href="{{ route('profile') }}" class="menu-link">
        <i class="menu-icon ri ri-user-settings-line"></i>
        <div>Account Settings</div>
      </a>
    </li>

    <li class="menu-item">
      <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar">
        @csrf
        <a href="javascript:void(0);" class="menu-link text-danger" onclick="document.getElementById('logout-form-sidebar').submit();">
          <i class="menu-icon ri ri-logout-box-line text-danger"></i>
          <div>Logout</div>
        </a>
      </form>
    </li>
  </ul>
</aside>
