@extends('settings._layout-base')

@section('title', 'Rental Configuration')
@section('description', 'Rental Configuration - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Golf Services / Settings /</span> Rental Configuration
</h4>

<div class="row">
  <div class="col-xl-8">
    <div class="card">
      <div class="card-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="mb-0 text-white"><i class="icon-base ri ri-settings-3-line me-2"></i>Equipment Rental Settings</h5>
      </div>
      <div class="card-body">
        <form id="rentalConfigForm">
          @csrf
          
          <h6 class="mb-3 fw-semibold">Deposit & Fees</h6>
          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="security_deposit" name="security_deposit" value="{{ $config->security_deposit ?? 50000 }}" min="0" />
                <label for="security_deposit">Security Deposit (TZS)</label>
              </div>
              <small class="text-muted">Default security deposit amount for equipment rentals</small>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="late_fee_per_hour" name="late_fee_per_hour" value="{{ $config->late_fee_per_hour ?? 5000 }}" min="0" />
                <label for="late_fee_per_hour">Late Return Fee per Hour (TZS)</label>
              </div>
              <small class="text-muted">Fee charged for each hour equipment is returned late</small>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="extension_fee_per_hour" name="extension_fee_per_hour" value="{{ $config->extension_fee_per_hour ?? 3000 }}" min="0" />
                <label for="extension_fee_per_hour">Extension Fee per Hour (TZS)</label>
              </div>
              <small class="text-muted">Fee for extending rental period</small>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="damage_fee_percentage" name="damage_fee_percentage" value="{{ $config->damage_fee_percentage ?? 10 }}" min="0" max="100" />
                <label for="damage_fee_percentage">Damage Fee Percentage (%)</label>
              </div>
              <small class="text-muted">Percentage of equipment value charged for damages</small>
            </div>
          </div>

          <hr class="my-4">

          <h6 class="mb-3 fw-semibold">Rental Limits</h6>
          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="max_rental_hours" name="max_rental_hours" value="{{ $config->max_rental_hours ?? 4 }}" min="1" />
                <label for="max_rental_hours">Maximum Rental Hours</label>
              </div>
              <small class="text-muted">Maximum hours allowed for a single rental</small>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="grace_period_minutes" name="grace_period_minutes" value="{{ $config->grace_period_minutes ?? 15 }}" min="0" />
                <label for="grace_period_minutes">Grace Period (minutes)</label>
              </div>
              <small class="text-muted">Grace period before late fees are applied</small>
            </div>
          </div>

          <hr class="my-4">

          <h6 class="mb-3 fw-semibold">Rental Policies</h6>
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="require_deposit" name="require_deposit" {{ ($config->require_deposit ?? true) ? 'checked' : '' }} />
                <label class="form-check-label" for="require_deposit">Require Security Deposit</label>
              </div>
              <small class="text-muted d-block mt-1">Require security deposit before rental</small>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="allow_extensions" name="allow_extensions" {{ ($config->allow_extensions ?? true) ? 'checked' : '' }} />
                <label class="form-check-label" for="allow_extensions">Allow Rental Extensions</label>
              </div>
              <small class="text-muted d-block mt-1">Allow customers to extend rental period</small>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="auto_charge_late" name="auto_charge_late" {{ ($config->auto_charge_late ?? true) ? 'checked' : '' }} />
                <label class="form-check-label" for="auto_charge_late">Auto-charge Late Fees</label>
              </div>
              <small class="text-muted d-block mt-1">Automatically charge late fees on return</small>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="icon-base ri ri-save-line me-1"></i> Save Configuration
            </button>
            <a href="{{ route('golf-services.equipment-rental') }}" class="btn btn-label-secondary ms-2">
              <i class="icon-base ri ri-arrow-left-line me-1"></i> Back to Equipment Rental
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-xl-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Current Settings</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <span>Security Deposit</span>
          <strong>TZS {{ number_format($config->security_deposit ?? 50000) }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Max Rental Hours</span>
          <strong>{{ $config->max_rental_hours ?? 4 }} hours</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Late Fee/Hour</span>
          <strong>TZS {{ number_format($config->late_fee_per_hour ?? 5000) }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Grace Period</span>
          <strong>{{ $config->grace_period_minutes ?? 15 }} minutes</strong>
        </div>
        <hr>
        <div class="mb-2">
          <small class="text-muted">Deposit Required:</small>
          <strong class="float-end">{{ ($config->require_deposit ?? true) ? 'Yes' : 'No' }}</strong>
        </div>
        <div class="mb-2">
          <small class="text-muted">Extensions Allowed:</small>
          <strong class="float-end">{{ ($config->allow_extensions ?? true) ? 'Yes' : 'No' }}</strong>
        </div>
        <div class="mb-2">
          <small class="text-muted">Auto-charge Late:</small>
          <strong class="float-end">{{ ($config->auto_charge_late ?? true) ? 'Yes' : 'No' }}</strong>
        </div>
        <hr>
        <p class="text-muted small mb-0">
          <i class="icon-base ri ri-time-line me-1"></i>
          Last updated: {{ $config->updated_at ? $config->updated_at->format('d M Y, H:i') : 'Never' }}
        </p>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-header">
        <h5 class="mb-0">Related Pages</h5>
      </div>
      <div class="card-body">
        <a href="{{ route('golf-services.equipment-rental') }}" class="btn btn-outline-primary w-100 mb-2">
          <i class="icon-base ri ri-shopping-bag-line me-1"></i> Equipment Rental
        </a>
        <a href="{{ route('golf-services.equipment-sales') }}" class="btn btn-outline-secondary w-100">
          <i class="icon-base ri ri-shopping-cart-line me-1"></i> Equipment Sales
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('rentalConfigForm')?.addEventListener('submit', function(e) {
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
      showSuccess('Rental configuration saved successfully!').then(() => location.reload());
    } else {
      showError(data.message || 'Failed to save configuration');
    }
  })
  .catch(err => {
    console.error(err);
    showError('Error saving configuration: ' + err.message);
  });
});
</script>
@endpush

