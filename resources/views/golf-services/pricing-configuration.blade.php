@extends('settings._layout-base')

@section('title', 'Pricing Configuration')
@section('description', 'Pricing Configuration - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Golf Services / Settings /</span> Pricing Configuration
</h4>

<div class="row">
  <div class="col-xl-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Driving Range Pricing</h5>
      </div>
      <div class="card-body">
        <form id="pricingConfigForm">
          @csrf
          
          <h6 class="mb-3">Session Rates</h6>
          <div class="row">
            <div class="col-md-4 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" value="{{ $config->hourly_rate ?? 5000 }}" />
                <label for="hourly_rate">Hourly Rate (TZS) - Legacy</label>
              </div>
              <small class="text-muted">Legacy field (not used)</small>
            </div>
            <div class="col-md-4 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="ball_limit_price" name="ball_limit_price" value="{{ $config->ball_limit_price ?? $config->hourly_rate ?? 5000 }}" />
                <label for="ball_limit_price">Ball Limit Price (TZS)</label>
              </div>
              <small class="text-muted">Price for ball limit session</small>
            </div>
            <div class="col-md-4 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="balls_limit_per_session" name="balls_limit_per_session" value="{{ $config->balls_limit_per_session ?? 50 }}" />
                <label for="balls_limit_per_session">Default Balls Limit</label>
              </div>
              <small class="text-muted">Default number of balls per session</small>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="bucket_price" name="bucket_price" value="{{ $config->bucket_price ?? 2000 }}" />
                <label for="bucket_price">Bucket Price (TZS)</label>
              </div>
              <small class="text-muted">Price per bucket of balls</small>
            </div>
            <div class="col-md-4 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="unlimited_price" name="unlimited_price" value="{{ $config->unlimited_price ?? 8000 }}" />
                <label for="unlimited_price">Unlimited (1hr) (TZS)</label>
              </div>
              <small class="text-muted">Unlimited balls for 1 hour</small>
            </div>
          </div>

          <hr class="my-4">

          <h6 class="mb-3">Customer Category Rates</h6>
          <div class="row">
            <div class="col-md-4 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="regular_rate" name="regular_rate" value="{{ $config->regular_rate ?? 5000 }}" />
                <label for="regular_rate">Regular Rate (TZS/hr)</label>
              </div>
              <small class="text-muted">Standard customer rate</small>
            </div>
            <div class="col-md-4 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="premium_rate" name="premium_rate" value="{{ $config->premium_rate ?? 7500 }}" />
                <label for="premium_rate">Premium Rate (TZS/hr)</label>
              </div>
              <small class="text-muted">Premium customer rate</small>
            </div>
            <div class="col-md-4 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="member_discount" name="member_discount" value="{{ $config->member_discount ?? 10 }}" />
                <label for="member_discount">Member Discount (%)</label>
              </div>
              <small class="text-muted">Discount for club members</small>
            </div>
          </div>

          <hr class="my-4">

          <h6 class="mb-3">Additional Fees</h6>
          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="late_fee_per_hour" name="late_fee_per_hour" value="{{ $config->late_fee_per_hour ?? 3000 }}" />
                <label for="late_fee_per_hour">Late Fee per Hour (TZS)</label>
              </div>
              <small class="text-muted">Fee charged for overstaying</small>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="equipment_damage_fee" name="equipment_damage_fee" value="{{ $config->equipment_damage_fee ?? 50000 }}" />
                <label for="equipment_damage_fee">Equipment Damage Fee (TZS)</label>
              </div>
              <small class="text-muted">Fee for damaged equipment</small>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="ri ri-save-line me-1"></i> Save Pricing
            </button>
            <a href="{{ route('golf-services.driving-range') }}" class="btn btn-label-secondary ms-2">
              Back to Driving Range
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-xl-4">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Current Pricing</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <span>Ball Limit Price</span>
          <strong>TZS {{ number_format($config->ball_limit_price ?? $config->hourly_rate ?? 5000) }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Default Balls Limit</span>
          <strong>{{ $config->balls_limit_per_session ?? 50 }} balls</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Bucket Price</span>
          <strong>TZS {{ number_format($config->bucket_price ?? 2000) }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Unlimited (1hr)</span>
          <strong>TZS {{ number_format($config->unlimited_price ?? 8000) }}</strong>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-3">
          <span>Member Discount</span>
          <strong>{{ $config->member_discount ?? 10 }}%</strong>
        </div>
        <hr>
        <p class="text-muted small mb-0">
          Last updated: {{ $config->updated_at ?? 'Never' }}
        </p>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-header">
        <h5 class="mb-0">Related Settings</h5>
      </div>
      <div class="card-body">
        <a href="{{ route('golf-services.range-configuration') }}" class="btn btn-outline-primary w-100 mb-2">
          <i class="ri ri-settings-3-line me-1"></i> Range Configuration
        </a>
        <a href="{{ route('golf-services.driving-range') }}" class="btn btn-outline-secondary w-100">
          <i class="ri ri-golf-ball-line me-1"></i> Driving Range
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('pricingConfigForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  
  fetch('{{ route("golf-services.driving-range.config") }}', {
    method: 'POST',
    body: new FormData(this),
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showSuccess('Pricing saved successfully!').then(() => location.reload());
    } else {
      showError('Error saving pricing');
    }
  });
});
</script>
@endpush


