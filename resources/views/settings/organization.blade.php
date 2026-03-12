@extends('settings._layout-base')

@section('title', 'Organization Settings')
@section('description', 'Organization Settings - Golf Club Management System')

@php
  $settingsFile = storage_path('app/organization_settings.json');
  $settings = file_exists($settingsFile) ? json_decode(file_get_contents($settingsFile), true) ?? [] : [];
@endphp

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Settings /</span> Organization Settings
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
              <i class="icon-base ri ri-building-line me-2"></i>Organization Settings
            </h4>
            <p class="mb-0 opacity-75">Configure your golf club's basic information and branding</p>
          </div>
          <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('settings.communication') }}" class="btn btn-light">
              <i class="icon-base ri ri-mail-settings-line me-1"></i>Communication
            </a>
            <a href="{{ route('settings.system-health') }}" class="btn btn-outline-light">
              <i class="icon-base ri ri-heart-pulse-line me-1"></i>System Health
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<form action="{{ route('settings.organization.update') }}" method="POST" enctype="multipart/form-data" id="organizationForm">
  @csrf
  @method('PUT')
  
  <div class="row">
    <!-- Organization Logo & Branding -->
    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
          <h5 class="mb-0 text-white"><i class="icon-base ri ri-image-line me-2"></i>Logo & Branding</h5>
        </div>
        <div class="card-body text-center">
          <!-- Logo Preview -->
          <div class="mb-4">
            <div class="position-relative d-inline-block">
              @if(isset($settings['logo']) && $settings['logo'])
                <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Organization Logo" id="logoPreview" class="rounded" style="max-width: 200px; max-height: 200px; object-fit: contain; border: 3px solid #940000; padding: 10px; background: #fff;">
              @else
                <div id="logoPreview" class="rounded d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; border: 3px dashed #ccc; background: #f8f9fa;">
                  <div class="text-center text-muted">
                    <i class="icon-base ri ri-image-add-line" style="font-size: 48px;"></i>
                    <p class="mb-0 mt-2">No Logo</p>
                  </div>
                </div>
              @endif
              <button type="button" class="btn btn-sm btn-primary rounded-circle position-absolute" style="bottom: 5px; right: 5px; width: 40px; height: 40px;" onclick="document.getElementById('logo').click()">
                <i class="icon-base ri ri-camera-line"></i>
              </button>
            </div>
          </div>
          
          <input type="file" class="d-none" id="logo" name="logo" accept="image/jpeg,image/png,image/gif,image/svg+xml" onchange="previewLogo(this)">
          
          <div class="d-grid gap-2">
            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('logo').click()">
              <i class="icon-base ri ri-upload-line me-1"></i>Upload Logo
            </button>
            @if(isset($settings['logo']) && $settings['logo'])
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeLogo()">
              <i class="icon-base ri ri-delete-bin-line me-1"></i>Remove Logo
            </button>
            @endif
          </div>
          <small class="text-muted d-block mt-2">Recommended: 200x200px, PNG/SVG</small>
          
          <!-- Favicon -->
          <hr class="my-4">
          <h6 class="fw-bold mb-3">Favicon</h6>
          <div class="mb-3">
            @if(isset($settings['favicon']) && $settings['favicon'])
              <img src="{{ asset('storage/' . $settings['favicon']) }}" alt="Favicon" id="faviconPreview" style="width: 32px; height: 32px;">
            @else
              <div id="faviconPreview" class="d-inline-flex align-items-center justify-content-center rounded" style="width: 32px; height: 32px; border: 2px dashed #ccc; background: #f8f9fa;">
                <i class="icon-base ri ri-image-line text-muted"></i>
              </div>
            @endif
          </div>
          <input type="file" class="d-none" id="favicon" name="favicon" accept="image/x-icon,image/png" onchange="previewFavicon(this)">
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('favicon').click()">
            <i class="icon-base ri ri-upload-line me-1"></i>Upload Favicon
          </button>
          <small class="text-muted d-block mt-2">ICO or PNG, 32x32px</small>
        </div>
      </div>
      
      <!-- Theme Colors -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="icon-base ri ri-palette-line me-2"></i>Theme Colors</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Primary Color</label>
            <div class="input-group">
              <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ $settings['primary_color'] ?? '#940000' }}" style="width: 60px;">
              <input type="text" class="form-control" id="primary_color_text" value="{{ $settings['primary_color'] ?? '#940000' }}" onchange="document.getElementById('primary_color').value = this.value">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Secondary Color</label>
            <div class="input-group">
              <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ $settings['secondary_color'] ?? '#b30000' }}" style="width: 60px;">
              <input type="text" class="form-control" id="secondary_color_text" value="{{ $settings['secondary_color'] ?? '#b30000' }}" onchange="document.getElementById('secondary_color').value = this.value">
            </div>
          </div>
          <div class="alert alert-info mb-0">
            <small><i class="icon-base ri ri-information-line me-1"></i>Colors affect headers, buttons, and accents</small>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Basic Information -->
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
          <h5 class="mb-0 text-white"><i class="icon-base ri ri-information-line me-2"></i>Basic Information</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="org_name" name="org_name" value="{{ $settings['org_name'] ?? 'Golf Club' }}" placeholder="Organization Name" required />
                <label for="org_name">Organization Name *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="org_short_name" name="org_short_name" value="{{ $settings['org_short_name'] ?? '' }}" placeholder="Short Name" />
                <label for="org_short_name">Short Name / Abbreviation</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="email" class="form-control" id="org_email" name="org_email" value="{{ $settings['org_email'] ?? '' }}" placeholder="Email" />
                <label for="org_email">Email Address</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="tel" class="form-control" id="org_phone" name="org_phone" value="{{ $settings['org_phone'] ?? '' }}" placeholder="Phone" />
                <label for="org_phone">Phone Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="tel" class="form-control" id="org_mobile" name="org_mobile" value="{{ $settings['org_mobile'] ?? '' }}" placeholder="Mobile" />
                <label for="org_mobile">Mobile Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="url" class="form-control" id="org_website" name="org_website" value="{{ $settings['org_website'] ?? '' }}" placeholder="Website" />
                <label for="org_website">Website URL</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="org_address" name="org_address" placeholder="Address" style="height: 80px">{{ $settings['org_address'] ?? '' }}</textarea>
                <label for="org_address">Physical Address</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="org_city" name="org_city" value="{{ $settings['org_city'] ?? '' }}" placeholder="City" />
                <label for="org_city">City</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="org_region" name="org_region" value="{{ $settings['org_region'] ?? '' }}" placeholder="Region/State" />
                <label for="org_region">Region / State</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="org_country" name="org_country" value="{{ $settings['org_country'] ?? 'Tanzania' }}" placeholder="Country" />
                <label for="org_country">Country</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="org_postal_code" name="org_postal_code" value="{{ $settings['org_postal_code'] ?? '' }}" placeholder="Postal Code" />
                <label for="org_postal_code">Postal Code / P.O. Box</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="org_tin" name="org_tin" value="{{ $settings['org_tin'] ?? '' }}" placeholder="TIN" />
                <label for="org_tin">Tax Identification Number (TIN)</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Advanced Settings -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="icon-base ri ri-settings-4-line me-2"></i>Advanced Settings</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="timezone" name="timezone">
                  <option value="Africa/Dar_es_Salaam" {{ ($settings['timezone'] ?? '') == 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Africa/Dar_es_Salaam (EAT)</option>
                  <option value="Africa/Nairobi" {{ ($settings['timezone'] ?? '') == 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT)</option>
                  <option value="UTC" {{ ($settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                </select>
                <label for="timezone">Timezone</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="currency" name="currency">
                  <option value="TZS" {{ ($settings['currency'] ?? 'TZS') == 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                  <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                  <option value="KES" {{ ($settings['currency'] ?? '') == 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                  <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                </select>
                <label for="currency">Currency</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="date_format" name="date_format">
                  <option value="d/m/Y" {{ ($settings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                  <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                  <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                  <option value="d M Y" {{ ($settings['date_format'] ?? '') == 'd M Y' ? 'selected' : '' }}>DD Mon YYYY</option>
                </select>
                <label for="date_format">Date Format</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="time_format" name="time_format">
                  <option value="H:i" {{ ($settings['time_format'] ?? 'H:i') == 'H:i' ? 'selected' : '' }}>24-hour (14:30)</option>
                  <option value="h:i A" {{ ($settings['time_format'] ?? '') == 'h:i A' ? 'selected' : '' }}>12-hour (2:30 PM)</option>
                </select>
                <label for="time_format">Time Format</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="language" name="language">
                  <option value="en" {{ ($settings['language'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                  <option value="sw" {{ ($settings['language'] ?? '') == 'sw' ? 'selected' : '' }}>Swahili</option>
                </select>
                <label for="language">Language</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="{{ $settings['session_timeout'] ?? 60 }}" min="5" max="480" />
                <label for="session_timeout">Session Timeout (minutes)</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Golf Club Specific Settings -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="icon-base ri ri-golf-ball-line me-2"></i>Golf Club Settings</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="membership_fee" name="membership_fee" value="{{ $settings['membership_fee'] ?? 0 }}" min="0" step="1000" />
                <label for="membership_fee">Annual Membership Fee (TZS)</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="guest_fee" name="guest_fee" value="{{ $settings['guest_fee'] ?? 0 }}" min="0" step="1000" />
                <label for="guest_fee">Guest Fee (TZS)</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="time" class="form-control" id="opening_time" name="opening_time" value="{{ $settings['opening_time'] ?? '06:00' }}" />
                <label for="opening_time">Opening Time</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="time" class="form-control" id="closing_time" name="closing_time" value="{{ $settings['closing_time'] ?? '18:00' }}" />
                <label for="closing_time">Closing Time</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="driving_range_bays" name="driving_range_bays" value="{{ $settings['driving_range_bays'] ?? 20 }}" min="1" />
                <label for="driving_range_bays">Driving Range Bays</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="course_holes" name="course_holes" value="{{ $settings['course_holes'] ?? 18 }}" min="9" max="36" />
                <label for="course_holes">Course Holes</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="ball_bucket_size" name="ball_bucket_size" value="{{ $settings['ball_bucket_size'] ?? 50 }}" min="10" />
                <label for="ball_bucket_size">Ball Bucket Size</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="allow_guest_bookings" name="allow_guest_bookings" {{ ($settings['allow_guest_bookings'] ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="allow_guest_bookings">Allow Guest Bookings</label>
              </div>
              <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="require_member_card" name="require_member_card" {{ ($settings['require_member_card'] ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="require_member_card">Require Member Card for Transactions</label>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="send_sms_notifications" name="send_sms_notifications" {{ ($settings['send_sms_notifications'] ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="send_sms_notifications">Send SMS Notifications</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Receipt & Invoice Settings -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="icon-base ri ri-file-list-3-line me-2"></i>Receipt & Invoice Settings</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="receipt_prefix" name="receipt_prefix" value="{{ $settings['receipt_prefix'] ?? 'RCP' }}" />
                <label for="receipt_prefix">Receipt Number Prefix</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV' }}" />
                <label for="invoice_prefix">Invoice Number Prefix</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="receipt_footer" name="receipt_footer" placeholder="Receipt Footer" style="height: 80px">{{ $settings['receipt_footer'] ?? 'Thank you for visiting our Golf Club!' }}</textarea>
                <label for="receipt_footer">Receipt Footer Text</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="terms_conditions" name="terms_conditions" placeholder="Terms & Conditions" style="height: 100px">{{ $settings['terms_conditions'] ?? '' }}</textarea>
                <label for="terms_conditions">Terms & Conditions (for invoices)</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Save Button -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
              <h6 class="mb-0 fw-bold">Save Organization Settings</h6>
              <small class="text-muted">All changes will be applied immediately</small>
            </div>
            <div class="d-flex gap-2">
              <button type="reset" class="btn btn-outline-secondary">
                <i class="icon-base ri ri-refresh-line me-1"></i>Reset
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ri ri-save-line me-1"></i>Save Settings
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
// Preview logo before upload
function previewLogo(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById('logoPreview');
      if (preview.tagName === 'IMG') {
        preview.src = e.target.result;
      } else {
        preview.outerHTML = '<img src="' + e.target.result + '" alt="Logo Preview" id="logoPreview" class="rounded" style="max-width: 200px; max-height: 200px; object-fit: contain; border: 3px solid #940000; padding: 10px; background: #fff;">';
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Preview favicon before upload
function previewFavicon(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById('faviconPreview');
      if (preview.tagName === 'IMG') {
        preview.src = e.target.result;
      } else {
        preview.outerHTML = '<img src="' + e.target.result + '" alt="Favicon Preview" id="faviconPreview" style="width: 32px; height: 32px;">';
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Remove logo
function removeLogo() {
  if (confirm('Are you sure you want to remove the logo?')) {
    document.getElementById('logo').value = '';
    const preview = document.getElementById('logoPreview');
    preview.outerHTML = '<div id="logoPreview" class="rounded d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; border: 3px dashed #ccc; background: #f8f9fa;"><div class="text-center text-muted"><i class="icon-base ri ri-image-add-line" style="font-size: 48px;"></i><p class="mb-0 mt-2">No Logo</p></div></div>';
    
    // Add hidden field to indicate logo removal
    const removeField = document.createElement('input');
    removeField.type = 'hidden';
    removeField.name = 'remove_logo';
    removeField.value = '1';
    document.getElementById('organizationForm').appendChild(removeField);
  }
}

// Sync color inputs
document.getElementById('primary_color').addEventListener('input', function() {
  document.getElementById('primary_color_text').value = this.value;
});

document.getElementById('secondary_color').addEventListener('input', function() {
  document.getElementById('secondary_color_text').value = this.value;
});
</script>
@endpush
