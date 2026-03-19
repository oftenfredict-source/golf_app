@extends('settings._layout-base')

@section('content')
<!-- Remix Icons -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

<style>
  :root {
    --card-silver: linear-gradient(135deg, #e0e0e0 0%, #bdbdbd 50%, #e0e0e0 100%);
    --card-gold: linear-gradient(135deg, #ffd700 0%, #ffa500 50%, #ffd700 100%);
    --card-black: linear-gradient(135deg, #2c3e50 0%, #000000 50%, #2c3e50 100%);
    --glass-bg: rgba(255, 255, 255, 0.15);
    --glass-border: rgba(255, 255, 255, 0.3);
  }

  .hero-banner-card {
    background: linear-gradient(135deg, #940000 0%, #5d0000 100%);
    border: none;
    overflow: hidden;
    position: relative;
    border-radius: 1.5rem;
  }

  .hero-banner-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    z-index: 0;
  }

  .metric-pill {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
    padding: 1rem;
    transition: all 0.3s ease;
  }

  .metric-pill:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-5px);
  }

  /* Glass Card Preview */
  .glass-card-preview {
    width: 100%;
    aspect-ratio: 1.586/1; /* ID-1 Standard */
    border-radius: 1.25rem;
    position: relative;
    padding: 1.75rem;
    color: white;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    background: var(--card-silver);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
  }

  .glass-card-preview::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 50%, rgba(0,0,0,0.1) 100%);
    pointer-events: none;
  }

  .card-chip {
    width: 50px;
    height: 40px;
    background: linear-gradient(135deg, #d4af37 0%, #f9f295 50%, #d4af37 100%);
    border-radius: 6px;
    margin-bottom: 1rem;
    position: relative;
    border: 1px solid rgba(0,0,0,0.1);
  }

  .card-photo-frame {
    width: 100px;
    height: 125px;
    border: 3px solid rgba(255, 255, 255, 0.5);
    border-radius: 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    backdrop-filter: blur(5px);
  }

  .card-photo-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .form-floating-outline .form-control:focus ~ label,
  .form-floating-outline .form-control:not(:placeholder-shown) ~ label {
    background-color: white !important;
  }

  .member-registry-table thead th {
    background: #f8f9fa;
    text-transform: uppercase;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    padding: 1rem;
  }

  .premium-input-group {
    background: #f8f9fa;
    border-radius: 1rem;
    padding: 0.5rem;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
  }

  .premium-input-group:focus-within {
    border-color: #940000;
    box-shadow: 0 0 0 4px rgba(148, 0, 0, 0.1);
  }

  /* Membership Tier Styles */
  .tier-standard { background: var(--card-silver); color: #2c3e50; }
  .tier-vip { background: var(--card-black); color: white; }
  .tier-premier { background: var(--card-gold); color: #5d4037; }

  .tier-vip .card-chip, .tier-standard .card-chip { opacity: 0.9; }
</style>
  <!-- Hero Banner -->
  <div class="card hero-banner-card mb-4 border-0 shadow-lg">
    <div class="card-body p-4 p-md-5">
      <div class="row align-items-center">
        <div class="col-lg-7 text-white">
          <h2 class="display-6 fw-bold mb-2">Card Issuance Tracking</h2>
          <p class="fs-5 opacity-75 mb-4">Track and manage the distribution of physical identity cards to your members. Mark cards as issued once they have been delivered.</p>
          <div class="d-flex flex-wrap gap-3">
            <div class="metric-pill">
              <small class="d-block opacity-75">Cards Issued Today</small>
              <span class="fs-4 fw-bold" id="stats_issued_today">{{ $members->where('is_card_issued', true)->where('updated_at', '>=', now()->startOfDay())->count() }}</span>
            </div>
            <div class="metric-pill">
              <small class="d-block opacity-75">Pending Issuance</small>
              <span class="fs-4 fw-bold text-warning" id="stats_pending">{{ $members->where('is_card_issued', false)->count() }}</span>
            </div>
          </div>
        </div>
        <div class="col-lg-5 d-none d-lg-block text-center position-relative">
          <i class="ri-checkbox-circle-line text-white opacity-25" style="font-size: 10rem;"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Member Registry (Left) -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header border-bottom bg-white py-3">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
              <h5 class="mb-0 fw-bold"><i class="ri-group-line me-2 text-primary"></i>Member Registry</h5>
              <small class="text-muted">Select a member to issue or update their card</small>
            </div>
            <div class="premium-input-group d-flex align-items-center px-3 w-100 w-md-auto" style="min-width: 100%; max-width: 400px; min-width: 250px;">
              <i class="ri-search-line text-muted me-2"></i>
              <input type="text" id="memberSearchInput" class="form-control border-0 bg-transparent p-1" placeholder="Search by name or card...">
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive" style="max-height: 700px;">
            <table class="table table-hover align-middle member-registry-table mb-0" id="membersTable">
              <thead>
                <tr>
                  <th class="ps-4">Member Info</th>
                  <th>ID / Card</th>
                  <th>Status</th>
                  <th>Issued</th>
                  <th class="text-end pe-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($members ?? [] as $m)
                <tr class="member-row" 
                    data-id="{{ $m->id }}"
                    data-member-id="{{ $m->member_id ?? '' }}"
                    data-name="{{ htmlspecialchars($m->name, ENT_QUOTES, 'UTF-8') }}"
                    data-email="{{ htmlspecialchars($m->email ?? '', ENT_QUOTES, 'UTF-8') }}"
                    data-card-number="{{ $m->card_number }}"
                    data-phone="{{ $m->phone }}"
                    data-membership-type="{{ $m->membership_type }}"
                    data-valid-until="{{ $m->valid_until ? \Carbon\Carbon::parse($m->valid_until)->format('Y-m-d') : '' }}"
                    data-search-text="{{ strtolower($m->name . ' ' . ($m->member_id ?? '') . ' ' . $m->card_number . ' ' . $m->phone) }}">
                  <td class="ps-4">
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-md me-3">
                        <div class="avatar-initial rounded-circle bg-label-primary fw-bold">
                          {{ strtoupper(substr($m->name, 0, 1)) }}
                        </div>
                      </div>
                      <div>
                        <div class="fw-bold text-dark">{{ $m->name }}</div>
                        <small class="text-muted"><i class="ri-phone-line me-1"></i>{{ $m->phone }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex flex-column">
                      <code class="text-dark mb-1">{{ $m->member_id ?? '-' }}</code>
                      <code class="text-primary small">{{ $m->card_number }}</code>
                    </div>
                  </td>
                  <td>
                    @if($m->status === 'active')
                      <span class="badge bg-label-success rounded-pill px-3">Active</span>
                    @else
                      <span class="badge bg-label-secondary rounded-pill px-3">{{ ucfirst($m->status) }}</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <span class="issuance-indicator" data-member-id="{{ $m->id }}">
                      @if($m->is_card_issued)
                        <i class="ri-checkbox-circle-fill text-success fs-4" title="Card Issued"></i>
                      @else
                        <i class="ri-close-circle-line text-muted fs-4" title="Pending Issuance"></i>
                      @endif
                    </span>
                  </td>
                  <td class="text-end pe-4">
                    <div class="d-flex justify-content-end gap-2">
                       <button class="btn btn-sm btn-icon btn-label-primary select-member-btn" 
                               title="View Profile"
                               data-id="{{ $m->id }}"
                               data-member-id="{{ $m->member_id }}"
                               data-name="{{ addslashes($m->name) }}"
                               data-card-number="{{ $m->card_number }}"
                               data-tier="{{ $m->membership_type }}"
                               data-is-issued="{{ $m->is_card_issued ? 'true' : 'false' }}"
                               onclick="selectMemberForIssuance(this)">
                        <i class="ri-user-search-line"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-5">
                    <div class="opacity-50">
                      <i class="ri-user-search-line ri-3x mb-2"></i>
                      <p>No members found in the registry</p>
                    </div>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Card Processor (Right) -->
    <div class="col-lg-4">
      <div class="sticky-top" style="top: 1rem; z-index: 100;">
        <!-- Member Issuance Profile -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" id="issuanceProfilePanel" style="display: none;">
          <div class="card-header bg-white border-bottom py-3">
            <h6 class="mb-0 fw-bold"><i class="ri-user-settings-line me-2 text-primary"></i>Member Profile</h6>
          </div>
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <div class="avatar avatar-xl mx-auto mb-3">
                <div class="avatar-initial rounded-circle bg-label-primary fs-2 fw-bold" id="profile_initials">?</div>
              </div>
              <h4 class="mb-1 fw-bold" id="profile_name">-</h4>
              <span class="badge bg-label-secondary text-uppercase" id="profile_tier">-</span>
            </div>

            <div class="list-group list-group-flush mb-4">
              <div class="list-group-item bg-transparent px-0 py-2 border-dashed">
                <div class="d-flex justify-content-between">
                  <small class="text-muted">Member ID</small>
                  <code class="fw-bold" id="profile_member_id">-</code>
                </div>
              </div>
              <div class="list-group-item bg-transparent px-0 py-2 border-dashed">
                <div class="d-flex justify-content-between">
                  <small class="text-muted">Card Number</small>
                  <code class="fw-bold text-primary" id="profile_card_number">-</code>
                </div>
              </div>
              <div class="list-group-item bg-transparent px-0 py-2 border-dashed">
                <div class="d-flex justify-content-between">
                  <small class="text-muted">Issuance Status</small>
                  <span id="profile_status_badge" class="badge bg-label-secondary">UNKNOWN</span>
                </div>
              </div>
            </div>

            <div class="d-grid gap-2">
              <button type="button" class="btn btn-lg shadow-sm fw-bold" id="toggleIssuanceBtn">
                <i class="ri-checkbox-circle-line me-2"></i>Mark as Issued
              </button>
              <div class="form-check form-switch mt-2 justify-content-center d-flex">
                <input class="form-check-input me-2" type="checkbox" id="send_issuance_sms" checked>
                <label class="form-check-label small" for="send_issuance_sms">Notify via SMS</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Placeholder when no member selected -->
        <div class="card border-0 shadow-sm rounded-4 py-5 text-center" id="noMemberSelected">
          <div class="card-body">
            <i class="ri-user-follow-line text-muted opacity-25 mb-3" style="font-size: 4rem;"></i>
            <h6 class="text-muted">Select a member from the registry<br>to manage card issuance.</h6>
          </div>
        </div>
      </div>
  </div>
</div>

@push('scripts')
<script>
let selectedMemberId = null;

function selectMemberForIssuance(btn) {
  const d = btn.dataset;
  selectedMemberId = d.id;
  
  const name = d.name;
  const isIssued = d.isIssued === 'true';
  
  // Update Profile Panel
  const profileName = document.getElementById('profile_name');
  const profileMId = document.getElementById('profile_member_id');
  const profileCNo = document.getElementById('profile_card_number');
  const profileTier = document.getElementById('profile_tier');
  
  if (profileName) profileName.textContent = name;
  if (profileMId) profileMId.textContent = d.memberId || 'N/A';
  if (profileCNo) profileCNo.textContent = d.cardNumber || 'PENDING';
  
  if (profileTier) {
    const tier = d.tier;
    profileTier.textContent = tier.toUpperCase();
    profileTier.className = 'badge bg-label-' + (tier === 'vip' ? 'dark' : (tier === 'premier' ? 'warning' : 'secondary'));
  }
  
  // Set Initials
  const initials = name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
  const initialElem = document.getElementById('profile_initials');
  if (initialElem) initialElem.textContent = initials;
  
  // Update Status Badge & Button
  updateIssuanceUI(isIssued);
  
  // Show Panel
  const panel = document.getElementById('issuanceProfilePanel');
  const placeholder = document.getElementById('noMemberSelected');
  if (placeholder) placeholder.style.display = 'none';
  if (panel) panel.style.display = 'block';
  
  // Smooth scroll
  if (window.innerWidth < 992 && panel) panel.scrollIntoView({ behavior: 'smooth' });

  // Highlight selected row
  document.querySelectorAll('.member-row').forEach(r => r.classList.remove('table-primary'));
  btn.closest('.member-row')?.classList.add('table-primary');
}

function updateIssuanceUI(isIssued) {
  const badge = document.getElementById('profile_status_badge');
  const btn = document.getElementById('toggleIssuanceBtn');
  if (!badge || !btn) return;
  
  if (isIssued) {
    badge.textContent = 'ISSUED';
    badge.className = 'badge bg-label-success';
    btn.innerHTML = '<i class="ri-close-circle-line me-2"></i>Mark as Not Issued';
    btn.className = 'btn btn-lg btn-label-danger shadow-sm fw-bold w-100';
  } else {
    badge.textContent = 'PENDING';
    badge.className = 'badge bg-label-warning';
    btn.innerHTML = '<i class="ri-checkbox-circle-line me-2"></i>Mark as Issued';
    btn.className = 'btn btn-lg btn-primary shadow-sm fw-bold w-100';
  }
}

document.getElementById('toggleIssuanceBtn').addEventListener('click', function() {
  if (!selectedMemberId) return;
  
  const btn = this;
  const originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
  
  fetch(`/payments/members/${selectedMemberId}/toggle-issuance`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      send_sms: document.getElementById('send_issuance_sms').checked
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const isIssued = data.is_card_issued;
      updateIssuanceUI(isIssued);
      
      // Update Registry Status Icon
      const indicator = document.querySelector(`.issuance-indicator[data-member-id="${selectedMemberId}"]`);
      if (indicator) {
        indicator.innerHTML = isIssued 
          ? '<i class="ri-checkbox-circle-fill text-success fs-4" title="Card Issued"></i>'
          : '<i class="ri-close-circle-line text-muted fs-4" title="Pending Issuance"></i>';
      }
      
      // Update Registry Select Button Data (Crucial for re-selection persistence)
      const selectBtn = document.querySelector(`.select-member-btn[data-id="${selectedMemberId}"]`);
      if (selectBtn) {
        selectBtn.dataset.isIssued = isIssued ? 'true' : 'false';
      }
      
      // Update Stats
      const pendingStat = document.getElementById('stats_pending');
      if (pendingStat) {
        let count = parseInt(pendingStat.textContent) || 0;
        pendingStat.textContent = isIssued ? Math.max(0, count - 1) : count + 1;
      }

      Swal.fire({
        icon: 'success',
        title: 'Status Updated',
        text: data.message,
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire('Error', 'Failed to update status', 'error');
  })
  .finally(() => {
    btn.disabled = false;
  });
});

document.getElementById('memberSearchInput').addEventListener('input', function(e) {
  const query = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('.member-row');
  
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
  });
});
</script>
@endpush
@endsection
