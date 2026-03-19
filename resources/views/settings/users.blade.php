@extends('settings._layout-base')

@section('title', 'User Management')
@section('description', 'Register and manage club staff and their roles.')

@section('content')
<!-- Remix Icons -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

<style>
    :root {
        --user-primary: #940000;
        --user-secondary: #f8f9fa;
        --user-success: #10b981;
        --user-danger: #ef4444;
        --user-warning: #f59e0b;
        --user-info: #3b82f6;
    }

    .user-card {
        border-radius: 1.25rem !important;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }

    .role-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-admin { background-color: rgba(148, 0, 0, 0.1); color: #940000; }
    .role-manager { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .role-reception { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .role-counter { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .role-storekeeper { background-color: rgba(107, 114, 128, 0.1); color: #6b7280; }
    .role-chef { background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .role-waiter { background-color: rgba(236, 72, 153, 0.1); color: #ec4899; }

    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        background: #f0f2f5;
        color: #940000;
        object-fit: cover;
    }

    .premium-modal {
        border-radius: 20px;
        overflow: hidden;
    }

    .premium-modal-header {
        background: linear-gradient(135deg, #940000 0%, #d40000 100%);
        padding: 1.5rem;
    }

    .form-floating > .form-control:focus,
    .form-floating > .form-control:not(:placeholder-shown) {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold mb-1 text-dark">Staff Directory</h2>
        <p class="text-muted mb-0">Manage all registered users and their system access levels.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#userModal">
            <i class="ri-user-add-line me-1"></i> Register New Staff
        </button>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card user-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="stats-icon bg-label-primary text-primary me-3">
                        <i class="ri-group-line"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold">{{ $users->count() }}</h3>
                        <small class="text-muted">Total Users</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card user-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="stats-icon bg-label-success text-success me-3">
                        <i class="ri-shield-user-line"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold">{{ $users->where('role', 'admin')->count() }}</h3>
                        <small class="text-muted">Administrators</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card user-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="stats-icon bg-label-info text-info me-3">
                        <i class="ri-user-star-line"></i>
                    </div>
                    <div>
                        <h3 class="mb-0 fw-bold">{{ $users->whereNotIn('role', ['admin'])->count() }}</h3>
                        <small class="text-muted">Support Staff</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card user-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="stats-icon bg-label-warning text-warning me-3">
                        <i class="ri-time-line"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $users->sortByDesc('created_at')->first()?->created_at->format('M d') ?? '-' }}</h6>
                        <small class="text-muted">Last Onboarding</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card user-card">
    <div class="card-header bg-white border-bottom py-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0 fw-bold">Active Staff Accounts</h5>
            </div>
            <div class="col-auto">
                <div class="input-group input-group-merge" style="width: 250px;">
                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                    <input type="text" class="form-control" placeholder="Search staff..." id="userSearch">
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Staff Member</th>
                        <th>Account Email</th>
                        <th>Role / Access</th>
                        <th>Phone Number</th>
                        <th>Joined Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="user-avatar me-3 shadow-xs">
                                @else
                                    <div class="user-avatar me-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <small class="text-muted">ID #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="role-badge role-{{ $user->role }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>{{ $user->phone ?? '---' }}</td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="text-end pe-4">
                            @if($user->id !== Auth::id())
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon btn-label-secondary" data-bs-toggle="dropdown">
                                    <i class="ri-more-2-line"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->phone }}')"><i class="ri-edit-line me-2 text-primary"></i>Edit Profile</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="resetUserPassword({{ $user->id }}, '{{ addslashes($user->name) }}')"><i class="ri-lock-password-line me-2 text-warning"></i>Reset Password</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')"><i class="ri-delete-bin-line me-2"></i>Remove Access</a></li>
                                </ul>
                            </div>
                            @else
                                <span class="badge bg-label-primary px-3 rounded-pill">Current User</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg premium-modal">
            <div class="modal-header premium-modal-header border-0">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-white text-danger me-3">
                        <i class="ri-user-add-line" id="modalIcon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold" id="modalTitle">Register New Staff</h5>
                        <p class="text-white opacity-75 mb-0 small">Fill in the details to create a new system account.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                @csrf
                <input type="hidden" id="userId" name="id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Full Name</label>
                            <input type="text" class="form-control" name="name" id="userName" placeholder="e.g. John Doe" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Email Address</label>
                            <input type="email" class="form-control" name="email" id="userEmail" placeholder="e.g. john@golfsystem.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="userPhone" placeholder="+255 ..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">System Role</label>
                            <select class="form-select" name="role" id="userRole" required>
                                <option value="reception">Reception</option>
                                <option value="counter">Counter</option>
                                <option value="storekeeper">Storekeeper</option>
                                <option value="chef">Chef</option>
                                <option value="waiter">Waiter</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div id="passwordSection" class="row g-3 p-0 m-0">
                            <hr class="my-3 opacity-25">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Password</label>
                                <input type="password" class="form-control" name="password" id="userPassword" placeholder="••••••••">
                                <small class="text-muted" id="passwordHint">Minimum 8 characters.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation" id="userPasswordConfirm" placeholder="••••••••">
                            </div>
                        </div>
                        <div id="smsNotice" class="col-12 mt-3">
                            <div class="alert alert-info border-0 rounded-4 d-flex align-items-center mb-0">
                                <i class="ri-information-line fs-4 me-3"></i>
                                <div class="small">
                                    <strong>Auto-generated Credentials:</strong> A simple password will be generated and sent to the staff member's phone via SMS immediately.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 border-0">
                    <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="submitBtn">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    const userForm = document.getElementById('userForm');
    
    // Reset modal on close
    document.getElementById('userModal').addEventListener('hidden.bs.modal', function () {
        userForm.reset();
        document.getElementById('userId').value = '';
        document.getElementById('modalTitle').innerText = 'Register New Staff';
        document.getElementById('submitBtn').innerText = 'Create Account';
        document.getElementById('passwordSection').style.display = 'none';
        document.getElementById('smsNotice').style.display = 'block';
        document.getElementById('userPassword').required = false;
        document.getElementById('passwordHint').innerText = '';
    });

    // Handle modal show for registration
    document.getElementById('userModal').addEventListener('show.bs.modal', function() {
        if (!document.getElementById('userId').value) {
            document.getElementById('passwordSection').style.display = 'none';
            document.getElementById('smsNotice').style.display = 'block';
            document.getElementById('userPassword').required = false;
        }
    });

    function editUser(id, name, email, role, phone) {
        document.getElementById('userId').value = id;
        document.getElementById('userName').value = name;
        document.getElementById('userEmail').value = email;
        document.getElementById('userRole').value = role;
        document.getElementById('userPhone').value = phone === 'null' ? '' : phone;
        
        document.getElementById('modalTitle').innerText = 'Edit Staff Member';
        document.getElementById('submitBtn').innerText = 'Update Account';
        
        document.getElementById('passwordSection').style.display = 'flex';
        document.getElementById('smsNotice').style.display = 'none';
        document.getElementById('userPassword').required = false;
        document.getElementById('passwordHint').innerText = 'Leave blank to keep current password.';
        
        userModal.show();
    }

    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('userId').value;
        const formData = new FormData(this);
        const url = id ? `/settings/users/${id}` : '/settings/users';
        
        const method = id ? 'PUT' : 'POST';
        
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userModal.hide();
                Swal.fire('Success!', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error!', data.message || 'Something went wrong', 'error');
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error!', 'Validation failed or connection lost.', 'error');
        });
    });

    function deleteUser(id, name) {
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to remove all system access for ${name}. This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#940000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove account',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/settings/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                });
            }
        });
    }

    function resetUserPassword(id, name) {
        Swal.fire({
            title: 'Reset Password?',
            text: `This will generate a new 6-digit password for ${name} and send it to them via SMS.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Reset & Send SMS',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/settings/users/${id}/reset-password`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(response.statusText);
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.success) {
                    Swal.fire({
                        title: 'Password Reset!',
                        html: `New Password: <strong class="fs-4 text-primary">${result.value.password}</strong><br><small class="text-muted">${result.value.message}</small>`,
                        icon: 'success'
                    });
                } else {
                    Swal.fire('Error!', result.value.message, 'error');
                }
            }
        });
    }

    // Live search
    document.getElementById('userSearch').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#userTableBody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });
</script>
@endpush
