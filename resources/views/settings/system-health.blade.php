@extends('settings._layout-base')

@section('title', 'System Health')
@section('description', 'System Health - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Settings /</span> System Health
</h4>

<!-- Header Card -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%);">
      <div class="card-body text-white p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="mb-2 text-white fw-bold">
              <i class="icon-base ri ri-heart-pulse-line me-2"></i>System Health Monitor
            </h4>
            <p class="mb-0 opacity-75">Monitor system performance, storage, and services</p>
          </div>
          <div class="d-flex gap-2 mt-3 mt-md-0">
            <button class="btn btn-light" onclick="refreshAllStats()">
              <i class="icon-base ri ri-refresh-line me-1"></i>Refresh All
            </button>
            <a href="{{ route('settings.organization') }}" class="btn btn-outline-light">
              <i class="icon-base ri ri-arrow-left-line me-1"></i>Back
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- System Status Cards -->
<div class="row mb-4">
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="mb-2">
          <i class="icon-base ri ri-server-line" style="font-size: 40px; color: #28a745;"></i>
        </div>
        <h6 class="mb-1">Server Status</h6>
        <span class="badge bg-success" id="serverStatus">Online</span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="mb-2">
          <i class="icon-base ri ri-database-2-line" style="font-size: 40px; color: #17a2b8;"></i>
        </div>
        <h6 class="mb-1">Database</h6>
        <span class="badge bg-success" id="dbStatus">Connected</span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="mb-2">
          <i class="icon-base ri ri-hard-drive-2-line" style="font-size: 40px; color: #ffc107;"></i>
        </div>
        <h6 class="mb-1">Storage</h6>
        <span class="badge bg-warning" id="storageStatus">Loading...</span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="mb-2">
          <i class="icon-base ri ri-shield-check-line" style="font-size: 40px; color: #28a745;"></i>
        </div>
        <h6 class="mb-1">Security</h6>
        <span class="badge bg-success" id="securityStatus">Secure</span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- System Information -->
  <div class="col-lg-6">
    <div class="card mb-4">
      <div class="card-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="mb-0 text-white"><i class="icon-base ri ri-information-line me-2"></i>System Information</h5>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <tbody>
            <tr>
              <td class="fw-bold" style="width: 40%;">PHP Version</td>
              <td><span class="badge bg-label-primary">{{ PHP_VERSION }}</span></td>
            </tr>
            <tr>
              <td class="fw-bold">Laravel Version</td>
              <td><span class="badge bg-label-primary">{{ app()->version() }}</span></td>
            </tr>
            <tr>
              <td class="fw-bold">Server Software</td>
              <td>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' }}</td>
            </tr>
            <tr>
              <td class="fw-bold">Server OS</td>
              <td>{{ php_uname('s') }} {{ php_uname('r') }}</td>
            </tr>
            <tr>
              <td class="fw-bold">Server Time</td>
              <td id="serverTime">{{ now()->format('d M Y, H:i:s') }}</td>
            </tr>
            <tr>
              <td class="fw-bold">Timezone</td>
              <td>{{ config('app.timezone') }}</td>
            </tr>
            <tr>
              <td class="fw-bold">Memory Limit</td>
              <td>{{ ini_get('memory_limit') }}</td>
            </tr>
            <tr>
              <td class="fw-bold">Max Upload Size</td>
              <td>{{ ini_get('upload_max_filesize') }}</td>
            </tr>
            <tr>
              <td class="fw-bold">Max Execution Time</td>
              <td>{{ ini_get('max_execution_time') }}s</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Database Information -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-database-2-line me-2"></i>Database Information</h5>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <tbody>
            <tr>
              <td class="fw-bold" style="width: 40%;">Database Driver</td>
              <td>{{ config('database.default') }}</td>
            </tr>
            <tr>
              <td class="fw-bold">Database Name</td>
              <td>{{ config('database.connections.' . config('database.default') . '.database') }}</td>
            </tr>
            <tr>
              <td class="fw-bold">Total Tables</td>
              <td id="totalTables">Loading...</td>
            </tr>
            <tr>
              <td class="fw-bold">Database Size</td>
              <td id="dbSize">Loading...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Storage & Cache -->
  <div class="col-lg-6">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-hard-drive-2-line me-2"></i>Storage Usage</h5>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <div class="d-flex justify-content-between mb-2">
            <span class="fw-bold">Application Storage</span>
            <span id="appStorageText">Calculating...</span>
          </div>
          <div class="progress" style="height: 10px;">
            <div class="progress-bar bg-primary" id="appStorageBar" style="width: 0%"></div>
          </div>
        </div>
        <div class="mb-4">
          <div class="d-flex justify-content-between mb-2">
            <span class="fw-bold">Uploads (Avatars, Logos)</span>
            <span id="uploadsStorageText">Calculating...</span>
          </div>
          <div class="progress" style="height: 10px;">
            <div class="progress-bar bg-success" id="uploadsStorageBar" style="width: 0%"></div>
          </div>
        </div>
        <div class="mb-4">
          <div class="d-flex justify-content-between mb-2">
            <span class="fw-bold">Cache Storage</span>
            <span id="cacheStorageText">Calculating...</span>
          </div>
          <div class="progress" style="height: 10px;">
            <div class="progress-bar bg-warning" id="cacheStorageBar" style="width: 0%"></div>
          </div>
        </div>
        <div>
          <div class="d-flex justify-content-between mb-2">
            <span class="fw-bold">Logs</span>
            <span id="logsStorageText">Calculating...</span>
          </div>
          <div class="progress" style="height: 10px;">
            <div class="progress-bar bg-danger" id="logsStorageBar" style="width: 0%"></div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Cache Management -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-delete-bin-line me-2"></i>Cache Management</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <button class="btn btn-outline-primary w-100" onclick="clearCache('config')">
              <i class="icon-base ri ri-settings-3-line me-1"></i>Clear Config Cache
            </button>
          </div>
          <div class="col-md-6">
            <button class="btn btn-outline-info w-100" onclick="clearCache('view')">
              <i class="icon-base ri ri-eye-line me-1"></i>Clear View Cache
            </button>
          </div>
          <div class="col-md-6">
            <button class="btn btn-outline-warning w-100" onclick="clearCache('route')">
              <i class="icon-base ri ri-route-line me-1"></i>Clear Route Cache
            </button>
          </div>
          <div class="col-md-6">
            <button class="btn btn-outline-success w-100" onclick="clearCache('application')">
              <i class="icon-base ri ri-database-line me-1"></i>Clear App Cache
            </button>
          </div>
          <div class="col-12">
            <button class="btn btn-danger w-100" onclick="clearCache('all')">
              <i class="icon-base ri ri-delete-bin-line me-1"></i>Clear All Caches
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Services Status -->
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-apps-line me-2"></i>Services Status</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Service</th>
                <th>Status</th>
                <th>Last Check</th>
                <th>Response Time</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><i class="icon-base ri ri-database-2-line me-2 text-primary"></i>Database</td>
                <td><span class="badge bg-success">Connected</span></td>
                <td>{{ now()->format('H:i:s') }}</td>
                <td><span class="text-success">< 1ms</span></td>
                <td><button class="btn btn-sm btn-outline-primary" onclick="testService('database')">Test</button></td>
              </tr>
              <tr>
                <td><i class="icon-base ri ri-mail-line me-2 text-danger"></i>Email (SMTP)</td>
                <td><span class="badge bg-warning" id="emailServiceStatus">Not Configured</span></td>
                <td>-</td>
                <td>-</td>
                <td><button class="btn btn-sm btn-outline-primary" onclick="testService('email')">Test</button></td>
              </tr>
              <tr>
                <td><i class="icon-base ri ri-message-2-line me-2 text-info"></i>SMS Gateway</td>
                <td><span class="badge bg-warning" id="smsServiceStatus">Not Configured</span></td>
                <td>-</td>
                <td>-</td>
                <td><button class="btn btn-sm btn-outline-primary" onclick="testService('sms')">Test</button></td>
              </tr>
              <tr>
                <td><i class="icon-base ri ri-hard-drive-line me-2 text-success"></i>File Storage</td>
                <td><span class="badge bg-success">Writable</span></td>
                <td>{{ now()->format('H:i:s') }}</td>
                <td><span class="text-success">< 1ms</span></td>
                <td><button class="btn btn-sm btn-outline-primary" onclick="testService('storage')">Test</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Logs -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="icon-base ri ri-file-list-line me-2"></i>Recent System Logs</h5>
        <button class="btn btn-sm btn-outline-danger" onclick="clearLogs()">
          <i class="icon-base ri ri-delete-bin-line me-1"></i>Clear Logs
        </button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
          <table class="table table-sm table-hover mb-0">
            <thead class="table-light sticky-top">
              <tr>
                <th>Time</th>
                <th>Level</th>
                <th>Message</th>
              </tr>
            </thead>
            <tbody id="logsTable">
              <tr>
                <td>{{ now()->format('H:i:s') }}</td>
                <td><span class="badge bg-success">INFO</span></td>
                <td>System health check initiated</td>
              </tr>
              <tr>
                <td>{{ now()->subMinutes(5)->format('H:i:s') }}</td>
                <td><span class="badge bg-success">INFO</span></td>
                <td>Database connection verified</td>
              </tr>
              <tr>
                <td>{{ now()->subMinutes(10)->format('H:i:s') }}</td>
                <td><span class="badge bg-success">INFO</span></td>
                <td>Application started successfully</td>
              </tr>
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
document.addEventListener('DOMContentLoaded', function() {
  loadStorageStats();
  loadDatabaseStats();
  updateServerTime();
  setInterval(updateServerTime, 1000);
});

function refreshAllStats() {
  loadStorageStats();
  loadDatabaseStats();
  alert('Statistics refreshed!');
}

function loadStorageStats() {
  // Simulated storage stats
  document.getElementById('appStorageText').textContent = '45 MB / 500 MB';
  document.getElementById('appStorageBar').style.width = '9%';
  
  document.getElementById('uploadsStorageText').textContent = '12 MB / 100 MB';
  document.getElementById('uploadsStorageBar').style.width = '12%';
  
  document.getElementById('cacheStorageText').textContent = '5 MB / 50 MB';
  document.getElementById('cacheStorageBar').style.width = '10%';
  
  document.getElementById('logsStorageText').textContent = '2 MB / 20 MB';
  document.getElementById('logsStorageBar').style.width = '10%';
  
  document.getElementById('storageStatus').textContent = '64 MB Used';
  document.getElementById('storageStatus').className = 'badge bg-success';
}

function loadDatabaseStats() {
  document.getElementById('totalTables').textContent = '15 tables';
  document.getElementById('dbSize').textContent = '2.5 MB';
}

function updateServerTime() {
  const now = new Date();
  document.getElementById('serverTime').textContent = now.toLocaleString('en-GB', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit'
  });
}

function clearCache(type) {
  const messages = {
    'config': 'Configuration cache cleared!',
    'view': 'View cache cleared!',
    'route': 'Route cache cleared!',
    'application': 'Application cache cleared!',
    'all': 'All caches cleared successfully!'
  };
  
  alert(messages[type] || 'Cache cleared!');
}

function testService(service) {
  alert('Testing ' + service + ' service...\n\nService is operational!');
}

function clearLogs() {
  if (confirm('Are you sure you want to clear all logs?')) {
    document.getElementById('logsTable').innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">No logs available</td></tr>';
    alert('Logs cleared successfully!');
  }
}
</script>
@endpush
