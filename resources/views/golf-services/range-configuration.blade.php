@extends('settings._layout-base')

@section('title', 'Range Configuration')
@section('description', 'Range Configuration - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Golf Services / Settings /</span> Range Configuration
</h4>

<div class="row">
  <div class="col-xl-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Driving Range Settings</h5>
      </div>
      <div class="card-body">
        <form id="rangeConfigForm">
          @csrf
          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="total_bays" name="total_bays" value="{{ $config->total_bays ?? 20 }}" />
                <label for="total_bays">Total Driving Range Bays</label>
              </div>
              <small class="text-muted">Number of available bays for customers</small>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="balls_per_bucket" name="balls_per_bucket" value="{{ $config->balls_per_bucket ?? 50 }}" />
                <label for="balls_per_bucket">Balls per Bucket</label>
              </div>
              <small class="text-muted">Number of balls in each bucket</small>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="range_distance" name="range_distance" value="{{ $config->range_distance ?? 250 }}" />
                <label for="range_distance">Range Distance (yards)</label>
              </div>
              <small class="text-muted">Maximum distance of the driving range</small>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="max_session_hours" name="max_session_hours" value="{{ $config->max_session_hours ?? 4 }}" />
                <label for="max_session_hours">Maximum Session Hours</label>
              </div>
              <small class="text-muted">Maximum hours per session</small>
            </div>
          </div>

          <hr class="my-4">

          <h6 class="mb-3">Range Features</h6>
          <div class="row">
            <div class="col-md-4 mb-3">
              <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="has_roof" name="has_roof" {{ ($config->has_roof ?? true) ? 'checked' : '' }} />
                <label class="form-check-label" for="has_roof">Covered/Roofed Range</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="has_lighting" name="has_lighting" {{ ($config->has_lighting ?? true) ? 'checked' : '' }} />
                <label class="form-check-label" for="has_lighting">Lighting Available</label>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" id="has_tracking" name="has_tracking" {{ ($config->has_tracking ?? false) ? 'checked' : '' }} />
                <label class="form-check-label" for="has_tracking">Ball Flight Tracking</label>
              </div>
            </div>
          </div>

          <hr class="my-4">

          <h6 class="mb-3">Operating Hours</h6>
          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="time" class="form-control" id="opening_time" name="opening_time" value="{{ $config->opening_time ?? '06:00' }}" />
                <label for="opening_time">Opening Time</label>
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="time" class="form-control" id="closing_time" name="closing_time" value="{{ $config->closing_time ?? '22:00' }}" />
                <label for="closing_time">Closing Time</label>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary">
              <i class="ri ri-save-line me-1"></i> Save Configuration
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
        <h5 class="mb-0">Quick Info</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <span>Total Bays</span>
          <strong>{{ $config->total_bays ?? 20 }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Balls per Bucket</span>
          <strong>{{ $config->balls_per_bucket ?? 50 }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Range Distance</span>
          <strong>{{ $config->range_distance ?? 250 }} yards</strong>
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
        <a href="{{ route('golf-services.pricing-configuration') }}" class="btn btn-outline-primary w-100 mb-2">
          <i class="ri ri-money-dollar-circle-line me-1"></i> Pricing Configuration
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
document.getElementById('rangeConfigForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  
  fetch('{{ route("golf-services.driving-range.config") }}', {
    method: 'POST',
    body: new FormData(this),
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showSuccess('Configuration saved successfully!').then(() => location.reload());
    } else {
      showError('Error saving configuration');
    }
  });
});
</script>
@endpush


