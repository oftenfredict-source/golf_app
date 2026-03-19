@extends('settings._layout-base')

@section('title', 'System Configuration')
@section('description', 'System Configuration - Golf Club Management System')

@section('content')
<style>
    /* Responsive Tabs for Configuration */
    .card-header-tabs {
        flex-wrap: nowrap !important;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 5px;
    }
    .card-header-tabs .nav-item {
        white-space: nowrap;
    }
    
    @media (max-width: 767.98px) {
        .card-header-tabs {
            margin-bottom: 0 !important;
        }
        .tab-content {
            padding-top: 1rem;
        }
        .card-body {
            padding: 1.25rem !important;
        }
        .card-body.p-4 {
            padding: 1rem !important;
        }
        .btn-primary {
            width: 100%;
        }
    }
</style>

<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Settings /</span> System Configuration
</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="icon-base ri ri-checkbox-circle-line me-2"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Header Card -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%);">
      <div class="card-body text-white p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="mb-2 text-white fw-bold">
              <i class="icon-base ri ri-settings-3-line me-2"></i>System Configuration
            </h4>
            <p class="mb-0 opacity-75">Centralized configuration for all system modules</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Configuration Tabs -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#driving-range" type="button" role="tab" id="driving-range-tab">
              <i class="icon-base ri ri-golf-ball-line me-1"></i> Driving Range
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#equipment-rental" type="button" role="tab" id="equipment-rental-tab">
              <i class="icon-base ri ri-shopping-bag-line me-1"></i> Equipment Rental
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#access-control" type="button" role="tab" id="access-control-tab">
              <i class="icon-base ri ri-shield-check-line me-1"></i> Access Control
            </button>
          </li>
        </ul>
      </div>
      <div class="card-body">
        <div class="tab-content">
          <!-- Driving Range Configuration -->
          <div class="tab-pane fade show active" id="driving-range" role="tabpanel">
            <form id="drivingRangeConfigForm">
              @csrf
              
              <!-- Range Settings -->
              <h6 class="mb-3 fw-semibold">Range Settings</h6>
              <div class="row">
                <div class="col-md-4 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control" id="total_bays" name="total_bays" value="{{ $drivingRangeConfig->total_bays ?? 20 }}" />
                    <label for="total_bays">Total Driving Range Bays</label>
                  </div>
                  <small class="text-muted">Number of available bays</small>
                </div>
                <div class="col-md-4 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control" id="balls_per_bucket" name="balls_per_bucket" value="{{ $drivingRangeConfig->balls_per_bucket ?? 50 }}" />
                    <label for="balls_per_bucket">Balls per Bucket</label>
                  </div>
                  <small class="text-muted">Number of balls in each bucket</small>
                </div>
                <div class="col-md-4 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control" id="range_distance" name="range_distance" value="{{ $drivingRangeConfig->range_distance ?? 250 }}" />
                    <label for="range_distance">Range Distance (yards)</label>
                  </div>
                  <small class="text-muted">Maximum distance of the range</small>
                </div>
              </div>

              <hr class="my-4">

              <!-- Pricing Settings -->
              <h6 class="mb-3 fw-semibold">Pricing Settings</h6>
              <div class="row">
                <div class="col-md-4 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="ball_limit_price" name="ball_limit_price" value="{{ $drivingRangeConfig->ball_limit_price ?? $drivingRangeConfig->hourly_rate ?? 5000 }}" />
                    <label for="ball_limit_price">Ball Limit Price (TZS)</label>
                  </div>
                  <small class="text-muted">Price for ball limit session</small>
                </div>
                <div class="col-md-4 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control" id="balls_limit_per_session" name="balls_limit_per_session" value="{{ $drivingRangeConfig->balls_limit_per_session ?? 50 }}" />
                    <label for="balls_limit_per_session">Default Balls Limit</label>
                  </div>
                  <small class="text-muted">Default number of balls per session</small>
                </div>
                <div class="col-md-4 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="bucket_price" name="bucket_price" value="{{ $drivingRangeConfig->bucket_price ?? 2000 }}" />
                    <label for="bucket_price">Bucket Price (TZS)</label>
                  </div>
                  <small class="text-muted">Price per bucket of balls</small>
                </div>
                <div class="col-md-4 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="unlimited_price" name="unlimited_price" value="{{ $drivingRangeConfig->unlimited_price ?? 8000 }}" />
                    <label for="unlimited_price">Unlimited (1hr) Price (TZS)</label>
                  </div>
                  <small class="text-muted">Unlimited balls for 1 hour</small>
                </div>
                <div class="col-md-4 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="member_discount" name="member_discount" value="{{ $drivingRangeConfig->member_discount ?? 10 }}" />
                    <label for="member_discount">Member Discount (%)</label>
                  </div>
                  <small class="text-muted">Discount for club members</small>
                </div>
              </div>

              <hr class="my-4">

              <!-- Range Features -->
              <h6 class="mb-3 fw-semibold">Range Features</h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="has_roof" name="has_roof" {{ ($drivingRangeConfig->has_roof ?? true) ? 'checked' : '' }} />
                    <label class="form-check-label" for="has_roof">Covered/Roofed Range</label>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="has_lighting" name="has_lighting" {{ ($drivingRangeConfig->has_lighting ?? true) ? 'checked' : '' }} />
                    <label class="form-check-label" for="has_lighting">Lighting Available</label>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="has_tracking" name="has_tracking" {{ ($drivingRangeConfig->has_tracking ?? false) ? 'checked' : '' }} />
                    <label class="form-check-label" for="has_tracking">Ball Flight Tracking</label>
                  </div>
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                  <i class="icon-base ri ri-save-line me-1"></i> <span>Save Driving Range Configuration</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Equipment Rental Configuration -->
          <div class="tab-pane fade" id="equipment-rental" role="tabpanel">
            <form id="equipmentRentalConfigForm">
              @csrf
              
              <!-- Deposit & Fees -->
              <h6 class="mb-3 fw-semibold">Deposit & Fees</h6>
              <div class="row">
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="security_deposit" name="security_deposit" value="{{ $rentalConfig->security_deposit ?? 50000 }}" min="0" />
                    <label for="security_deposit">Security Deposit (TZS)</label>
                  </div>
                  <small class="text-muted">Default security deposit amount</small>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="late_fee_per_hour" name="late_fee_per_hour" value="{{ $rentalConfig->late_fee_per_hour ?? 5000 }}" min="0" />
                    <label for="late_fee_per_hour">Late Return Fee per Hour (TZS)</label>
                  </div>
                  <small class="text-muted">Fee charged for each hour equipment is returned late</small>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="extension_fee_per_hour" name="extension_fee_per_hour" value="{{ $rentalConfig->extension_fee_per_hour ?? 3000 }}" min="0" />
                    <label for="extension_fee_per_hour">Extension Fee per Hour (TZS)</label>
                  </div>
                  <small class="text-muted">Fee for extending rental period</small>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="damage_fee_percentage" name="damage_fee_percentage" value="{{ $rentalConfig->damage_fee_percentage ?? 10 }}" min="0" max="100" />
                    <label for="damage_fee_percentage">Damage Fee Percentage (%)</label>
                  </div>
                  <small class="text-muted">Percentage of equipment value charged for damages</small>
                </div>
              </div>

              <hr class="my-4">

              <!-- Rental Limits -->
              <h6 class="mb-3 fw-semibold">Rental Limits</h6>
              <div class="row">
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control" id="max_rental_hours" name="max_rental_hours" value="{{ $rentalConfig->max_rental_hours ?? 4 }}" min="1" />
                    <label for="max_rental_hours">Maximum Rental Hours</label>
                  </div>
                  <small class="text-muted">Maximum hours allowed for a single rental</small>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control" id="grace_period_minutes" name="grace_period_minutes" value="{{ $rentalConfig->grace_period_minutes ?? 15 }}" min="0" />
                    <label for="grace_period_minutes">Grace Period (minutes)</label>
                  </div>
                  <small class="text-muted">Grace period before late fees are applied</small>
                </div>
              </div>

              <hr class="my-4">

              <!-- Rental Policies -->
              <h6 class="mb-3 fw-semibold">Rental Policies</h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="require_deposit" name="require_deposit" {{ ($rentalConfig->require_deposit ?? true) ? 'checked' : '' }} />
                    <label class="form-check-label" for="require_deposit">Require Security Deposit</label>
                  </div>
                  <small class="text-muted d-block mt-1">Require security deposit before rental</small>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="allow_extensions" name="allow_extensions" {{ ($rentalConfig->allow_extensions ?? true) ? 'checked' : '' }} />
                    <label class="form-check-label" for="allow_extensions">Allow Rental Extensions</label>
                  </div>
                  <small class="text-muted d-block mt-1">Allow customers to extend rental period</small>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="auto_charge_late" name="auto_charge_late" {{ ($rentalConfig->auto_charge_late ?? true) ? 'checked' : '' }} />
                    <label class="form-check-label" for="auto_charge_late">Auto-charge Late Fees</label>
                  </div>
                  <small class="text-muted d-block mt-1">Automatically charge late fees on return</small>
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                  <i class="icon-base ri ri-save-line me-1"></i> <span>Save Equipment Rental Configuration</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Access Control Configuration -->
          <div class="tab-pane fade" id="access-control" role="tabpanel">
            <form id="accessControlConfigForm">
              @csrf
              
              <!-- Access Rules -->
              <h6 class="mb-3 fw-semibold">Access Rules</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="members_only" name="members_only" {{ ($accessControlConfig->members_only ?? true) ? 'checked' : '' }} />
                    <label class="form-check-label" for="members_only">Members Only</label>
                  </div>
                  <small class="text-muted d-block mt-1">Only allow registered members to access</small>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="require_valid_card" name="require_valid_card" {{ ($accessControlConfig->require_valid_card ?? true) ? 'checked' : '' }} />
                    <label class="form-check-label" for="require_valid_card">Require Valid Card</label>
                  </div>
                  <small class="text-muted d-block mt-1">Require a valid member card for entry</small>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="check_balance" name="check_balance" {{ ($accessControlConfig->check_balance ?? false) ? 'checked' : '' }} />
                    <label class="form-check-label" for="check_balance">Check Balance</label>
                  </div>
                  <small class="text-muted d-block mt-1">Verify member balance before allowing entry</small>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="allow_guests" name="allow_guests" {{ ($accessControlConfig->allow_guests ?? true) ? 'checked' : '' }} />
                    <label class="form-check-label" for="allow_guests">Allow Guests</label>
                  </div>
                  <small class="text-muted d-block mt-1">Allow non-member guests to enter</small>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="operating_hours_only" name="operating_hours_only" {{ ($accessControlConfig->operating_hours_only ?? true) ? 'checked' : '' }} />
                    <label class="form-check-label" for="operating_hours_only">Operating Hours Only</label>
                  </div>
                  <small class="text-muted d-block mt-1">Restrict access to operating hours only</small>
                </div>
              </div>

              <hr class="my-4">

              <!-- Operating Hours -->
              <h6 class="mb-3 fw-semibold">Operating Hours</h6>
              <div class="row">
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="time" class="form-control" id="opening_time" name="opening_time" value="{{ $accessControlConfig->opening_time ?? '06:00' }}" />
                    <label for="opening_time">Opening Time</label>
                  </div>
                  <small class="text-muted">When access control starts</small>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="time" class="form-control" id="closing_time" name="closing_time" value="{{ $accessControlConfig->closing_time ?? '22:00' }}" />
                    <label for="closing_time">Closing Time</label>
                  </div>
                  <small class="text-muted">When access control ends</small>
                </div>
              </div>

              <hr class="my-4">

              <!-- Membership Requirements -->
              <h6 class="mb-3 fw-semibold">Membership Requirements</h6>
              <div class="row">
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="min_balance" name="min_balance" value="{{ $accessControlConfig->min_balance ?? 0 }}" min="0" />
                    <label for="min_balance">Minimum Balance for Entry (TZS)</label>
                  </div>
                  <small class="text-muted">Minimum balance required for member entry</small>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" step="0.01" class="form-control" id="guest_fee" name="guest_fee" value="{{ $accessControlConfig->guest_fee ?? 50000 }}" min="0" />
                    <label for="guest_fee">Guest Entry Fee (TZS)</label>
                  </div>
                  <small class="text-muted">Fee charged for guest entry</small>
                </div>
              </div>

              <hr class="my-4">

              <!-- Blocked Cards -->
              <h6 class="mb-3 fw-semibold">Blocked Cards</h6>
              <div class="row">
                <div class="col-12 mb-4">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control" id="blocked_cards" name="blocked_cards" rows="4" placeholder="Enter blocked card numbers, one per line">{{ $accessControlConfig->blocked_cards ?? '' }}</textarea>
                    <label for="blocked_cards">Blocked Card Numbers</label>
                  </div>
                  <small class="text-muted">Enter card numbers to block, one per line. These cards will be denied access at all gates.</small>
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                  <i class="icon-base ri ri-save-line me-1"></i> <span>Save Access Control Configuration</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Driving Range Configuration Form
document.getElementById('drivingRangeConfigForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  
  fetch('{{ route("golf-services.driving-range.config") }}', {
    method: 'POST',
    body: new FormData(this),
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showSuccess('Driving Range configuration saved successfully!').then(() => location.reload());
    } else {
      showError('Error saving configuration');
    }
  })
  .catch(err => {
    console.error(err);
    showError('Error saving configuration');
  });
});

// Equipment Rental Configuration Form
document.getElementById('equipmentRentalConfigForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  
  // Convert checkboxes to boolean values
  formData.set('require_deposit', document.getElementById('require_deposit').checked ? '1' : '0');
  formData.set('allow_extensions', document.getElementById('allow_extensions').checked ? '1' : '0');
  formData.set('auto_charge_late', document.getElementById('auto_charge_late').checked ? '1' : '0');
  
  fetch('{{ route("golf-services.rental-configuration.update") }}', {
    method: 'POST',
    body: formData,
    headers: { 
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    }
  })
  .then(r => {
    if (!r.ok) throw new Error('Network response was not ok');
    return r.json();
  })
  .then(data => {
    if (data.success) {
      showSuccess('Equipment Rental configuration saved successfully!').then(() => location.reload());
    } else {
      showError(data.message || 'Failed to save configuration');
    }
  })
  .catch(err => {
    console.error(err);
    showError('Error saving configuration: ' + err.message);
  });
});

// Access Control Configuration Form
document.getElementById('accessControlConfigForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  
  // Convert checkboxes to boolean values
  formData.set('members_only', document.getElementById('members_only').checked ? '1' : '0');
  formData.set('require_valid_card', document.getElementById('require_valid_card').checked ? '1' : '0');
  formData.set('check_balance', document.getElementById('check_balance').checked ? '1' : '0');
  formData.set('allow_guests', document.getElementById('allow_guests').checked ? '1' : '0');
  formData.set('operating_hours_only', document.getElementById('operating_hours_only').checked ? '1' : '0');
  
  fetch('{{ route("settings.access-control-config.save") }}', {
    method: 'POST',
    body: formData,
    headers: { 
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    }
  })
  .then(r => {
    if (!r.ok) throw new Error('Network response was not ok');
    return r.json();
  })
  .then(data => {
    if (data.success) {
      showSuccess('Access control configuration saved successfully!').then(() => location.reload());
    } else {
      showError(data.message || 'Failed to save configuration');
    }
  })
  .catch(err => {
    console.error(err);
    showError('Error saving configuration: ' + err.message);
  });
});

// Handle URL hash to switch tabs
document.addEventListener('DOMContentLoaded', function() {
  const hash = window.location.hash;
  if (hash === '#equipment-rental') {
    const tab = new bootstrap.Tab(document.getElementById('equipment-rental-tab'));
    tab.show();
  } else if (hash === '#access-control') {
    const tab = new bootstrap.Tab(document.getElementById('access-control-tab'));
    tab.show();
  } else if (hash === '#driving-range' || !hash) {
    const tab = new bootstrap.Tab(document.getElementById('driving-range-tab'));
    tab.show();
  }
});
</script>
@endpush

