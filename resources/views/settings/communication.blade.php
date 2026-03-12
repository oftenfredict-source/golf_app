@extends('settings._layout-base')

@section('title', 'Communication Settings')
@section('description', 'Communication Settings - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Settings /</span> Communication Settings
</h4>

<!-- Header Card -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%);">
      <div class="card-body text-white p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="mb-2 text-white fw-bold">
              <i class="icon-base ri ri-mail-settings-line me-2"></i>Communication Settings
            </h4>
            <p class="mb-0 opacity-75">Configure SMS and Email services for system notifications</p>
          </div>
          <div class="d-flex gap-2 mt-3 mt-md-0">
            <button class="btn btn-light" onclick="checkAllConnections()">
              <i class="icon-base ri ri-refresh-line me-1"></i>Check Connections
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

<!-- Connection Status -->
<div class="row mb-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="mb-3">
          <i class="icon-base ri ri-message-2-line" style="font-size: 48px; color: #940000;"></i>
        </div>
        <h5 class="mb-2">SMS Service</h5>
        <div id="smsStatus" class="badge bg-label-warning mb-3 px-3 py-2">
          <i class="icon-base ri ri-loader-4-line me-1"></i>Checking...
        </div>
        <br>
        <button class="btn btn-sm btn-outline-primary" onclick="checkSMSStatus()">
          <i class="icon-base ri ri-refresh-line me-1"></i>Refresh Status
        </button>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body text-center">
        <div class="mb-3">
          <i class="icon-base ri ri-mail-line" style="font-size: 48px; color: #940000;"></i>
        </div>
        <h5 class="mb-2">Email Service</h5>
        <div id="emailStatus" class="badge bg-label-warning mb-3 px-3 py-2">
          <i class="icon-base ri ri-loader-4-line me-1"></i>Checking...
        </div>
        <br>
        <button class="btn btn-sm btn-outline-danger" onclick="checkEmailStatus()">
          <i class="icon-base ri ri-refresh-line me-1"></i>Refresh Status
        </button>
      </div>
    </div>
  </div>
</div>

<form id="communicationSettingsForm">
  @csrf
  <div class="row">
    <!-- SMS Settings -->
    <div class="col-lg-6">
      <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
          <h5 class="mb-0 text-white">
            <i class="icon-base ri ri-message-2-line me-2"></i>SMS Gateway Configuration
          </h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12">
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="sms_enabled" name="sms_enabled" />
                <label class="form-check-label" for="sms_enabled">
                  <strong>Enable SMS Service</strong>
                </label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="sms_username" name="sms_username" placeholder="SMS Username" />
                <label for="sms_username">SMS Username</label>
              </div>
              <small class="text-muted">SMS gateway API username</small>
            </div>
            <div class="col-12">
              <div class="input-group">
                <div class="form-floating form-floating-outline flex-grow-1">
                  <input type="password" class="form-control" id="sms_password" name="sms_password" placeholder="SMS Password" />
                  <label for="sms_password">SMS Password</label>
                </div>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('sms_password')">
                  <i class="icon-base ri ri-eye-line" id="sms_password_icon"></i>
                </button>
              </div>
              <small class="text-muted">SMS gateway API password</small>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="sms_from" name="sms_from" placeholder="Sender Name" />
                <label for="sms_from">Sender Name</label>
              </div>
              <small class="text-muted">e.g., GolfClub</small>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="url" class="form-control" id="sms_url" name="sms_url" placeholder="API URL" />
                <label for="sms_url">SMS API URL</label>
              </div>
            </div>
            <div class="col-12">
              <div class="card bg-light">
                <div class="card-body">
                  <label class="form-label fw-bold mb-2">Test SMS</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="icon-base ri ri-phone-line"></i></span>
                    <input type="text" class="form-control" id="testSmsPhone" placeholder="255712345678" />
                    <button type="button" class="btn btn-primary" onclick="testSMS()">
                      <i class="icon-base ri ri-send-plane-line me-1"></i>Send Test
                    </button>
                  </div>
                  <small class="text-muted">Format: 255XXXXXXXXX (12 digits)</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Email Settings -->
    <div class="col-lg-6">
      <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
          <h5 class="mb-0 text-white">
            <i class="icon-base ri ri-mail-line me-2"></i>Email (SMTP) Configuration
          </h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="mail_mailer" name="mail_mailer">
                  <option value="smtp">SMTP</option>
                  <option value="sendmail">Sendmail</option>
                </select>
                <label for="mail_mailer">Mailer Type</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="mail_host" name="mail_host" placeholder="smtp.gmail.com" />
                <label for="mail_host">SMTP Host</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="mail_port" name="mail_port" placeholder="587" />
                <label for="mail_port">SMTP Port</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="mail_encryption" name="mail_encryption">
                  <option value="tls">TLS</option>
                  <option value="ssl">SSL</option>
                </select>
                <label for="mail_encryption">Encryption</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <input type="email" class="form-control" id="mail_username" name="mail_username" placeholder="email@gmail.com" />
                <label for="mail_username">SMTP Username</label>
              </div>
            </div>
            <div class="col-12">
              <div class="input-group">
                <div class="form-floating form-floating-outline flex-grow-1">
                  <input type="password" class="form-control" id="mail_password" name="mail_password" placeholder="Password" />
                  <label for="mail_password">SMTP Password</label>
                </div>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('mail_password')">
                  <i class="icon-base ri ri-eye-line" id="mail_password_icon"></i>
                </button>
              </div>
              <small class="text-muted">For Gmail, use App Password</small>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" placeholder="noreply@golfclub.com" />
                <label for="mail_from_address">From Email</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" placeholder="Golf Club" />
                <label for="mail_from_name">From Name</label>
              </div>
            </div>
            <div class="col-12">
              <div class="card bg-light">
                <div class="card-body">
                  <label class="form-label fw-bold mb-2">Test Email</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="icon-base ri ri-mail-line"></i></span>
                    <input type="email" class="form-control" id="testEmailAddress" placeholder="test@example.com" />
                    <button type="button" class="btn btn-danger" onclick="testEmail()">
                      <i class="icon-base ri ri-send-plane-line me-1"></i>Send Test
                    </button>
                  </div>
                </div>
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
              <h6 class="mb-0 fw-bold">Ready to save your communication settings?</h6>
              <small class="text-muted">All SMS and Email configurations will be updated</small>
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('settings.organization') }}" class="btn btn-outline-secondary">
                <i class="icon-base ri ri-close-line me-1"></i>Cancel
              </a>
              <button type="button" class="btn btn-primary" onclick="saveCommunicationSettings()">
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
const csrfToken = '{{ csrf_token() }}';

// Load settings on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCommunicationSettings();
    setTimeout(checkAllConnections, 1000);
});

function loadCommunicationSettings() {
    // Load from API/database
    const smsSettings = @json($smsSettings ?? null);
    
    if (smsSettings) {
        document.getElementById('sms_enabled').checked = smsSettings.enabled || false;
        document.getElementById('sms_username').value = smsSettings.username || '';
        document.getElementById('sms_password').value = smsSettings.password || '';
        document.getElementById('sms_from').value = smsSettings.sender_name || '';
        document.getElementById('sms_url').value = smsSettings.api_url || '';
    }
    
    // Fallback to localStorage for email settings (if not migrated yet)
    const settings = JSON.parse(localStorage.getItem('communicationSettings') || '{}');
    
    if (settings.email) {
        document.getElementById('mail_mailer').value = settings.email.mailer || 'smtp';
        document.getElementById('mail_host').value = settings.email.host || '';
        document.getElementById('mail_port').value = settings.email.port || '587';
        document.getElementById('mail_encryption').value = settings.email.encryption || 'tls';
        document.getElementById('mail_username').value = settings.email.username || '';
        document.getElementById('mail_password').value = settings.email.password || '';
        document.getElementById('mail_from_address').value = settings.email.from_address || '';
        document.getElementById('mail_from_name').value = settings.email.from_name || '';
    }
}

function saveCommunicationSettings() {
    const formData = {
        sms_username: document.getElementById('sms_username').value,
        sms_password: document.getElementById('sms_password').value,
        sms_from: document.getElementById('sms_from').value,
        sms_url: document.getElementById('sms_url').value,
        sms_enabled: document.getElementById('sms_enabled').checked,
        _token: csrfToken
    };
    
    // Show loading
    const saveBtn = document.querySelector('button[onclick="saveCommunicationSettings()"]');
    const originalText = saveBtn ? saveBtn.innerHTML : '';
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    }
    
    fetch('{{ route("settings.communication.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(r => r.json())
    .then(data => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        
        if (data.success) {
            alert('Communication settings saved successfully!');
            checkAllConnections();
        } else {
            alert('Error: ' + (data.message || 'Failed to save settings'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        alert('Error saving settings. Please try again.');
    });
}

function testSMS() {
    const phone = document.getElementById('testSmsPhone').value.trim();
    if (!phone) {
        alert('Please enter a phone number');
        return;
    }
    
    if (!/^255[0-9]{9}$/.test(phone)) {
        alert('Phone number must be in format: 255XXXXXXXXX (12 digits)');
        return;
    }
    
    const testBtn = document.querySelector('button[onclick="testSMS()"]');
    const originalText = testBtn ? testBtn.innerHTML : '';
    if (testBtn) {
        testBtn.disabled = true;
        testBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
    }
    
    fetch('{{ route("settings.communication.test-sms") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ phone: phone })
    })
    .then(r => r.json())
    .then(data => {
        if (testBtn) {
            testBtn.disabled = false;
            testBtn.innerHTML = originalText;
        }
        
        if (data.success) {
            alert('Test SMS sent successfully to ' + phone);
        } else {
            alert('Error: ' + (data.message || 'Failed to send test SMS'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (testBtn) {
            testBtn.disabled = false;
            testBtn.innerHTML = originalText;
        }
        alert('Error sending test SMS. Please try again.');
    });
}

function testEmail() {
    const email = document.getElementById('testEmailAddress').value.trim();
    if (!email) {
        alert('Please enter an email address');
        return;
    }
    
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert('Please enter a valid email address');
        return;
    }
    
    alert('Test email would be sent to: ' + email + '\n\nNote: SMTP configuration required.');
}

function checkSMSStatus() {
    const statusEl = document.getElementById('smsStatus');
    const smsUrl = document.getElementById('sms_url').value;
    const smsUsername = document.getElementById('sms_username').value;
    
    if (smsUrl && smsUsername) {
        statusEl.className = 'badge bg-label-success mb-3 px-3 py-2';
        statusEl.innerHTML = '<i class="icon-base ri ri-checkbox-circle-line me-1"></i>Configured';
    } else {
        statusEl.className = 'badge bg-label-warning mb-3 px-3 py-2';
        statusEl.innerHTML = '<i class="icon-base ri ri-error-warning-line me-1"></i>Not Configured';
    }
}

function checkEmailStatus() {
    const statusEl = document.getElementById('emailStatus');
    const mailHost = document.getElementById('mail_host').value;
    const mailUsername = document.getElementById('mail_username').value;
    
    if (mailHost && mailUsername) {
        statusEl.className = 'badge bg-label-success mb-3 px-3 py-2';
        statusEl.innerHTML = '<i class="icon-base ri ri-checkbox-circle-line me-1"></i>Configured';
    } else {
        statusEl.className = 'badge bg-label-warning mb-3 px-3 py-2';
        statusEl.innerHTML = '<i class="icon-base ri ri-error-warning-line me-1"></i>Not Configured';
    }
}

function checkAllConnections() {
    checkSMSStatus();
    checkEmailStatus();
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('ri-eye-line');
        icon.classList.add('ri-eye-off-line');
    } else {
        field.type = 'password';
        icon.classList.remove('ri-eye-off-line');
        icon.classList.add('ri-eye-line');
    }
}
</script>
@endpush
