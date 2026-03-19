@extends('settings._layout-base')

@section('title', 'Entry Gates')
@section('description', 'Entry Gates Management - Golf Club Management System')

@section('content')
<style>
  /* Premium Command Center Styles */
  .hero-command-center {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border-radius: 1.5rem;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
  }

  .hero-command-center::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(148, 0, 0, 0.2) 0%, transparent 70%);
    z-index: 1;
  }

  .hero-badge-container {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    border-radius: 1rem;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
  }

  .hero-badge-container:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.05);
  }

  .hero-metric-value {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.25rem;
    background: linear-gradient(to bottom, #fff 0%, #ccc 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .hero-metric-label {
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.75rem;
    font-weight: 600;
    opacity: 0.7;
  }

  .pulse-success {
    box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    animation: pulse-green 2s infinite;
    border-radius: 50%;
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: #28a745;
  }

  @keyframes pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
  }

  .stat-card-vibrant {
    border: none;
    border-radius: 1.25rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  }

  .stat-card-vibrant:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  }

  .stat-icon-box {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1rem;
    margin-bottom: 1rem;
  }

  /* Enhanced Label Badges for Contrast */
  .bg-label-success { background-color: #e8fadf !important; color: #28a745 !important; }
  .bg-label-primary { background-color: rgba(67, 89, 113, 0.1) !important; color: #435971 !important; }
  .bg-label-info { background-color: #e0f2f1 !important; color: #00897b !important; }
  .bg-label-danger { background-color: #ffeef3 !important; color: #ff3e1d !important; }
  .bg-label-secondary { background-color: #ebedef !important; color: #8592a3 !important; }
  .bg-label-warning { background-color: #fff2e0 !important; color: #ff9f43 !important; }

  /* Custom Red override if needed */
  .btn-primary { background-color: #940000; border-color: #940000; }
  .btn-primary:hover { background-color: #7a0000; border-color: #7a0000; }
  
  .badge-premium {
    padding: 0.5rem 0.8rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    border-radius: 8px;
  }

  /* Member Search Dropdown Styling */
  .search-dropdown-container {
    background: #fff !important;
    border: 1px solid #d9dee3 !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    z-index: 2000 !important;
  }

  .search-dropdown-item {
    padding: 0.75rem 1rem !important;
    border-bottom: 1px solid #f0f2f4 !important;
    transition: background 0.2s ease !important;
  }

  .search-dropdown-item:last-child {
    border-bottom: none !important;
  }

  .search-dropdown-item:hover {
    background-color: #f8f9fa !important;
  }
</style>
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Access Control /</span> Entry Gates
</h4>

<!-- Premium Hero Section -->
<div class="row mb-4">
  <div class="col-12">
    <div class="hero-command-center p-4 p-md-5">
      <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-7 text-white">
          <div class="d-flex align-items-center mb-3">
            <span class="badge bg-danger px-3 py-2 rounded-pill me-3">
              <i class="icon-base ri ri-radar-line me-1"></i> LIVE MONITORING
            </span>
            <span class="text-white-50 small"><i class="icon-base ri ri-time-line me-1"></i>System Active: <span id="liveClock">--:--:--</span></span>
          </div>
          <h1 class="display-5 fw-bold mb-3 text-white">Access Control Command Center</h1>
          <p class="fs-5 opacity-75 mb-4 max-w-500">Monitor physical entry points, manage member access, and track real-time club entries with advanced security and analytics.</p>
          
          <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-danger btn-lg px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#addGateModal">
              <i class="icon-base ri ri-add-circle-line me-1"></i> Add New Access Point
            </button>
            <button class="btn btn-outline-light btn-lg px-4 rounded-pill" onclick="location.reload()">
              <i class="icon-base ri ri-refresh-line me-1"></i> Sync Status
            </button>
          </div>
        </div>
        
        <div class="col-lg-5 mt-4 mt-lg-0">
          <div class="row g-3">
            <div class="col-6">
              <div class="hero-badge-container text-center text-white">
                <div class="hero-metric-value">{{ $stats['currently_inside'] ?? 0 }}</div>
                <div class="hero-metric-label">Members Inside</div>
                <div class="mt-2 small opacity-50 text-white"><i class="icon-base ri ri-group-line me-1"></i>Peak occupancy today</div>
              </div>
            </div>
            <div class="col-6">
              <div class="hero-badge-container text-center text-white">
                <div class="hero-metric-value">{{ ($stats['today_entries'] ?? 0) + ($stats['today_exits'] ?? 0) }}</div>
                <div class="hero-metric-label">Total Traffic</div>
                <div class="mt-2 small opacity-50 text-white"><i class="icon-base ri ri-swap-line me-1"></i>Combined access today</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-5">
  <div class="col-md-3 col-6 mb-4">
    <div class="card stat-card-vibrant h-100">
      <div class="card-body">
        <div class="stat-icon-box bg-label-success">
          <i class="icon-base ri ri-door-open-line fs-3"></i>
        </div>
        <p class="mb-1 text-muted fw-medium">Active Points</p>
        <div class="d-flex align-items-center gap-2">
          <h3 class="mb-0 fw-bold">{{ $stats['active_gates'] ?? 0 }}</h3>
          <span class="pulse-success"></span>
        </div>
        <div class="mt-2 small text-success">
          <i class="icon-base ri ri-arrow-up-line me-1"></i>System fully operational
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card stat-card-vibrant h-100">
      <div class="card-body">
        <div class="stat-icon-box bg-label-primary">
          <i class="icon-base ri ri-login-box-line fs-3"></i>
        </div>
        <p class="mb-1 text-muted fw-medium">Entries Today</p>
        <h3 class="mb-0 fw-bold">{{ $stats['today_entries'] ?? 0 }}</h3>
        <div class="mt-2 small text-primary">
          <i class="icon-base ri ri-user-follow-line me-1"></i>Total successful entries
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card stat-card-vibrant h-100">
      <div class="card-body">
        <div class="stat-icon-box bg-label-info">
          <i class="icon-base ri ri-logout-box-line fs-3"></i>
        </div>
        <p class="mb-1 text-muted fw-medium">Exits Today</p>
        <h3 class="mb-0 fw-bold">{{ $stats['today_exits'] ?? 0 }}</h3>
        <div class="mt-2 small text-info">
          <i class="icon-base ri ri-user-shared-line me-1"></i>Total registered exits
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card stat-card-vibrant h-100">
      <div class="card-body">
        <div class="stat-icon-box bg-label-danger">
          <i class="icon-base ri ri-close-circle-line fs-3"></i>
        </div>
        <p class="mb-1 text-muted fw-medium">Access Denied</p>
        <h3 class="mb-0 fw-bold">{{ $stats['today_denied'] ?? 0 }}</h3>
        <div class="mt-2 small text-danger">
          <i class="icon-base ri ri-error-warning-line me-1"></i>Recent security alerts
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Advanced Analytics Section -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center border-bottom">
        <h5 class="mb-0 fw-bold"><i class="icon-base ri ri-bar-chart-2-line me-2 text-primary"></i>Operational Insights</h5>
        <div class="btn-group rounded-pill overflow-hidden border" role="group">
          <button type="button" class="btn btn-outline-primary active border-0" onclick="showAnalyticsTab('hourly')" id="analyticsTabHourly">
            Hourly
          </button>
          <button type="button" class="btn btn-outline-primary border-0 border-start" onclick="showAnalyticsTab('daily')" id="analyticsTabDaily">
            Daily
          </button>
          <button type="button" class="btn btn-outline-primary border-0 border-start" onclick="showAnalyticsTab('gates')" id="analyticsTabGates">
            Points
          </button>
        </div>
      </div>
      <div class="card-body">
        <!-- Hourly Statistics - Visual Bar Chart -->
        <div id="analyticsHourly" class="analytics-tab">
          @php
            $hourlyData = $analytics['hourly_entries'] ?? [];
            $maxEntries = max(array_values($hourlyData) ?: [1]);
            if ($maxEntries == 0) $maxEntries = 1;
          @endphp

          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <h6 class="mb-0 fw-bold">Today's Hourly Entry Pattern</h6>
              <small class="text-muted">Each bar shows how many members entered during that hour</small>
            </div>
            <div class="text-end">
              @if(isset($analytics['peak_hours']) && count($analytics['peak_hours']) > 0)
                <small class="text-muted">Peak Hour</small>
                <div class="fw-bold text-danger">{{ sprintf('%02d:00 – %02d:59', $analytics['peak_hours'][0], $analytics['peak_hours'][0]) }}</div>
              @endif
            </div>
          </div>

          {{-- Time period colour guide --}}
          <div class="d-flex gap-3 mb-3 flex-wrap">
            <span class="d-flex align-items-center gap-1"><span style="width:12px;height:12px;border-radius:3px;background:#6c757d;display:inline-block;"></span><small class="text-muted">Night (00–05)</small></span>
            <span class="d-flex align-items-center gap-1"><span style="width:12px;height:12px;border-radius:3px;background:#fd7e14;display:inline-block;"></span><small class="text-muted">Morning (06–11)</small></span>
            <span class="d-flex align-items-center gap-1"><span style="width:12px;height:12px;border-radius:3px;background:#0d6efd;display:inline-block;"></span><small class="text-muted">Afternoon (12–17)</small></span>
            <span class="d-flex align-items-center gap-1"><span style="width:12px;height:12px;border-radius:3px;background:#6f42c1;display:inline-block;"></span><small class="text-muted">Evening (18–23)</small></span>
          </div>

          <div class="row g-1">
            @for($i = 0; $i < 24; $i++)
              @php
                $count = $hourlyData[$i] ?? 0;
                $pct = $maxEntries > 0 ? round(($count / $maxEntries) * 100) : 0;
                $isPeak = isset($analytics['peak_hours']) && in_array($i, $analytics['peak_hours']);
                if ($i < 6)      { $color = '#6c757d'; $bg = '#f8f9fa'; }
                elseif ($i < 12) { $color = '#fd7e14'; $bg = '#fff8f3'; }
                elseif ($i < 18) { $color = '#0d6efd'; $bg = '#f0f5ff'; }
                else             { $color = '#6f42c1'; $bg = '#f8f3ff'; }
              @endphp
              <div class="col-6 col-md-3 col-lg-2">
                <div class="p-2 rounded-2" style="background: {{ $bg }}; border: {{ $isPeak ? '2px solid ' . $color : '1px solid rgba(0,0,0,0.06)' }};">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="fw-bold" style="color: {{ $color }}; font-size: 0.7rem;">{{ sprintf('%02d:00', $i) }}</small>
                    <small class="fw-bold {{ $count > 0 ? '' : 'text-muted' }}" style="{{ $count > 0 ? 'color: ' . $color : '' }}; font-size: 0.75rem;">{{ $count }}</small>
                  </div>
                  <div style="background: rgba(0,0,0,0.08); border-radius: 4px; height: 6px; overflow: hidden;">
                    <div style="width: {{ $pct }}%; height: 100%; background: {{ $color }}; border-radius: 4px; transition: width 0.5s ease;"></div>
                  </div>
                </div>
              </div>
            @endfor
          </div>

          @if(array_sum($hourlyData) === 0)
            <div class="text-center py-4 text-muted mt-3">
              <i class="icon-base ri ri-bar-chart-line d-block fs-2 mb-2"></i>
              No entries recorded yet today
            </div>
          @endif
        </div>

        <!-- Daily Statistics -->
        <div id="analyticsDaily" class="analytics-tab d-none">
          @php
            $dailyData = $analytics['daily_entries'] ?? [];
            $maxDaily = count($dailyData) > 0 ? max(array_values($dailyData)) : 1;
            if ($maxDaily == 0) $maxDaily = 1;
          @endphp
          <h6 class="mb-1 fw-bold">Last 7 Days Entry Trend</h6>
          <small class="text-muted d-block mb-4">How many members entered the club each day this week</small>
          @if(count($dailyData) > 0)
            <div class="d-flex flex-column gap-2">
              @foreach($dailyData as $date => $count)
                @php
                  $pct = $maxDaily > 0 ? round(($count / $maxDaily) * 100) : 0;
                  $parsed = \Carbon\Carbon::parse($date);
                  $isToday = $parsed->isToday();
                @endphp
                <div class="d-flex align-items-center gap-3">
                  <div style="width: 110px; flex-shrink: 0;">
                    <div class="fw-bold {{ $isToday ? 'text-primary' : 'text-dark' }}" style="font-size: 0.85rem;">{{ $parsed->format('D, d M') }}</div>
                    @if($isToday)<small class="badge bg-label-primary" style="font-size:0.65rem;">Today</small>@endif
                  </div>
                  <div class="flex-grow-1" style="background: #f0f2f4; border-radius: 6px; height: 22px; overflow: hidden;">
                    <div style="width: {{ $pct }}%; height: 100%; background: {{ $isToday ? '#0d6efd' : '#28a745' }}; border-radius: 6px; display: flex; align-items: center; justify-content: flex-end; padding-right: 8px; min-width: 30px; transition: width 0.5s;">
                      @if($count > 0)<small class="text-white fw-bold" style="font-size: 0.7rem;">{{ $count }}</small>@endif
                    </div>
                  </div>
                  <div style="width: 40px; text-align: right;">
                    <span class="fw-bold {{ $isToday ? 'text-primary' : 'text-dark' }}" style="font-size: 0.85rem;">{{ $count }}</span>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-4 text-muted">
              <i class="icon-base ri ri-calendar-line d-block fs-2 mb-2"></i>
              No daily data available yet
            </div>
          @endif
        </div>

        <!-- Gate Statistics -->
        <div id="analyticsGates" class="analytics-tab d-none">
          <h6 class="mb-1 fw-bold">Gate Performance Today</h6>
          <small class="text-muted d-block mb-4">Activity summary for each gate point</small>
          @if(count($analytics['gate_stats'] ?? []) > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Gate</th>
                    <th class="text-center text-success">Entries</th>
                    <th class="text-center text-info">Exits</th>
                    <th class="text-center text-danger">Denied</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($analytics['gate_stats'] ?? [] as $gate)
                  <tr>
                    <td>
                      <div class="fw-bold">{{ $gate->name }}</div>
                      <small class="text-muted">{{ $gate->location ?? 'No location set' }}</small>
                    </td>
                    <td class="text-center"><span class="badge bg-label-success px-3">{{ $gate->today_entries ?? 0 }}</span></td>
                    <td class="text-center"><span class="badge bg-label-info px-3">{{ $gate->today_exits ?? 0 }}</span></td>
                    <td class="text-center"><span class="badge bg-label-danger px-3">{{ $gate->today_denied ?? 0 }}</span></td>
                    <td class="text-center">
                      <span class="badge bg-{{ $gate->is_active && $gate->status === 'online' ? 'success' : 'secondary' }}">
                        {{ $gate->is_active ? ucfirst($gate->status ?? 'offline') : 'Disabled' }}
                      </span>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-4 text-muted">
              <i class="icon-base ri ri-router-line d-block fs-2 mb-2"></i>
              No gate data available
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Advanced Filtering & Currently Inside Members -->
<div class="row mb-4">
  <!-- Advanced Filters -->
  <div class="col-lg-6">
    <div class="card stat-card-vibrant h-100">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold"><i class="icon-base ri ri-filter-3-line me-2 text-warning"></i>Search & Drill-down</h5>
      </div>
      <div class="card-body">
        <form id="advancedFiltersForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Date From</label>
              <input type="date" class="form-control" id="filterDateFrom" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Date To</label>
              <input type="date" class="form-control" id="filterDateTo" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Gate</label>
              <select class="form-select" id="filterGate">
                <option value="">All Gates</option>
                @foreach($gates as $gate)
                <option value="{{ $gate->id }}">{{ $gate->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Access Type</label>
              <select class="form-select" id="filterAccessType">
                <option value="">All Types</option>
                <option value="entry">Entry</option>
                <option value="exit">Exit</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select" id="filterStatus">
                <option value="">All Status</option>
                <option value="success">Success</option>
                <option value="denied">Denied</option>
                <option value="pending">Pending</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Search</label>
              <input type="text" class="form-control" id="filterSearch" placeholder="Card number, name...">
            </div>
            <div class="col-12 mt-4">
              <div class="btn-group w-100">
                <button type="button" class="btn btn-primary px-4" style="flex: 5;" onclick="applyAdvancedFilters()">
                  <i class="icon-base ri ri-search-eye-line me-1"></i> APPLY SEARCH FILTERS
                </button>
                <button type="button" class="btn btn-outline-secondary px-3" style="flex: 1;" onclick="resetAdvancedFilters()">
                  <i class="icon-base ri ri-refresh-line"></i>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Currently Inside Members -->
  <div class="col-lg-6">
    <div class="card stat-card-vibrant h-100">
      <div class="card-header d-flex justify-content-between align-items-center border-bottom">
        <h5 class="mb-0 fw-bold"><i class="icon-base ri ri-user-location-line me-2 text-primary"></i>Currently Active Inside</h5>
        <span class="badge bg-primary rounded-pill">{{ count($analytics['inside_members'] ?? []) }}</span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light sticky-top">
              <tr>
                <th class="ps-4 py-3">MEMBER</th>
                <th class="py-3">CARD</th>
                <th class="py-3">ENTRY</th>
                <th class="text-end pe-4 py-3">ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              @forelse($analytics['inside_members'] ?? [] as $log)
              <tr>
                <td class="ps-4">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ substr($log->member_name, 0, 1) }}
                      </span>
                    </div>
                    <div>
                      <div class="fw-bold text-dark">{{ $log->member_name }}</div>
                      @if($log->member)
                        <small class="text-muted">#{{ $log->member->member_id }}</small>
                      @endif
                    </div>
                  </div>
                </td>
                <td><code class="text-primary fw-medium">{{ $log->card_number }}</code></td>
                <td>
                  <div class="fw-semibold text-dark">{{ $log->created_at->format('H:i') }}</div>
                  <small class="text-muted">{{ $log->gate->name ?? 'Point A' }}</small>
                </td>
                <td class="text-end pe-4">
                  <div class="d-flex align-items-center justify-content-end gap-2">
                    {{-- Quick Exit Button --}}
                    <button
                      class="btn btn-danger btn-sm rounded-pill px-3"
                      onclick="quickExit('{{ $log->card_number }}', '{{ $log->member_id }}', this)"
                      title="Record Exit"
                    >
                      <i class="icon-base ri ri-logout-box-r-line me-1"></i>Exit
                    </button>
                    @if($log->member_id)
                    <a href="{{ route('payments.members.show', $log->member_id) }}" class="btn btn-icon btn-label-primary btn-sm rounded-pill" title="View Profile">
                      <i class="icon-base ri ri-arrow-right-line"></i>
                    </a>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-5 text-muted">
                  <i class="icon-base ri ri-user-unfollow-line d-block fs-2 mb-2"></i>
                  No members currently inside
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

<div class="row">
  <!-- Gates Status -->
  <div class="col-lg-8">
    <!-- Gate Cards -->
    <div class="row mb-4" id="gatesContainer">
      @forelse($gates as $gate)
      <div class="col-md-6 mb-4">
        <div class="card stat-card-vibrant h-100 border-top border-4 border-{{ $gate->is_active && $gate->status === 'online' ? 'success' : 'secondary' }}">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="d-flex align-items-center">
                <div class="stat-icon-box bg-label-{{ $gate->is_active && $gate->status === 'online' ? 'success' : 'secondary' }} rounded-circle me-3">
                  <i class="icon-base ri ri-door-lock-line fs-4"></i>
                </div>
                <div>
                  <h5 class="mb-0 fw-bold">{{ $gate->name }}</h5>
                  <small class="text-muted">
                    <i class="icon-base ri ri-map-pin-2-line me-1"></i>{{ $gate->location ?? 'Global Entry' }}
                  </small>
                </div>
              </div>
              <div class="text-end">
                @if($gate->is_active && $gate->status === 'online')
                  <span class="badge bg-label-success rounded-pill px-3">
                    <span class="pulse-success me-1"></span> ONLINE
                  </span>
                @else
                  <span class="badge bg-label-secondary rounded-pill px-3">
                    OFFLINE
                  </span>
                @endif
                <div class="mt-1">
                  @php
                    $todayEntries = \App\Models\AccessLog::where('gate_id', $gate->id)
                      ->whereDate('created_at', today())
                      ->where('access_type', 'entry')
                      ->where('status', 'success')
                      ->count();
                  @endphp
                  <small class="text-muted fw-semibold">{{ $todayEntries }} entries today</small>
                </div>
              </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-4">
              <span class="badge badge-premium bg-label-{{ $gate->type === 'entry' ? 'success' : ($gate->type === 'exit' ? 'info' : 'primary') }} border-0">
                <i class="icon-base ri ri-{{ $gate->type === 'entry' ? 'login-circle' : ($gate->type === 'exit' ? 'logout-circle' : 'repeat-2') }}-line me-1"></i>
                {{ strtoupper($gate->type) }} GATE
              </span>
              @if($gate->requires_card)
                <span class="badge badge-premium bg-label-info border-0">
                  <i class="icon-base ri ri-vip-diamond-line me-1"></i>
                  PREMIUM ACCESS
                </span>
              @endif
              @if($gate->device_id)
                <span class="badge bg-label-secondary border-0 small">
                  <i class="icon-base ri ri-cpu-line me-1"></i>{{ $gate->device_id }}
                </span>
              @endif
            </div>

            <div class="d-flex gap-2">
              <button class="btn btn-primary flex-grow-1" onclick="editGate({{ $gate->id }})">
                <i class="icon-base ri ri-edit-2-line me-1"></i> Manage
              </button>
              <button class="btn btn-outline-{{ $gate->is_active ? 'danger' : 'success' }}" onclick="toggleGate({{ $gate->id }})">
                <i class="icon-base ri ri-{{ $gate->is_active ? 'stop-circle' : 'play-circle' }}-line me-1"></i>
                {{ $gate->is_active ? 'Shutdown' : 'Activate' }}
              </button>
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center py-5">
        <div class="bg-label-secondary rounded-circle d-inline-flex p-4 mb-3">
          <i class="icon-base ri ri-door-lock-line fs-1"></i>
        </div>
        <h5>No Access Points Configured</h5>
        <p class="text-muted">Initialize your first gate to start monitoring entries.</p>
        <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#addGateModal">
          <i class="icon-base ri ri-add-line me-1"></i> Configure Initial Gate
        </button>
      </div>
      @endforelse
    </div>
    
    <!-- Recent Access Logs -->
    <div class="card stat-card-vibrant mt-4">
      <div class="card-header d-flex justify-content-between align-items-center border-bottom bg-light bg-opacity-50">
        <h5 class="mb-0 fw-bold"><i class="icon-base ri ri-history-line me-2 text-danger"></i>Live Traffic Feed</h5>
        <div class="d-flex gap-2">
          <a href="{{ route('access-control.entry-gates.logs.export') }}" class="btn btn-sm btn-label-success px-3">
            <i class="icon-base ri ri-download-2-line me-1"></i>Export
          </a>
          <button class="btn btn-sm btn-label-primary px-3" onclick="viewAllLogs()">
            Full History
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light sticky-top">
              <tr>
                <th class="ps-4 py-3">TIME</th>
                <th class="py-3">IDENTIFIER</th>
                <th class="py-3">POINT</th>
                <th class="py-3">TYPE</th>
                <th class="py-3">SEC_STATUS</th>
                <th class="text-end pe-4 py-3">ACTION</th>
              </tr>
            </thead>
            <tbody id="accessLogsTable">
              @forelse($recentLogs as $log)
              <tr>
                <td class="ps-4">
                  <div class="fw-bold text-dark">{{ $log->created_at->format('H:i:s') }}</div>
                  <small class="text-muted">{{ $log->created_at->format('d M') }}</small>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <span class="avatar-initial rounded-circle bg-label-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                        <i class="icon-base ri ri-{{ $log->status === 'success' ? 'shield-check' : 'shield-keyhole' }}-line"></i>
                      </span>
                    </div>
                    <div>
                      <div class="fw-bold text-dark">{{ $log->member_name }}</div>
                      <code class="text-primary small">{{ $log->card_number }}</code>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-label-secondary border-0 fw-medium">{{ $log->gate->name ?? 'System' }}</span>
                </td>
                <td>
                  <span class="badge bg-{{ $log->access_type === 'entry' ? 'success' : 'info' }} bg-opacity-10 text-{{ $log->access_type === 'entry' ? 'success' : 'info' }} border-0 fw-bold">
                    {{ strtoupper($log->access_type) }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-{{ $log->status === 'success' ? 'success' : 'danger' }} px-3 rounded-pill">
                    {{ strtoupper($log->status) }}
                  </span>
                  @if($log->denial_reason)
                    <div class="text-danger x-small mt-1 fw-medium" style="font-size: 0.65rem;">{{ Str::limit($log->denial_reason, 35) }}</div>
                  @endif
                </td>
                <td class="text-end pe-4">
                  @if($log->member_id)
                    <a href="{{ route('payments.members.show', $log->member_id) }}" class="btn btn-icon btn-label-primary btn-sm rounded-pill">
                      <i class="icon-base ri ri-user-search-line"></i>
                    </a>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">No access activity recorded</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Quick Scan & Actions -->
  <div class="col-lg-4">
    <!-- Manual Card Scan — redesigned step-by-step flow -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <div>
          <h5 class="mb-0 text-white"><i class="icon-base ri ri-qr-scan-2-line me-2"></i>Quick Card Scan</h5>
          <small class="text-white opacity-75">Verify & log member access</small>
        </div>
        <span class="badge bg-white text-danger fw-bold px-2 py-1" id="scanStatus">READY</span>
      </div>
      <div class="card-body p-3">

        {{-- STEP 1: Member Search --}}
        <div class="mb-3">
          <label class="form-label fw-bold text-muted small mb-1">
            <span class="badge bg-danger rounded-pill me-1">1</span> MEMBER
          </label>
          <div class="position-relative">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="icon-base ri ri-search-line text-muted"></i></span>
              <input type="text" class="form-control border-start-0 ps-0" id="cardNumber"
                placeholder="Name, card number, or ID…"
                autocomplete="off"
                onkeypress="if(event.key==='Enter') scanCard()">
              <button class="btn btn-outline-secondary" type="button" id="clearMemberBtn" onclick="clearMemberSelection()" title="Clear" style="display:none;">
                <i class="icon-base ri ri-close-line"></i>
              </button>
            </div>
            <input type="hidden" id="selectedMemberId" value="">
            <div id="memberDropdown" class="list-group position-absolute w-100 search-dropdown-container shadow" style="max-height: 240px; overflow-y: auto; display: none; margin-top: 2px; z-index: 999;"></div>
          </div>
          {{-- Member Quick Preview --}}
          <div id="memberPreview" class="mt-2 p-2 rounded-2 border d-none" style="background: #f8f9fa;">
            <div class="d-flex align-items-center gap-2">
              <div class="avatar avatar-sm flex-shrink-0">
                <span class="avatar-initial rounded-circle bg-label-primary fw-bold" id="previewAvatar">?</span>
              </div>
              <div class="flex-grow-1 overflow-hidden">
                <div class="fw-bold text-dark text-truncate" id="previewName">-</div>
                <div class="d-flex gap-2 flex-wrap">
                  <small class="text-muted" id="previewCard">-</small>
                  <small class="fw-bold text-success" id="previewBalance">-</small>
                </div>
              </div>
              <span class="badge" id="previewStatus">-</span>
            </div>
          </div>
        </div>

        {{-- STEP 2: Gate + Type --}}
        <div class="mb-3">
          <label class="form-label fw-bold text-muted small mb-1">
            <span class="badge bg-danger rounded-pill me-1">2</span> GATE & DIRECTION
          </label>
          <div class="row g-2">
            <div class="col-7">
              <select class="form-select" id="scanGate">
                <option value="">— Select Gate —</option>
                @foreach($gates->where('is_active', true) as $gate)
                <option value="{{ $gate->id }}" data-type="{{ $gate->type }}">{{ $gate->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-5">
              <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="scanType" id="scanEntry" value="entry" checked>
                <label class="btn btn-outline-success btn-sm fw-bold" for="scanEntry">
                  <i class="icon-base ri ri-login-box-line"></i> IN
                </label>
                <input type="radio" class="btn-check" name="scanType" id="scanExit" value="exit">
                <label class="btn btn-outline-info btn-sm fw-bold" for="scanExit">
                  OUT <i class="icon-base ri ri-logout-box-line"></i>
                </label>
              </div>
            </div>
          </div>
        </div>

        {{-- STEP 3: Actions --}}
        <div class="mb-3">
          <label class="form-label fw-bold text-muted small mb-1">
            <span class="badge bg-danger rounded-pill me-1">3</span> ACTION
          </label>
          <div class="row g-2">
            <div class="col-12">
              <button class="btn btn-danger btn-lg w-100 fw-bold" onclick="scanCard()" id="scanBtn">
                <i class="icon-base ri ri-qr-scan-2-line me-2"></i>LOG ENTRY / EXIT
              </button>
            </div>
            <div class="col-6">
              <button class="btn btn-outline-success w-100" onclick="getEnter()" title="Retrieve member info without logging">
                <i class="icon-base ri ri-eye-line me-1"></i>Check Info
              </button>
            </div>
            <div class="col-6">
              <button class="btn btn-outline-primary w-100" onclick="showBalance()" title="View wallet balance">
                <i class="icon-base ri ri-wallet-3-line me-1"></i>Balance
              </button>
            </div>
          </div>
        </div>

        <!-- Scan Result -->
        <div id="scanResult" class="d-none">
          <div class="rounded-3 p-3 mt-1" id="scanResultBox" style="border: 2px solid #dee2e6;">
            <div class="d-flex align-items-center gap-3 mb-2">
              <div class="avatar flex-shrink-0">
                <span class="avatar-initial rounded-circle" id="scanResultIcon" style="width:48px;height:48px;font-size:20px;">
                  <i class="icon-base ri ri-check-line"></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold fs-6" id="scanResultName">-</div>
                <div class="d-flex gap-1 flex-wrap mt-1">
                  <span class="badge bg-label-primary small" id="scanResultCardNumber">-</span>
                  <span class="badge bg-label-info small" id="scanResultMemberId">-</span>
                  <span class="badge bg-label-success small" id="scanResultType">-</span>
                </div>
              </div>
            </div>
            <div id="scanResultMessage" class="small mb-0"></div>
            <div id="scanResultDetails" class="border-top mt-2 pt-2 d-none">
              <div class="row g-1 text-center">
                <div class="col-6">
                  <div class="small text-muted">Balance</div>
                  <div class="fw-bold text-success small" id="scanResultBalance">-</div>
                </div>
                <div class="col-6">
                  <div class="small text-muted">Status</div>
                  <span class="badge" id="scanResultStatus">-</span>
                </div>
                <div class="col-6">
                  <div class="small text-muted">Phone</div>
                  <div class="small" id="scanResultPhone">-</div>
                </div>
                <div class="col-6">
                  <div class="small text-muted">Valid Until</div>
                  <div class="small" id="scanResultValidUntil">-</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>



    <!-- Gate Controls -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-remote-control-line me-2"></i>Gate Controls</h5>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
          @php
            $config = \App\Models\AccessControlConfig::getConfig();
            $mode = $config->global_mode ?? 'normal';
          @endphp

          @if($mode !== 'normal')
            <div class="alert alert-{{ $mode === 'emergency' ? 'danger' : ($mode === 'open' ? 'success' : 'dark') }} mb-2 py-2 text-center fw-bold">
              @if($mode === 'emergency')
                <i class="ri-alarm-warning-fill me-1"></i> EMERGENCY OVERRIDE ACTIVE
              @elseif($mode === 'open')
                <i class="ri-door-open-fill me-1"></i> GATES OPEN OVERRIDE ACTIVE
              @elseif($mode === 'locked')
                <i class="ri-lock-fill me-1"></i> SYSTEM LOCKDOWN ACTIVE
              @endif
            </div>
            
            <button class="btn btn-outline-primary mb-2" onclick="resetToNormal()">
              <i class="ri-refresh-line me-1"></i> Return to Normal Mode
            </button>
          @endif

          <button class="btn btn-success" onclick="openAllGates()">
            <i class="icon-base ri ri-door-open-line me-1"></i>Open All Gates
          </button>
          <button class="btn btn-danger" onclick="closeAllGates()">
            <i class="icon-base ri ri-door-lock-line me-1"></i>Lock All Gates
          </button>
          <button class="btn btn-warning" onclick="emergencyMode()">
            <i class="icon-base ri ri-alarm-warning-line me-1"></i>Emergency Mode
          </button>
        </div>
      </div>
    </div>
    
    <!-- Access Rules Info -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="icon-base ri ri-shield-check-line me-2"></i>Access Rules</h5>
        <a href="{{ route('settings.configuration') }}#access-control" class="btn btn-sm btn-outline-primary">
          <i class="icon-base ri ri-settings-3-line me-1"></i>Configure
        </a>
      </div>
      <div class="card-body">
        <p class="text-muted mb-0">
          <i class="icon-base ri ri-information-line me-1"></i>
          Access rules are configured in <a href="{{ route('settings.configuration') }}#access-control">System Configuration</a> page.
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Add Gate Modal -->
<div class="modal fade" id="addGateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-add-line me-2"></i>Add New Gate</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addGateForm" onsubmit="saveGate(event)">
        <div class="modal-body">
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="gate_name" name="name" placeholder="Gate Name" required>
              <label for="gate_name">Gate Name *</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <select class="form-select" id="gate_type" name="type" required>
                <option value="entry">Entry Only</option>
                <option value="exit">Exit Only</option>
                <option value="both">Entry & Exit</option>
              </select>
              <label for="gate_type">Gate Type *</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="gate_location" name="location" placeholder="Location">
              <label for="gate_location">Location</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="gate_device_id" name="device_id" placeholder="Device ID">
              <label for="gate_device_id">Device ID (for hardware integration)</label>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <div class="form-check form-switch px-0">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="gate_active" name="is_active" checked>
                <label class="form-check-label fw-bold" for="gate_active">Active</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-check form-switch px-0">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="gate_requires_card" name="requires_card">
                <label class="form-check-label fw-bold" for="gate_requires_card">Requires Card</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-save-line me-1"></i>Save Gate
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Gate Modal -->
<div class="modal fade" id="editGateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-edit-line me-2"></i>Edit Gate</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editGateForm" onsubmit="updateGate(event)">
        <input type="hidden" id="edit_gate_id" name="id">
        <div class="modal-body">
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="edit_gate_name" name="name" placeholder="Gate Name" required>
              <label for="edit_gate_name">Gate Name *</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <select class="form-select" id="edit_gate_type" name="type" required>
                <option value="entry">Entry Only</option>
                <option value="exit">Exit Only</option>
                <option value="both">Entry & Exit</option>
              </select>
              <label for="edit_gate_type">Gate Type *</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="edit_gate_location" name="location" placeholder="Location">
              <label for="edit_gate_location">Location</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="edit_gate_device_id" name="device_id" placeholder="Device ID">
              <label for="edit_gate_device_id">Device ID</label>
            </div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <div class="form-check form-switch px-0">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="edit_gate_active" name="is_active">
                <label class="form-check-label fw-bold" for="edit_gate_active">Active</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-check form-switch px-0">
                <input class="form-check-input ms-0 me-2" type="checkbox" id="edit_requires_card" name="requires_card">
                <label class="form-check-label fw-bold" for="edit_requires_card">Requires Card</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-save-line me-1"></i>Update Gate
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Quick Card Scan Modal -->
<div class="modal fade" id="quickScanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white">
          <i class="icon-base ri ri-qr-scan-2-line me-2"></i>Quick Card Scan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-bold">Card Number, Member ID, or Name</label>
          <div class="position-relative">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control form-control-lg" id="modalCardNumber" placeholder="Start typing to search..." autocomplete="off" autofocus>
              <label for="modalCardNumber">Start typing to search...</label>
            </div>
            <input type="hidden" id="modalSelectedMemberId" value="">
            <div id="modalMemberDropdown" class="list-group position-absolute w-100 search-dropdown-container" style="max-height: 250px; overflow-y: auto; display: none; margin-top: 2px;">
              <!-- Dropdown items will be populated here -->
            </div>
          </div>
          <small class="text-muted">Type card number, member ID, or name to search</small>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Select Gate</label>
          <div class="form-floating form-floating-outline">
            <select class="form-select" id="modalScanGate">
              <option value="">Select Gate</option>
              @foreach($gates->where('is_active', true) as $gate)
              <option value="{{ $gate->id }}">{{ $gate->name }}</option>
              @endforeach
            </select>
            <label for="modalScanGate">Gate</label>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Access Type</label>
          <div class="btn-group w-100" role="group">
            <input type="radio" class="btn-check" name="modalScanType" id="modalScanEntry" value="entry" checked>
            <label class="btn btn-outline-success" for="modalScanEntry">
              <i class="icon-base ri ri-login-box-line me-1"></i>Entry
            </label>
            <input type="radio" class="btn-check" name="modalScanType" id="modalScanExit" value="exit">
            <label class="btn btn-outline-info" for="modalScanExit">
              <i class="icon-base ri ri-logout-box-line me-1"></i>Exit
            </label>
          </div>
        </div>
        
        <!-- Enhanced Scan Result -->
        <div id="modalScanResult" class="mt-3 d-none">
          <div class="card border-0 shadow-sm" id="modalScanResultCard">
            <div class="card-body">
              <div class="d-flex align-items-start mb-3">
                <div class="avatar avatar-lg me-3">
                  <span class="avatar-initial rounded-circle" id="modalScanResultIcon" style="width: 60px; height: 60px; font-size: 24px;">
                    <i class="icon-base ri ri-check-line"></i>
                  </span>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1 fw-bold" id="modalScanResultName">-</h5>
                  <div class="mb-2">
                    <span class="badge bg-label-primary me-2" id="modalScanResultCardNumber">-</span>
                    <span class="badge bg-label-info me-2" id="modalScanResultMemberId">-</span>
                    <span class="badge bg-label-success" id="modalScanResultType">-</span>
                  </div>
                  <div id="modalScanResultMessage" class="text-muted small">-</div>
                </div>
              </div>
              
              <!-- Detailed Member Information -->
              <div id="modalScanResultDetails" class="mt-3 pt-3 border-top d-none">
                <div class="row g-2">
                  <div class="col-6">
                    <small class="text-muted d-block">Balance</small>
                    <strong class="text-success" id="modalScanResultBalance">-</strong>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge" id="modalScanResultStatus">-</span>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block">Phone</small>
                    <span id="modalScanResultPhone">-</span>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block">Valid Until</small>
                    <span id="modalScanResultValidUntil">-</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close (Esc)</button>
        <button type="button" class="btn btn-primary" onclick="scanCardFromModal()">
          <i class="icon-base ri ri-qr-scan-line me-1"></i>Process Scan
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const gates = @json($gates);
let currentGateId = null;

document.addEventListener('DOMContentLoaded', function() {
  // Auto-refresh every 2 minutes
  setInterval(refreshLogs, 120000);
  
  // Member search autocomplete
  let memberSearchTimeout;
  const modalCardInput = document.getElementById('modalCardNumber');
  const modalMemberDropdown = document.getElementById('modalMemberDropdown');
  const modalSelectedMemberId = document.getElementById('modalSelectedMemberId');
  
  if (modalCardInput) {
    modalCardInput.addEventListener('input', function(e) {
      const query = this.value.trim();
      clearTimeout(memberSearchTimeout);
      
      if (query.length < 1) {
        if (modalMemberDropdown) modalMemberDropdown.style.display = 'none';
        if (modalSelectedMemberId) modalSelectedMemberId.value = '';
        return;
      }
      
      memberSearchTimeout = setTimeout(function() {
        fetch('{{ route("access-control.entry-gates.members.search") }}?q=' + encodeURIComponent(query), {
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        })
        .then(r => r.json())
        .then(members => {
          if (!modalMemberDropdown) return;
          if (members && members.length > 0) {
            modalMemberDropdown.innerHTML = members.map(member => {
              const statusBadge = member.status === 'active' ? 'success' : 'warning';
              const balance = parseFloat(member.balance || 0).toLocaleString();
              return `
                <a href="#" class="list-group-item list-group-item-action search-dropdown-item" data-member-id="${member.id}" data-member-name="${member.name}" data-card-number="${member.card_number}" data-member-id-value="${member.member_id}" data-member-balance="${member.balance || 0}" data-member-phone="${member.phone || ''}" data-member-type="${member.membership_type || 'standard'}">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-dark">${member.name}</strong>
                    <span class="badge bg-label-${statusBadge} small">${member.status}</span>
                  </div>
                  <div class="d-flex justify-content-between align-items-center small">
                    <span class="text-muted"><i class="icon-base ri ri-credit-card-line me-1"></i>${member.card_number}</span>
                    <span class="text-success fw-bold">TZS ${balance}</span>
                  </div>
                </a>
              `;
            }).join('');
            modalMemberDropdown.style.display = 'block';
            
            // Attach click handlers
            modalMemberDropdown.querySelectorAll('a').forEach(item => {
              item.addEventListener('click', function(e) {
                e.preventDefault();
                const memberId = this.getAttribute('data-member-id');
                const memberName = this.getAttribute('data-member-name');
                const cardNumber = this.getAttribute('data-card-number');
                const memberIdValue = this.getAttribute('data-member-id-value');
                
                modalSelectedMemberId.value = memberId;
                const memberBalance = this.getAttribute('data-member-balance') || '0';
                const memberPhone = this.getAttribute('data-member-phone') || '';
                const memberType = this.getAttribute('data-member-type') || 'standard';
                
                modalCardInput.value = memberName + ' (' + cardNumber + ')';
                modalCardInput.setAttribute('data-card-number', cardNumber);
                modalCardInput.setAttribute('data-member-name', memberName);
                modalCardInput.setAttribute('data-member-balance', memberBalance);
                modalCardInput.setAttribute('data-member-phone', memberPhone);
                modalCardInput.setAttribute('data-member-type', memberType);
                modalMemberDropdown.style.display = 'none';
              });
            });
          } else {
            modalMemberDropdown.innerHTML = '<div class="list-group-item text-muted">No members found</div>';
            modalMemberDropdown.style.display = 'block';
          }
        })
        .catch(err => {
          console.error('Search error:', err);
        });
      }, 300);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (modalCardInput && modalMemberDropdown) {
        if (!modalCardInput.contains(e.target) && !modalMemberDropdown.contains(e.target)) {
          modalMemberDropdown.style.display = 'none';
        }
      }
    });
  }
  
  // Global keyboard listener to open Quick Scan modal when typing starts
  let typingTimeout;
  let capturedKeys = '';
  
  document.addEventListener('keydown', function(e) {
    // Don't trigger if user is already in an input, textarea, or select
    const activeElement = document.activeElement;
    if (activeElement && (
      activeElement.tagName === 'INPUT' || 
      activeElement.tagName === 'TEXTAREA' || 
      activeElement.tagName === 'SELECT' ||
      activeElement.isContentEditable
    )) {
      // If modal is open and user is typing in modal input, don't capture
      if (activeElement.id === 'modalCardNumber') {
        return;
      }
      // If user is in any other input, don't capture
      return;
    }
    
    // Don't capture special keys like Escape, Tab, Enter, etc.
    if (['Escape', 'Tab', 'Enter', 'F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'F7', 'F8', 'F9', 'F10', 'F11', 'F12'].includes(e.key)) {
      return;
    }
    
    // Don't capture if Ctrl, Alt, or Meta keys are pressed (shortcuts)
    if (e.ctrlKey || e.altKey || e.metaKey) {
      return;
    }
    
    // Capture alphanumeric characters and digits
    if (e.key.length === 1 && /[a-zA-Z0-9]/.test(e.key)) {
      e.preventDefault();
      
      // If modal is not open, open it
      const modal = bootstrap.Modal.getInstance(document.getElementById('quickScanModal'));
      if (!modal || !modal._isShown) {
        const quickScanModal = new bootstrap.Modal(document.getElementById('quickScanModal'));
        quickScanModal.show();
        
        // Focus on input after modal is shown
        setTimeout(() => {
          const modalInput = document.getElementById('modalCardNumber');
          if (modalInput) {
            modalInput.focus();
            modalInput.value = e.key; // Set the first character
          }
        }, 300);
      } else {
        // Modal is already open, just append the key
        const modalInput = document.getElementById('modalCardNumber');
        if (modalInput) {
          modalInput.value += e.key;
          modalInput.focus();
        }
      }
    }
  });
  
  // Sidebar member search autocomplete
  const cardInput = document.getElementById('cardNumber');
  const memberDropdown = document.getElementById('memberDropdown');
  const selectedMemberId = document.getElementById('selectedMemberId');
  
  if (cardInput) {
    cardInput.addEventListener('input', function(e) {
      const query = this.value.trim();
      clearTimeout(memberSearchTimeout);
      
      if (query.length < 1) {
        if (memberDropdown) memberDropdown.style.display = 'none';
        if (selectedMemberId) selectedMemberId.value = '';
        return;
      }
      
      memberSearchTimeout = setTimeout(function() {
        fetch('{{ route("access-control.entry-gates.members.search") }}?q=' + encodeURIComponent(query), {
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        })
        .then(r => r.json())
        .then(members => {
          if (!memberDropdown) return;
          if (members && members.length > 0) {
            memberDropdown.innerHTML = members.map(member => {
              const statusBadge = member.status === 'active' ? 'success' : 'warning';
              const balance = parseFloat(member.balance || 0).toLocaleString();
              return `
                <a href="#" class="list-group-item list-group-item-action search-dropdown-item" data-member-id="${member.id}" data-member-name="${member.name}" data-card-number="${member.card_number}" data-member-id-value="${member.member_id}" data-member-balance="${member.balance || 0}" data-member-phone="${member.phone || ''}" data-member-type="${member.membership_type || 'standard'}">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-dark">${member.name}</strong>
                    <span class="badge bg-label-${statusBadge} small">${member.status}</span>
                  </div>
                  <div class="d-flex justify-content-between align-items-center small">
                    <span class="text-muted"><i class="icon-base ri ri-credit-card-line me-1"></i>${member.card_number}</span>
                    <span class="text-success fw-bold">TZS ${balance}</span>
                  </div>
                </a>
              `;
            }).join('');
            memberDropdown.style.display = 'block';
            
            // Attach click handlers
            memberDropdown.querySelectorAll('a').forEach(item => {
              item.addEventListener('click', function(e) {
                e.preventDefault();
                const memberId = this.getAttribute('data-member-id');
                const memberName = this.getAttribute('data-member-name');
                const cardNumber = this.getAttribute('data-card-number');
                const memberIdValue = this.getAttribute('data-member-id-value');
                
                selectedMemberId.value = memberId;
                const memberBalance = this.getAttribute('data-member-balance') || '0';
                const memberPhone = this.getAttribute('data-member-phone') || '';
                const memberType = this.getAttribute('data-member-type') || 'standard';
                
                cardInput.value = memberName + ' (' + cardNumber + ')';
                cardInput.setAttribute('data-card-number', cardNumber);
                cardInput.setAttribute('data-member-name', memberName);
                cardInput.setAttribute('data-member-balance', memberBalance);
                cardInput.setAttribute('data-member-phone', memberPhone);
                cardInput.setAttribute('data-member-type', memberType);
                memberDropdown.style.display = 'none';

                // Show member preview
                showMemberPreview(memberName, cardNumber, memberBalance, member.status || 'active');
              });
            });
          } else {
            memberDropdown.innerHTML = '<div class="list-group-item text-muted">No members found</div>';
            memberDropdown.style.display = 'block';
          }
        })
        .catch(err => {
          console.error('Search error:', err);
        });
      }, 300);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (cardInput && memberDropdown) {
        if (!cardInput.contains(e.target) && !memberDropdown.contains(e.target)) {
          memberDropdown.style.display = 'none';
        }
      }
    });

    // Clear member preview when user types again
    cardInput.addEventListener('input', function() {
      if (!this.value.trim()) {
        clearMemberSelection();
      } else {
        // Hide preview when user modifies the input
        document.getElementById('memberPreview')?.classList.add('d-none');
        document.getElementById('clearMemberBtn').style.display = 'none';
        this.removeAttribute('data-card-number');
        document.getElementById('selectedMemberId').value = '';
      }
    });
  }

  // Auto-select gate if only one active gate exists
  const scanGate = document.getElementById('scanGate');
  if (scanGate) {
    const options = scanGate.querySelectorAll('option[value]:not([value=""])');
    if (options.length === 1) {
      scanGate.value = options[0].value;
    }
  }
  
  // Handle Enter key in modal card input - attach after modal is shown
  document.getElementById('quickScanModal')?.addEventListener('shown.bs.modal', function() {
    const modalInput = document.getElementById('modalCardNumber');
    if (modalInput) {
      modalInput.focus();
      modalInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          scanCardFromModal();
        }
      }, { once: false });
    }
  });
});

function refreshLogs() {
  location.reload();
}

// Show member preview card in the Quick Scan panel
function showMemberPreview(name, cardNumber, balance, status) {
  const preview = document.getElementById('memberPreview');
  if (!preview) return;
  const isActive = status === 'active';
  document.getElementById('previewAvatar').textContent = name ? name.charAt(0).toUpperCase() : '?';
  document.getElementById('previewName').textContent = name || '-';
  document.getElementById('previewCard').textContent = cardNumber || '-';
  document.getElementById('previewBalance').textContent = 'TZS ' + parseFloat(balance || 0).toLocaleString();
  const statusEl = document.getElementById('previewStatus');
  statusEl.textContent = status || 'unknown';
  statusEl.className = 'badge bg-' + (isActive ? 'success' : 'warning');
  preview.classList.remove('d-none');
  const clearBtn = document.getElementById('clearMemberBtn');
  if (clearBtn) clearBtn.style.display = 'block';
}

// Clear member selection from the scan panel
function clearMemberSelection() {
  const cardInput = document.getElementById('cardNumber');
  const selectedMemberId = document.getElementById('selectedMemberId');
  const preview = document.getElementById('memberPreview');
  const clearBtn = document.getElementById('clearMemberBtn');
  if (cardInput) { cardInput.value = ''; cardInput.removeAttribute('data-card-number'); }
  if (selectedMemberId) selectedMemberId.value = '';
  if (preview) preview.classList.add('d-none');
  if (clearBtn) clearBtn.style.display = 'none';
  // Also hide scan result
  const scanResult = document.getElementById('scanResult');
  if (scanResult) scanResult.classList.add('d-none');
  const scanStatus = document.getElementById('scanStatus');
  if (scanStatus) { scanStatus.textContent = 'READY'; scanStatus.className = 'badge bg-white text-danger fw-bold px-2 py-1'; }
}

function quickExit(cardNumber, memberId, btn) {
  // Disable button immediately to prevent double-click
  btn.disabled = true;
  btn.innerHTML = '<i class="icon-base ri ri-loader-line me-1"></i>Exiting…';

  // Use the first gate available, or fallback to gate 1
  const gateId = {{ $gates->where('is_active', true)->first()?->id ?? 'null' }};

  if (!gateId) {
    btn.disabled = false;
    btn.innerHTML = '<i class="icon-base ri ri-logout-box-r-line me-1"></i>Exit';
    showError('No active gate found. Please configure a gate first.');
    return;
  }

  fetch('{{ route("access-control.entry-gates.scan") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      card_number: cardNumber,
      gate_id: gateId,
      access_type: 'exit',
      member_id: memberId || null
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Mark the row as exited visually
      const row = btn.closest('tr');
      row.style.background = '#d4edda';
      row.style.transition = 'background 0.3s';
      btn.innerHTML = '<i class="icon-base ri ri-check-line me-1"></i>Exited';
      btn.className = 'btn btn-success btn-sm rounded-pill px-3';
      // Reload after short delay to update counts
      setTimeout(() => location.reload(), 1500);
    } else {
      btn.disabled = false;
      btn.innerHTML = '<i class="icon-base ri ri-logout-box-r-line me-1"></i>Exit';
      showError('Exit failed: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(err => {
    console.error('Quick exit error:', err);
    btn.disabled = false;
    btn.innerHTML = '<i class="icon-base ri ri-logout-box-r-line me-1"></i>Exit';
    showError('Network error. Please try again.');
  });
}

function scanCard() {
  const selectedMemberIdEl = document.getElementById('selectedMemberId');
  const selectedMemberId = selectedMemberIdEl ? selectedMemberIdEl.value : null;
  const cardInput = document.getElementById('cardNumber');
  if (!cardInput) {
    showError('Card input not found');
    return;
  }
  const cardNumber = cardInput.getAttribute('data-card-number') || cardInput.value.trim();
  const scanGateEl = document.getElementById('scanGate');
  const gateId = scanGateEl ? scanGateEl.value : null;
  const scanTypeEl = document.querySelector('input[name="scanType"]:checked');
  const scanType = scanTypeEl ? scanTypeEl.value : 'entry';
  
  if (!cardNumber) {
    showWarning('Please search and select a member from the dropdown');
    return;
  }
  
  if (!selectedMemberId) {
    showWarning('Please select a member from the dropdown list. No member was selected.');
    return;
  }
  
  if (!gateId) {
    showWarning('Please select a gate');
    return;
  }
  
  const resultDiv = document.getElementById('scanResult');
  const resultBox  = document.getElementById('scanResultBox');
  const resultIcon = document.getElementById('scanResultIcon');
  const resultName = document.getElementById('scanResultName');
  const resultMessage = document.getElementById('scanResultMessage');
  const scanStatus = document.getElementById('scanStatus');
  
  // Show loading state
  resultDiv.classList.remove('d-none');
  if (resultBox) { resultBox.style.borderColor = '#17a2b8'; resultBox.style.background = '#f0faff'; }
  resultIcon.className = 'avatar-initial rounded-circle bg-info';
  resultIcon.style.backgroundColor = '#17a2b8';
  resultIcon.innerHTML = '<i class="icon-base ri ri-loader-line"></i>';
  resultName.textContent = 'Processing...';
  resultMessage.textContent = 'Scanning card...';
  if (scanStatus) { scanStatus.textContent = 'SCANNING'; scanStatus.className = 'badge bg-info text-white fw-bold px-2 py-1'; }
  
  fetch('{{ route("access-control.entry-gates.scan") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      card_number: cardNumber,
      gate_id: gateId,
      access_type: scanType,
      member_id: selectedMemberId
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const isSuccess = data.status === 'success';
      const color = isSuccess ? '#28a745' : '#dc3545';
      
      // Color the result box
      if (resultBox) { resultBox.style.borderColor = color; resultBox.style.background = isSuccess ? '#f0fff4' : '#fff5f5'; }
      resultIcon.className = `avatar-initial rounded-circle`;
      resultIcon.style.backgroundColor = color;
      resultIcon.innerHTML = `<i class="icon-base ri ri-${isSuccess ? 'check' : 'close'}-line"></i>`;
      resultName.textContent = data.member_name || data.member?.name || 'Unknown';
      
      // Update header status badge
      if (scanStatus) {
        scanStatus.textContent = isSuccess ? 'GRANTED' : 'DENIED';
        scanStatus.className = `badge fw-bold px-2 py-1 bg-${isSuccess ? 'success' : 'danger'}`;
      }
      
      // Update badges
      const cardNumberEl = document.getElementById('scanResultCardNumber');
      const memberIdEl = document.getElementById('scanResultMemberId');
      const typeEl = document.getElementById('scanResultType');
      if (cardNumberEl) cardNumberEl.textContent = data.card_number || data.member?.card_number || '-';
      if (memberIdEl) memberIdEl.textContent = data.member?.member_id || '-';
      if (typeEl) typeEl.textContent = (data.member?.membership_type || 'Standard').toUpperCase();
      
      resultMessage.innerHTML = `<div class="fw-semibold ${isSuccess ? 'text-success' : 'text-danger'}">${data.message}</div>`;
      
      // Show member details
      const resultDetails = document.getElementById('scanResultDetails');
      if (data.member && resultDetails) {
        resultDetails.classList.remove('d-none');
        document.getElementById('scanResultBalance').textContent = 'TZS ' + parseFloat(data.member.balance || 0).toLocaleString();
        document.getElementById('scanResultStatus').textContent = data.member.status || 'Unknown';
        document.getElementById('scanResultStatus').className = 'badge bg-' + (data.member.status === 'active' ? 'success' : 'warning');
        document.getElementById('scanResultPhone').textContent = data.member.phone || '-';
        document.getElementById('scanResultValidUntil').textContent = data.member.valid_until || 'N/A';
      } else if (resultDetails) {
        resultDetails.classList.add('d-none');
      }
      
      // Auto-refresh after 2.5s
      setTimeout(() => { location.reload(); }, 2500);
    } else {
      throw new Error(data.message || 'Scan failed');
    }
  })
  .catch(err => {
    console.error('Scan error:', err);
    if (resultBox) { resultBox.style.borderColor = '#dc3545'; resultBox.style.background = '#fff5f5'; }
    resultIcon.className = 'avatar-initial rounded-circle';
    resultIcon.style.backgroundColor = '#dc3545';
    resultIcon.innerHTML = '<i class="icon-base ri ri-close-line"></i>';
    resultName.textContent = 'Error';
    resultMessage.textContent = err.message || 'Failed to scan. Please try again.';
    if (scanStatus) { scanStatus.textContent = 'ERROR'; scanStatus.className = 'badge bg-danger fw-bold px-2 py-1'; }
  });
  
  // Clear input and refocus after result
  document.getElementById('cardNumber').value = '';
  document.getElementById('memberPreview')?.classList.add('d-none');
  document.getElementById('clearMemberBtn').style.display = 'none';
  document.getElementById('selectedMemberId').value = '';
  setTimeout(() => { document.getElementById('cardNumber').focus(); }, 2600);
}

function saveGate(e) {
  e.preventDefault();
  
  const btn = e.target.querySelector('button[type="submit"]');
  const origText = btn ? btn.innerHTML : '';
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...'; }

  const formData = {
    name: document.getElementById('gate_name').value,
    type: document.getElementById('gate_type').value,
    location: document.getElementById('gate_location').value || null,
    device_id: document.getElementById('gate_device_id').value || null,
    is_active: document.getElementById('gate_active').checked ? true : false,
    requires_card: document.getElementById('gate_requires_card') ? document.getElementById('gate_requires_card').checked : false
  };
  
  fetch('{{ route("access-control.entry-gates.store") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify(formData)
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Close modal safely
      const modalEl = document.getElementById('addGateModal');
      if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.hide();
        // Wait for modal to close before reloading
        modalEl.addEventListener('hidden.bs.modal', () => { window.location.href = window.location.href; }, { once: true });
      } else {
        window.location.href = window.location.href;
      }
    } else {
      if (btn) { btn.disabled = false; btn.innerHTML = origText; }
      showError('Error: ' + (data.message || JSON.stringify(data.errors) || 'Failed to save gate'));
    }
  })
  .catch(err => {
    if (btn) { btn.disabled = false; btn.innerHTML = origText; }
    console.error('Save error:', err);
    showError('Error saving gate: ' + (err.message || 'Please check console for details'));
  });
}

function editGate(id) {
  const gate = gates.find(g => g.id === id);
  if (!gate) return;
  
  document.getElementById('edit_gate_id').value = gate.id;
  document.getElementById('edit_gate_name').value = gate.name;
  document.getElementById('edit_gate_type').value = gate.type;
  document.getElementById('edit_gate_location').value = gate.location || '';
  document.getElementById('edit_gate_device_id').value = gate.device_id || '';
  document.getElementById('edit_gate_active').checked = gate.is_active;
  document.getElementById('edit_requires_card').checked = !!gate.requires_card;
  
  new bootstrap.Modal(document.getElementById('editGateModal')).show();
}

function updateGate(e) {
  e.preventDefault();
  
  const id = parseInt(document.getElementById('edit_gate_id').value);
  const formData = {
    name: document.getElementById('edit_gate_name').value,
    type: document.getElementById('edit_gate_type').value,
    location: document.getElementById('edit_gate_location').value,
    device_id: document.getElementById('edit_gate_device_id').value,
    is_active: document.getElementById('edit_gate_active').checked,
    requires_card: document.getElementById('edit_requires_card').checked
  };
  
  fetch(`{{ route("access-control.entry-gates.update", ":id") }}`.replace(':id', id), {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify(formData)
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const modalEl = document.getElementById('editGateModal');
      const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modal.hide();
      location.reload();
    } else {
      showError('Error: ' + (data.message || 'Failed to update gate'));
    }
  })
  .catch(err => {
    console.error('Update error:', err);
    showError('Error updating gate. Please try again.');
  });
}

function toggleGate(id) {
  fetch(`{{ route("access-control.entry-gates.toggle", ":id") }}`.replace(':id', id), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      showError('Error: ' + (data.message || 'Failed to toggle gate'));
    }
  })
  .catch(err => {
    console.error('Toggle error:', err);
    showError('Error toggling gate. Please try again.');
  });
}

function deleteGate(id) {
  showConfirm('Are you sure you want to delete this gate? This action cannot be undone.', 'Delete Gate', 'Yes, Delete', 'Cancel').then((result) => {
    if (result.isConfirmed) {
      fetch(`{{ route("access-control.entry-gates.destroy", ":id") }}`.replace(':id', id), {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          showError('Error: ' + (data.message || 'Failed to delete gate'));
        }
      })
      .catch(err => {
        console.error('Delete error:', err);
        showError('Error deleting gate. Please try again.');
      });
    }
  });
}

function openAllGates() {
  showConfirm('Open all gates? This will allow unrestricted access for 5 minutes.', 'Admin Override', 'Open All', 'Cancel').then((result) => {
    if (result.isConfirmed) {
      updateGlobalMode('open', 5);
    }
  });
}

function closeAllGates() {
  showConfirm('Lock all gates? This will prevent all access until manually reset to Normal.', 'System Lockdown', 'Lock All', 'Cancel').then((result) => {
    if (result.isConfirmed) {
      updateGlobalMode('locked');
    }
  });
}

function emergencyMode() {
  showConfirm('Activate EMERGENCY MODE? All gates will open immediately for evacuation.', 'EMERGENCY OVERRIDE', 'Activate Emergency', 'Cancel').then((result) => {
    if (result.isConfirmed) {
      updateGlobalMode('emergency');
    }
  });
}

function resetToNormal() {
  showConfirm('Return system to Normal Mode? Access rules will be reapplied.', 'Reset System', 'Reset to Normal', 'Cancel').then((result) => {
    if (result.isConfirmed) {
      updateGlobalMode('normal');
    }
  });
}

function updateGlobalMode(mode, duration = null) {
  fetch('{{ route("access-control.entry-gates.global-mode") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ mode: mode, duration: duration })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showSuccess(data.message);
      location.reload();
    } else {
      showError('Error: ' + (data.message || 'Failed to update system mode'));
    }
  })
  .catch(err => {
    console.error('Global mode error:', err);
    showError('Error updating system mode. Please try again.');
  });
}


function viewAllLogs() {
  showInfo('Advanced logs view coming soon!');
}

// GET ENTER function - retrieve member info without logging
function getEnter() {
  const selectedMemberIdEl = document.getElementById('selectedMemberId');
  const selectedMemberId = selectedMemberIdEl ? selectedMemberIdEl.value : null;
  const cardInput = document.getElementById('cardNumber');
  if (!cardInput) {
    showError('Card input not found');
    return;
  }
  const cardNumber = cardInput.getAttribute('data-card-number') || cardInput.value.trim();
  
  if (!cardNumber) {
    showWarning('Please search and select a member from the dropdown');
    return;
  }
  
  if (!selectedMemberId) {
    showWarning('Please select a member from the dropdown list.');
    return;
  }
  
  // Show loading
  const resultDiv = document.getElementById('scanResult');
  const resultAlert = document.getElementById('scanResultAlert');
  const resultIcon = document.getElementById('scanResultIcon');
  const resultName = document.getElementById('scanResultName');
  const resultMessage = document.getElementById('scanResultMessage');
  
  if (resultDiv) resultDiv.classList.remove('d-none');
  if (resultAlert) resultAlert.className = 'alert alert-info mb-0';
  if (resultIcon) {
    resultIcon.className = 'avatar-initial rounded-circle bg-info';
    resultIcon.innerHTML = '<i class="icon-base ri ri-loader-line"></i>';
  }
  if (resultName) resultName.textContent = 'Loading...';
  if (resultMessage) resultMessage.textContent = 'Retrieving member information...';
  
  fetch('{{ route("access-control.entry-gates.scan") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      card_number: cardNumber,
      gate_id: 1,
      access_type: 'entry',
      member_id: selectedMemberId,
      action: 'get_enter'
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success && data.member) {
      const member = data.member;
      const resultCard = document.getElementById('scanResultCard');
      const resultDetails = document.getElementById('scanResultDetails');
      
      if (resultCard) resultCard.className = 'card border-0 shadow-sm border-success';
      if (resultIcon) {
        resultIcon.className = 'avatar-initial rounded-circle bg-success';
        resultIcon.style.backgroundColor = '#28a745';
        resultIcon.innerHTML = '<i class="icon-base ri ri-check-line"></i>';
      }
      if (resultName) resultName.textContent = member.name;
      
      // Update badges
      const cardNumberEl = document.getElementById('scanResultCardNumber');
      const memberIdEl = document.getElementById('scanResultMemberId');
      const typeEl = document.getElementById('scanResultType');
      
      if (cardNumberEl) cardNumberEl.textContent = member.card_number || '-';
      if (memberIdEl) memberIdEl.textContent = member.member_id || '-';
      if (typeEl) typeEl.textContent = (member.membership_type || 'Standard').toUpperCase();
      
      if (resultMessage) {
        resultMessage.innerHTML = `<div class="fw-semibold text-success">Member Information Retrieved</div>`;
      }
      
      // Show detailed information
      if (resultDetails) {
        resultDetails.classList.remove('d-none');
        document.getElementById('scanResultBalance').textContent = 'TZS ' + parseFloat(member.balance || 0).toLocaleString();
        document.getElementById('scanResultStatus').textContent = member.status || 'Unknown';
        document.getElementById('scanResultStatus').className = 'badge bg-' + (member.status === 'active' ? 'success' : 'warning');
        document.getElementById('scanResultPhone').textContent = member.phone || '-';
        document.getElementById('scanResultValidUntil').textContent = member.valid_until || 'N/A';
      }
    } else {
      if (resultAlert) resultAlert.className = 'alert alert-danger mb-0';
      if (resultIcon) {
        resultIcon.className = 'avatar-initial rounded-circle bg-danger';
        resultIcon.innerHTML = '<i class="icon-base ri ri-close-line"></i>';
      }
      if (resultName) resultName.textContent = 'Error';
      if (resultMessage) resultMessage.textContent = data.message || 'Member not found';
    }
  })
  .catch(err => {
    console.error('GET ENTER error:', err);
    if (resultAlert) resultAlert.className = 'alert alert-danger mb-0';
    if (resultIcon) {
      resultIcon.className = 'avatar-initial rounded-circle bg-danger';
      resultIcon.innerHTML = '<i class="icon-base ri ri-close-line"></i>';
    }
    if (resultName) resultName.textContent = 'Error';
    if (resultMessage) resultMessage.textContent = 'Network error. Please try again.';
  });
}

// SHOW BALANCE function
function showBalance() {
  const selectedMemberIdEl = document.getElementById('selectedMemberId');
  const selectedMemberId = selectedMemberIdEl ? selectedMemberIdEl.value : null;
  const cardInput = document.getElementById('cardNumber');
  if (!cardInput) {
    showError('Card input not found');
    return;
  }
  const cardNumber = cardInput.getAttribute('data-card-number') || cardInput.value.trim();
  
  if (!cardNumber) {
    showWarning('Please search and select a member from the dropdown');
    return;
  }
  
  if (!selectedMemberId) {
    showWarning('Please select a member from the dropdown list.');
    return;
  }
  
  // Show loading
  const resultDiv = document.getElementById('scanResult');
  const resultAlert = document.getElementById('scanResultAlert');
  const resultIcon = document.getElementById('scanResultIcon');
  const resultName = document.getElementById('scanResultName');
  const resultMessage = document.getElementById('scanResultMessage');
  
  if (resultDiv) resultDiv.classList.remove('d-none');
  if (resultAlert) resultAlert.className = 'alert alert-info mb-0';
  if (resultIcon) {
    resultIcon.className = 'avatar-initial rounded-circle bg-info';
    resultIcon.innerHTML = '<i class="icon-base ri ri-loader-line"></i>';
  }
  if (resultName) resultName.textContent = 'Loading...';
  if (resultMessage) resultMessage.textContent = 'Retrieving balance...';
  
  fetch('{{ route("access-control.entry-gates.scan") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      card_number: cardNumber,
      gate_id: 1,
      access_type: 'entry',
      member_id: selectedMemberId,
      action: 'show_balance'
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success && data.member) {
      const member = data.member;
      const resultCard = document.getElementById('scanResultCard');
      const resultDetails = document.getElementById('scanResultDetails');
      
      if (resultCard) resultCard.className = 'card border-0 shadow-sm border-info';
      if (resultIcon) {
        resultIcon.className = 'avatar-initial rounded-circle bg-info';
        resultIcon.style.backgroundColor = '#17a2b8';
        resultIcon.innerHTML = '<i class="icon-base ri ri-wallet-line"></i>';
      }
      if (resultName) resultName.textContent = member.name;
      
      // Update badges
      const cardNumberEl = document.getElementById('scanResultCardNumber');
      const memberIdEl = document.getElementById('scanResultMemberId');
      const typeEl = document.getElementById('scanResultType');
      
      if (cardNumberEl) cardNumberEl.textContent = member.card_number || '-';
      if (memberIdEl) memberIdEl.textContent = member.member_id || '-';
      if (typeEl) typeEl.textContent = (member.membership_type || 'Standard').toUpperCase();
      
      if (resultMessage) {
        resultMessage.innerHTML = `
          <div class="fw-semibold text-info mb-2">Balance Information</div>
          <div style="font-size: 1.3em; font-weight: bold; color: #28a745;">
            TZS ${parseFloat(member.balance || 0).toLocaleString()}
          </div>
        `;
      }
      
      // Show detailed information
      if (resultDetails) {
        resultDetails.classList.remove('d-none');
        document.getElementById('scanResultBalance').textContent = 'TZS ' + parseFloat(member.balance || 0).toLocaleString();
        document.getElementById('scanResultStatus').textContent = member.status || 'Unknown';
        document.getElementById('scanResultStatus').className = 'badge bg-' + (member.status === 'active' ? 'success' : 'warning');
        document.getElementById('scanResultPhone').textContent = member.phone || '-';
        document.getElementById('scanResultValidUntil').textContent = member.valid_until || 'N/A';
      }
    } else {
      if (resultAlert) resultAlert.className = 'alert alert-danger mb-0';
      if (resultIcon) {
        resultIcon.className = 'avatar-initial rounded-circle bg-danger';
        resultIcon.innerHTML = '<i class="icon-base ri ri-close-line"></i>';
      }
      if (resultName) resultName.textContent = 'Error';
      if (resultMessage) resultMessage.textContent = data.message || 'Member not found';
    }
  })
  .catch(err => {
    console.error('SHOW BALANCE error:', err);
    if (resultAlert) resultAlert.className = 'alert alert-danger mb-0';
    if (resultIcon) {
      resultIcon.className = 'avatar-initial rounded-circle bg-danger';
      resultIcon.innerHTML = '<i class="icon-base ri ri-close-line"></i>';
    }
    if (resultName) resultName.textContent = 'Error';
    if (resultMessage) resultMessage.textContent = 'Network error. Please try again.';
  });
}

function scanCardFromModal() {
  const modalCardInput = document.getElementById('modalCardNumber');
  const modalSelectedMemberId = document.getElementById('modalSelectedMemberId');
  let cardNumber = modalCardInput.getAttribute('data-card-number');
  
  // If no card number in data attribute, extract from input value
  // Input might contain "Name (CardNumber)" or just the search term
  if (!cardNumber) {
    const inputValue = modalCardInput.value.trim();
    // Try to extract card number from "Name (CardNumber)" format
    const match = inputValue.match(/\(([^)]+)\)/);
    if (match) {
      cardNumber = match[1];
    } else {
      // Use the search term directly (name, member ID, or card number)
      cardNumber = inputValue;
    }
  }
  
  const gateId = document.getElementById('modalScanGate').value;
  const scanType = document.querySelector('input[name="modalScanType"]:checked').value;
  const selectedMemberId = modalSelectedMemberId ? modalSelectedMemberId.value : null;
  
  if (!cardNumber || cardNumber.length < 1) {
    showWarning('Please enter or select a member (card number, member ID, or name)');
    modalCardInput.focus();
    return;
  }
  
  if (!gateId) {
    showWarning('Please select a gate');
    document.getElementById('modalScanGate').focus();
    return;
  }
  
  const resultDiv = document.getElementById('modalScanResult');
  const resultCard = document.getElementById('modalScanResultCard');
  const resultIcon = document.getElementById('modalScanResultIcon');
  const resultName = document.getElementById('modalScanResultName');
  const resultMessage = document.getElementById('modalScanResultMessage');
  
  // Show loading
  resultDiv.classList.remove('d-none');
  if (resultCard) resultCard.className = 'card border-0 shadow-sm border-info';
  resultIcon.className = 'avatar-initial rounded-circle bg-info';
  resultIcon.style.backgroundColor = '#17a2b8';
  resultIcon.innerHTML = '<i class="icon-base ri ri-loader-line"></i>';
  resultName.textContent = 'Processing...';
  resultMessage.textContent = 'Scanning card...';
  
  fetch('{{ route("access-control.entry-gates.scan") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      card_number: cardNumber,
      gate_id: gateId,
      access_type: scanType,
      member_id: selectedMemberId
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const isSuccess = data.status === 'success';
      const modalResultCard = document.getElementById('modalScanResultCard');
      const modalResultDetails = document.getElementById('modalScanResultDetails');
      
      // Update main result
      if (modalResultCard) modalResultCard.className = `card border-0 shadow-sm border-${isSuccess ? 'success' : 'danger'}`;
      resultIcon.className = `avatar-initial rounded-circle bg-${isSuccess ? 'success' : 'danger'}`;
      resultIcon.style.backgroundColor = isSuccess ? '#28a745' : '#dc3545';
      resultIcon.innerHTML = `<i class="icon-base ri ri-${isSuccess ? 'check' : 'close'}-line"></i>`;
      resultName.textContent = data.member_name || data.member?.name || 'Unknown';
      
      // Update badges
      const cardNumberEl = document.getElementById('modalScanResultCardNumber');
      const memberIdEl = document.getElementById('modalScanResultMemberId');
      const typeEl = document.getElementById('modalScanResultType');
      
      if (cardNumberEl) cardNumberEl.textContent = data.card_number || data.member?.card_number || '-';
      if (memberIdEl) memberIdEl.textContent = data.member?.member_id || '-';
      if (typeEl) typeEl.textContent = (data.member?.membership_type || 'Standard').toUpperCase();
      
      // Update message with enhanced display
      resultMessage.innerHTML = `<div class="fw-semibold ${isSuccess ? 'text-success' : 'text-danger'}">${data.message}</div>`;
      
      // Show detailed information if member data available
      if (data.member && modalResultDetails) {
        modalResultDetails.classList.remove('d-none');
        document.getElementById('modalScanResultBalance').textContent = 'TZS ' + parseFloat(data.member.balance || 0).toLocaleString();
        document.getElementById('modalScanResultStatus').textContent = data.member.status || 'Unknown';
        document.getElementById('modalScanResultStatus').className = 'badge bg-' + (data.member.status === 'active' ? 'success' : 'warning');
        document.getElementById('modalScanResultPhone').textContent = data.member.phone || '-';
        document.getElementById('modalScanResultValidUntil').textContent = data.member.valid_until || 'N/A';
      } else if (modalResultDetails) {
        modalResultDetails.classList.add('d-none');
      }
      
      // Clear input after successful scan
      document.getElementById('modalCardNumber').value = '';
      document.getElementById('modalSelectedMemberId').value = '';
      
      // Auto-close modal after 2 seconds if successful, or keep it open for errors
      if (isSuccess) {
        setTimeout(() => {
          const modal = bootstrap.Modal.getInstance(document.getElementById('quickScanModal'));
          if (modal) {
            modal.hide();
          }
          // Refresh page to update logs
          setTimeout(() => {
            location.reload();
          }, 500);
        }, 2000);
      } else {
        // Keep modal open on error, refocus input
        setTimeout(() => {
          document.getElementById('modalCardNumber').focus();
        }, 500);
      }
    } else {
      throw new Error(data.message || 'Scan failed');
    }
  })
  .catch(err => {
    console.error('Scan error:', err);
    resultAlert.className = 'alert alert-danger mb-0';
    resultIcon.className = 'avatar-initial rounded-circle bg-danger';
    resultIcon.innerHTML = '<i class="icon-base ri ri-close-line"></i>';
    resultName.textContent = 'Error';
    resultMessage.textContent = err.message || 'Failed to scan card. Please try again.';
    // Keep modal open on error so user can retry
    document.getElementById('modalCardNumber').focus();
  });
}

// Advanced Analytics Functions
function showAnalyticsTab(tab) {
  // Hide all tabs
  document.querySelectorAll('.analytics-tab').forEach(el => el.classList.add('d-none'));
  document.querySelectorAll('#analyticsTabHourly, #analyticsTabDaily, #analyticsTabGates').forEach(el => {
    el.classList.remove('active');
  });
  
  // Show selected tab
  document.getElementById('analytics' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.remove('d-none');
  document.getElementById('analyticsTab' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.add('active');
}

// Advanced Filtering Functions
function applyAdvancedFilters() {
  const filters = {
    date_from: document.getElementById('filterDateFrom').value,
    date_to: document.getElementById('filterDateTo').value,
    gate_id: document.getElementById('filterGate').value,
    access_type: document.getElementById('filterAccessType').value,
    status: document.getElementById('filterStatus').value,
    search: document.getElementById('filterSearch').value
  };
  
  // Build query string
  const params = new URLSearchParams();
  Object.keys(filters).forEach(key => {
    if (filters[key]) {
      params.append(key, filters[key]);
    }
  });
  
  // Fetch filtered logs
  fetch('{{ route("access-control.entry-gates.logs") }}?' + params.toString(), {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      updateLogsTable(data.logs.data || data.logs);
    }
  })
  .catch(err => {
    console.error('Filter error:', err);
    showError('Error applying filters');
  });
}

function resetAdvancedFilters() {
  document.getElementById('filterDateFrom').value = '{{ date("Y-m-d", strtotime("-7 days")) }}';
  document.getElementById('filterDateTo').value = '{{ date("Y-m-d") }}';
  document.getElementById('filterGate').value = '';
  document.getElementById('filterAccessType').value = '';
  document.getElementById('filterStatus').value = '';
  document.getElementById('filterSearch').value = '';
  
  // Reload original logs
  refreshLogs();
}

function updateLogsTable(logs) {
  const tbody = document.getElementById('accessLogsTable');
  if (!tbody) return;
  
  if (logs.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted"><i class="icon-base ri ri-history-line d-block fs-2 mb-2"></i>No access activity recorded</td></tr>';
    return;
  }
  
  tbody.innerHTML = logs.map(log => {
    const isSuccess = log.status === 'success';
    const statusColor = isSuccess ? 'success' : 'danger';
    const accessTypeColor = log.access_type === 'entry' ? 'success' : 'info';
    const time = new Date(log.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const date = new Date(log.created_at).toLocaleDateString([], { day: '2-digit', month: 'short' });
    
    return `
      <tr>
        <td class="ps-4">
          <div class="fw-bold text-dark">${time}</div>
          <small class="text-muted">${date}</small>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-3">
              <span class="avatar-initial rounded-circle bg-label-${statusColor}">
                <i class="icon-base ri ri-${isSuccess ? 'shield-check' : 'shield-keyhole'}-line"></i>
              </span>
            </div>
            <div>
              <div class="fw-bold text-dark">${log.member_name || 'Generic Card'}</div>
              <code class="text-primary small">${log.card_number}</code>
            </div>
          </div>
        </td>
        <td>
          <span class="badge bg-label-secondary border-0 fw-medium">${log.gate ? log.gate.name : 'System'}</span>
        </td>
        <td>
          <span class="badge bg-${accessTypeColor} bg-opacity-10 text-${accessTypeColor} border-0 fw-bold">
            ${log.access_type.toUpperCase()}
          </span>
        </td>
        <td>
          <span class="badge bg-${statusColor} px-3 rounded-pill">
            ${log.status.toUpperCase()}
          </span>
          ${log.denial_reason ? `<div class="text-danger x-small mt-1 fw-medium" style="font-size: 0.65rem;">${log.denial_reason.substring(0, 35)}</div>` : ''}
        </td>
        <td class="text-end pe-4">
          ${log.member_id ? `<a href="/payments/members/${log.member_id}" class="btn btn-icon btn-label-primary btn-sm rounded-pill"><i class="icon-base ri ri-user-search-line"></i></a>` : ''}
        </td>
      </tr>
    `;
  }).join('');
}

function exportFilteredLogs() {
  const filters = {
    date_from: document.getElementById('filterDateFrom').value,
    date_to: document.getElementById('filterDateTo').value,
    gate_id: document.getElementById('filterGate').value,
    access_type: document.getElementById('filterAccessType').value,
    status: document.getElementById('filterStatus').value,
    search: document.getElementById('filterSearch').value
  };
  
  const params = new URLSearchParams();
  Object.keys(filters).forEach(key => {
    if (filters[key]) {
      params.append(key, filters[key]);
    }
  });
  
  window.location.href = '{{ route("access-control.entry-gates.logs.export") }}?' + params.toString();
}

// Auto-refresh logs every 2 minutes
document.addEventListener('DOMContentLoaded', function() {
  setInterval(refreshLogs, 120000);
  
  // Live clock for Command Center
  const clockEl = document.getElementById('liveClock');
  if (clockEl) {
    function updateClock() {
      const now = new Date();
      clockEl.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
    }
    updateClock();
    setInterval(updateClock, 1000);
  }
});
</script>
@endpush
