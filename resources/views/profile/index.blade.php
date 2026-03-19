@extends('settings._layout-base')

@section('title', 'Account Settings')
@section('description', 'Update your password and phone number - Golf Club Management System')

@php
  $user = Auth::user();
@endphp

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">User /</span> Account Settings
</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="icon-base ri ri-checkbox-circle-line me-2"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <ul class="mb-0">
    @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
  <!-- Profile Card -->
  <div class="col-xl-4 col-lg-5 col-md-5">
    <div class="card mb-4">
      <div class="card-body text-center">
        <!-- Avatar Upload -->
        <div class="position-relative d-inline-block mb-3">
          @if($user && $user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #940000;">
          @else
            <div class="avatar-initial rounded-circle" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); width: 120px; height: 120px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
              <span class="text-white" style="font-size: 48px;">{{ $user ? strtoupper(substr($user->name, 0, 1)) : 'U' }}</span>
            </div>
          @endif
          <button type="button" class="btn btn-sm btn-primary rounded-circle position-absolute" style="bottom: 5px; right: 5px; width: 36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#avatarModal">
            <i class="icon-base ri ri-camera-line"></i>
          </button>
        </div>
        
        <h5 class="mb-1">{{ $user->name ?? 'User' }}</h5>
        <p class="text-body-secondary mb-2">{{ $user->email ?? '' }}</p>
        <span class="badge bg-label-primary mb-2">{{ ucfirst($user->role ?? 'Staff') }}</span>
        <span class="badge bg-label-success">Active</span>
      </div>
      <div class="card-footer bg-transparent border-top">
        <div class="row text-center">
          <div class="col-4">
            <h6 class="mb-0">{{ $user && $user->created_at ? $user->created_at->format('M Y') : '-' }}</h6>
            <small class="text-body-secondary">Member Since</small>
          </div>
          <div class="col-4">
            <h6 class="mb-0">{{ ucfirst($user->role ?? 'Staff') }}</h6>
            <small class="text-body-secondary">Role</small>
          </div>
          <div class="col-4">
            <h6 class="mb-0">{{ $user && $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Now' }}</h6>
            <small class="text-body-secondary">Last Login</small>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Role & Permissions -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-shield-user-line me-2"></i>Role & Permissions</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Current Role</label>
          <div class="d-flex align-items-center">
            <span class="badge bg-primary me-2" style="font-size: 14px;">{{ ucfirst($user->role ?? 'Staff') }}</span>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Permissions</label>
          <div class="d-flex flex-wrap gap-1">
            @php
              $permissions = [
                'admin' => ['All Access', 'User Management', 'Reports', 'Settings', 'Transactions', 'Members'],
                'manager' => ['Reports', 'Transactions', 'Members', 'Inventory'],
                'cashier' => ['Transactions', 'Top-ups', 'POS'],
                'ball_manager' => ['Ball Inventory', 'Issue Balls', 'Return Balls'],
                'staff' => ['View Dashboard', 'Basic Operations'],
              ];
              $userRole = $user->role ?? 'staff';
              $userPermissions = $permissions[$userRole] ?? $permissions['staff'];
            @endphp
            @foreach($userPermissions as $perm)
              <span class="badge bg-label-info">{{ $perm }}</span>
            @endforeach
          </div>
        </div>
        <div>
          <label class="form-label fw-semibold">Access Level</label>
          <div class="progress" style="height: 10px;">
            @php
              $accessLevels = ['staff' => 25, 'ball_manager' => 30, 'cashier' => 50, 'manager' => 75, 'admin' => 100];
              $accessLevel = $accessLevels[$userRole] ?? 25;
            @endphp
            <div class="progress-bar bg-primary" style="width: {{ $accessLevel }}%"></div>
          </div>
          <small class="text-body-secondary">{{ $accessLevel }}% System Access</small>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Profile Details -->
  <div class="col-xl-8 col-lg-7 col-md-7">
    <!-- Account Details -->
    <div class="card mb-4">
      <div class="card-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="mb-0 text-white"><i class="icon-base ri ri-user-settings-line me-2"></i>Personal Information</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Full Name" required />
                <label for="name">Full Name *</label>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="Email" required />
                <label for="email">Email Address *</label>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="Phone Number" required />
                <label for="phone">Phone Number *</label>
                @error('phone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="role" value="{{ ucfirst($user->role ?? 'Staff') }}" disabled />
                <label for="role">Role (Contact Admin to change)</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-save-line me-1"></i> Save Changes
          </button>
        </form>
      </div>
    </div>
    
    <!-- Security Settings -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-shield-check-line me-2"></i>Security Settings</h5>
      </div>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-3">
              <div>
                <h6 class="mb-1">Two-Factor Authentication</h6>
                <small class="text-body-secondary">Add an extra layer of security</small>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="twoFactor" {{ ($user->two_factor_enabled ?? false) ? 'checked' : '' }} disabled />
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-3">
              <div>
                <h6 class="mb-1">Login Notifications</h6>
                <small class="text-body-secondary">Get notified on new logins</small>
              </div>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="loginNotify" checked disabled />
              </div>
            </div>
          </div>
        </div>
        
        <div class="alert alert-info d-flex align-items-center mb-0">
          <i class="icon-base ri ri-information-line me-2"></i>
          <div>
            <strong>Security Status:</strong> Your account is secured. Last password change: {{ $user && $user->password_changed_at ? $user->password_changed_at->diffForHumans() : 'Never' }}
          </div>
        </div>
      </div>
    </div>
    
    <!-- Change Password -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="icon-base ri ri-lock-line me-2"></i>Change Password</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('profile.password') }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-12 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Current Password" required />
                <label for="current_password">Current Password *</label>
                @error('current_password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="New Password" required />
                <label for="password">New Password *</label>
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <small class="text-muted">Minimum 8 characters</small>
            </div>
            <div class="col-md-6 mb-4">
              <div class="form-floating form-floating-outline">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required />
                <label for="password_confirmation">Confirm New Password *</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-warning">
            <i class="icon-base ri ri-lock-password-line me-1"></i> Update Password
          </button>
        </form>
      </div>
    </div>
    
    <!-- Login Activity -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="icon-base ri ri-history-line me-2"></i>Recent Login Activity</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Date & Time</th>
                <th>IP Address</th>
                <th>Device/Browser</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ now()->format('d M Y, H:i') }}</td>
                <td>{{ request()->ip() }}</td>
                <td>{{ Str::limit(request()->userAgent(), 30) }}</td>
                <td><span class="badge bg-label-success">Current Session</span></td>
              </tr>
              <tr>
                <td colspan="4" class="text-center text-body-secondary py-3">No previous login records</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
  </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-camera-line me-2"></i>Update Profile Photo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body text-center">
          <div class="mb-4">
            @if($user && $user->avatar)
              <img src="{{ asset('storage/' . $user->avatar) }}" alt="Current Avatar" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #940000;">
            @else
              <div class="avatar-initial rounded-circle mx-auto mb-3" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); width: 150px; height: 150px; display: flex; align-items: center; justify-content: center;">
                <span class="text-white" style="font-size: 60px;">{{ $user ? strtoupper(substr($user->name, 0, 1)) : 'U' }}</span>
              </div>
            @endif
          </div>
          
          <div class="mb-3">
            <label for="avatar" class="form-label">Choose a new photo</label>
            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg,image/gif" required />
            @error('avatar')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-2">Allowed formats: JPG, PNG, GIF. Max size: 2MB</small>
          </div>
          
          <div id="avatarPreview" class="d-none mb-3">
            <p class="text-body-secondary mb-2">Preview:</p>
            <img id="previewImage" src="" alt="Preview" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #940000;">
          </div>
        </div>
        <div class="modal-footer">
          @if($user && $user->avatar)
          <button type="button" class="btn btn-outline-danger me-auto" onclick="document.getElementById('deleteAvatarForm').submit();">
            <i class="icon-base ri ri-delete-bin-line me-1"></i> Remove Photo
          </button>
          @endif
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-upload-line me-1"></i> Upload Photo
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Avatar Form -->
<form id="deleteAvatarForm" action="{{ route('profile.avatar.delete') }}" method="POST" class="d-none">
  @csrf
  @method('DELETE')
</form>

@push('scripts')
<script>
// Avatar preview
document.getElementById('avatar')?.addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('previewImage').src = event.target.result;
      document.getElementById('avatarPreview').classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  }
});
</script>
@endpush
@endsection
