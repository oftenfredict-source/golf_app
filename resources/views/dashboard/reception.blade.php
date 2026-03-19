@extends('settings._layout-base')

@section('title', 'Reception Workspace')
@section('description', 'Member registration, Top-ups, and Card Issuance.')

@section('content')
<!-- Remix Icons -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

<div class="row mb-5 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold mb-1 text-dark">Reception Desk</h2>
        <p class="text-muted mb-0">Quick access to member registration and transactional services.</p>
    </div>
</div>

<!-- Quick Action Stats -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-1">
                    <div class="avatar bg-label-primary rounded-3 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-user-add-line fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['members_today'] }}</h4>
                        <small class="text-muted">Registered Today</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-1">
                    <div class="avatar bg-label-success rounded-3 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-money-dollar-circle-line fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['topups_today'] }}</h4>
                        <small class="text-muted">Top-ups Today</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-1">
                    <div class="avatar bg-label-warning rounded-3 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-bank-card-line fs-4"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ $stats['cards_pending'] }}</h4>
                        <small class="text-muted">Cards Pending</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-1">
                    <div class="avatar bg-white bg-opacity-25 rounded-3 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-wallet-3-line fs-4 text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-white">TZS {{ number_format($stats['topup_amount_today']) }}</h4>
                        <small class="text-white-50">Total Revenue</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Workspace -->
<div class="row g-4 mb-5">
    <!-- Registration Section -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0">Member Registration</h5>
                <p class="text-muted small">Register new cardholders</p>
            </div>
            <div class="card-body p-4">
                <form id="memberForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="Member Name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" class="form-control" name="phone" required placeholder="255XXXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Membership Type</label>
                            <select class="form-select" name="membership_type">
                                <option value="standard">Standard (Silver)</option>
                                <option value="vip">VIP (Black)</option>
                                <option value="premier">Premier (Gold)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Initial Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" class="form-control" name="initial_balance" value="0">
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="has_full_access" value="1" checked id="fullAccessSwitch">
                                <label class="form-check-label fw-semibold" for="fullAccessSwitch">Issue Member Card</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="ri-user-add-line me-2"></i> Register Member
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Top-up Section -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0">Quick Top-up</h5>
                <p class="text-muted small">Add funds to member balance</p>
            </div>
            <div class="card-body p-4">
                <form id="topupForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Search Member (Name or Phone)</label>
                            <div class="position-relative">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                                    <input type="text" class="form-control" id="memberSearch" placeholder="Start typing..." autocomplete="off">
                                </div>
                                <div id="searchResults" class="list-group position-absolute w-100 shadow-lg mt-1" style="z-index: 1000; display: none;"></div>
                            </div>
                            <input type="hidden" name="member_id" id="targetMemberId">
                        </div>
                        
                        <div id="selectedMemberInfo" class="col-12" style="display: none;">
                            <div class="d-flex align-items-center p-3 rounded-4 bg-light border border-primary border-opacity-25">
                                <div class="avatar avatar-md bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" id="memberNameInitial">?</div>
                                <div>
                                    <h6 class="mb-0 fw-bold" id="selectedMemberName">---</h6>
                                    <small class="text-muted">Balance: <span class="fw-bold text-success" id="selectedMemberBalance">TZS 0</span></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger ms-auto rounded-circle" id="clearMember">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" class="form-control" name="amount" required placeholder="Min 1000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold" id="topupSubmitBtn" disabled>
                                <i class="ri-add-circle-line me-2"></i> Process Top-up
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Pending Cards & Recent Registrations -->
<div class="row g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4 pb-0 d-flex justify-content-between">
                <h5 class="fw-bold mb-0">Recent Registrations</h5>
                <a href="{{ route('payments.upi-management') }}" class="btn btn-sm btn-link">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Member</th>
                                <th>Card No</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentMembers as $member)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $member->name }}</div>
                                    <small class="text-muted">{{ $member->phone }}</small>
                                </td>
                                <td><code>{{ $member->card_number ?? '--' }}</code></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-label-{{ $member->status == 'active' ? 'success' : 'warning' }} rounded-pill px-2 mb-1" style="font-size: 0.7rem;">
                                            Account: {{ ucfirst($member->status) }}
                                        </span>
                                        @if($member->has_full_access)
                                            <span class="badge bg-label-{{ $member->card_status == 'issued' ? 'success' : ($member->card_status == 'ready' ? 'info' : 'warning') }} rounded-pill px-2" style="font-size: 0.7rem;">
                                                Card: {{ ucfirst(str_replace('_', ' ', $member->card_status)) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('payments.members.show', $member->id) }}" class="btn btn-sm btn-light">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 border-start border-warning border-5">
            <div class="card-header bg-transparent border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0 text-warning">Card Issuance Queue</h5>
                <p class="text-muted small">Manage card design, printing, and pickup</p>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush pt-2">
                    @forelse($pendingCards as $member)
                    <div class="list-group-item px-4 py-3 border-0 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar bg-label-warning rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">{{ $member->name }}</h6>
                                <small class="text-muted">Registered {{ $member->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                    <i class="ri-more-2-line"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('payments.generate-card', $member->ulid ?? $member->id) }}"><i class="ri-printer-line me-2"></i> Print Template</a></li>
                                    <li><a class="dropdown-item" href="{{ route('payments.members.show', $member->id) }}"><i class="ri-user-line me-2"></i> Member Profile</a></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-3">
                            <div class="small fw-bold">
                                @if($member->card_status == 'pending_design')
                                    <span class="text-warning"><i class="ri-time-line me-1"></i> Pending Design</span>
                                @elseif($member->card_status == 'printing')
                                    <span class="text-primary"><i class="ri-printer-line me-1"></i> Printing...</span>
                                @elseif($member->card_status == 'ready')
                                    <span class="text-success"><i class="ri-checkbox-circle-line me-1"></i> Ready for Pickup</span>
                                @endif
                            </div>
                            <div class="btn-group">
                                @if($member->card_status == 'pending_design')
                                    <button class="btn btn-xs btn-primary py-1 px-2" onclick="updateCardStatus('{{ $member->id }}', 'printing')">
                                        Mark Printing
                                    </button>
                                @elseif($member->card_status == 'printing')
                                    <button class="btn btn-xs btn-success py-1 px-2" onclick="updateCardStatus('{{ $member->id }}', 'ready')">
                                        Mark Ready (SMS)
                                    </button>
                                @elseif($member->card_status == 'ready')
                                    <button class="btn btn-xs btn-dark py-1 px-2" onclick="updateCardStatus('{{ $member->id }}', 'issued')">
                                        Issue to Member
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted small">No cards in the issuance queue.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-label-primary { background-color: rgba(67, 89, 113, 0.1) !important; color: #696cff !important; }
    .bg-label-success { background-color: rgba(113, 221, 55, 0.1) !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: rgba(255, 171, 0, 0.1) !important; color: #ffab00 !important; }
    .bg-label-danger { background-color: rgba(255, 62, 29, 0.1) !important; color: #ff3e1d !important; }
    .bg-label-info { background-color: rgba(3, 195, 236, 0.1) !important; color: #03c3ec !important; }
    
    .list-group-item:hover { background-color: #fcfcfc; }
    #searchResults .list-group-item { cursor: pointer; }
    #searchResults .list-group-item:hover { background-color: #f0f2f5; }
</style>
@endsection

@push('scripts')
<script>
    // Member Registration
    document.getElementById('memberForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ri-loader-4-line ri-spin me-2"></i> Registering...';

        fetch("{{ route('payments.members.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Registration Successful!',
                    text: data.message,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Print Card Now',
                    cancelButtonText: 'Done'
                }).then((result) => {
                    if (result.isConfirmed && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        location.reload();
                    }
                });
            } else {
                Swal.fire('Error', data.message || 'Something went wrong', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ri-user-add-line me-2"></i> Register Member';
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Connection lost or server error', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ri-user-add-line me-2"></i> Register Member';
        });
    });

    // Member Search for Top-up
    let searchTimeout;
    const memberSearch = document.getElementById('memberSearch');
    const searchResults = document.getElementById('searchResults');
    const selectedMemberInfo = document.getElementById('selectedMemberInfo');
    const targetMemberId = document.getElementById('targetMemberId');
    const topupSubmitBtn = document.getElementById('topupSubmitBtn');

    memberSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 1) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('payments.members.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length > 0) {
                    data.slice(0, 5).forEach(member => {
                        const item = document.createElement('a');
                        item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                        item.innerHTML = `
                            <div>
                                <div class="fw-bold">${member.name}</div>
                                <small class="text-muted">${member.phone} | Card: ${member.card_number || 'No Card'}</small>
                            </div>
                            <span class="badge bg-label-success">TZS ${parseFloat(member.balance).toLocaleString()}</span>
                        `;
                        item.onclick = () => selectMember(member);
                        searchResults.appendChild(item);
                    });
                    searchResults.style.display = 'block';
                } else {
                    searchResults.innerHTML = '<div class="list-group-item text-center py-3 text-muted">No members found</div>';
                    searchResults.style.display = 'block';
                }
            });
        }, 300);
    });

    function selectMember(member) {
        targetMemberId.value = member.id;
        document.getElementById('selectedMemberName').innerText = member.name;
        document.getElementById('memberNameInitial').innerText = member.name.charAt(0).toUpperCase();
        document.getElementById('selectedMemberBalance').innerText = 'TZS ' + parseFloat(member.balance).toLocaleString();
        
        memberSearch.value = member.name;
        searchResults.style.display = 'none';
        selectedMemberInfo.style.display = 'block';
        topupSubmitBtn.disabled = false;
    }

    document.getElementById('clearMember').addEventListener('click', function() {
        targetMemberId.value = '';
        selectedMemberInfo.style.display = 'none';
        memberSearch.value = '';
        topupSubmitBtn.disabled = true;
    });

    // Top-up Submission
    document.getElementById('topupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = document.getElementById('topupSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ri-loader-4-line ri-spin me-2"></i> Processing...';

        fetch("{{ route('payments.top-ups.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Top-up Successful!',
                    text: data.message,
                    icon: 'success'
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Something went wrong', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ri-add-circle-line me-2"></i> Process Top-up';
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire('Error', 'Connection lost or server error', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="ri-add-circle-line me-2"></i> Process Top-up';
        });
    });

    // Card Status Management
    window.updateCardStatus = function(memberId, status) {
        let confirmText = "Transitioning card to " + status.replace('_', ' ') + "...";
        if (status === 'ready') confirmText = "The member will be notified via SMS that their card is ready for pickup.";
        if (status === 'issued') confirmText = "Confirm that the member has successfully collected their card.";

        Swal.fire({
            title: 'Update Card Status?',
            text: confirmText,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/payments/members/${memberId}/card-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
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
            if (result.isConfirmed && result.value.success) {
                Swal.fire('Success', result.value.message, 'success').then(() => location.reload());
            }
        });
    }

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!memberSearch.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
</script>
@endpush
