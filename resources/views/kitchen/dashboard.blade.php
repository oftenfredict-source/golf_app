@extends('settings._layout-base')

@section('title', 'Professional KDS')

@section('content')
<!-- Professional KDS Styles -->
<style>
    /* Premium Dark Theme Overrides */
    body {
        background-color: #0c0e12 !important;
        color: #e0e0e0;
    }
    
    .kds-container {
        padding: 2rem;
        min-height: 100vh;
    }

    .kds-header {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
    }

    .order-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    /* KDS Card Design */
    .kds-card {
        background: #1a1d23;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
    }

    .kds-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        border-color: rgba(255, 255, 255, 0.2);
    }

    /* Status Glow Indicators */
    .status-preparing { border-top: 6px solid #00d2ff; box-shadow: inset 0 6px 20px rgba(0, 210, 255, 0.05); }
    .status-ready { border-top: 6px solid #00f260; box-shadow: inset 0 6px 20px rgba(0, 242, 96, 0.05); }
    .status-pending, .status-saved { border-top: 6px solid #f9d423; box-shadow: inset 0 6px 20px rgba(249, 212, 35, 0.05); }

    /* Urgency States (Overridden by JS) */
    .urgency-warning { border-top-color: #ff9f43 !important; }
    .urgency-critical { border-top-color: #ff4757 !important; animation: pulse-border 2s infinite; }

    @keyframes pulse-border {
        0% { border-top-color: #ff4757; }
        50% { border-top-color: #ff6b81; }
        100% { border-top-color: #ff4757; }
    }

    .kds-card-header {
        padding: 1.25rem;
        background: rgba(255, 255, 255, 0.02);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .order-num {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
    }

    .timer-badge {
        background: rgba(255, 255, 255, 0.05);
        padding: 0.5rem 0.8rem;
        border-radius: 10px;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 700;
        color: #a0a0a0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .timer-urgent { color: #ff4757; background: rgba(255, 71, 87, 0.1); }

    .kds-card-body {
        padding: 1.25rem;
        flex-grow: 1;
    }

    .table-tag {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #00d2ff;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: block;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }

    .item-qty {
        background: #2f3542;
        color: #fff;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-weight: 700;
        margin-right: 0.75rem;
    }

    .item-name {
        font-weight: 600;
        color: #ced4da;
    }

    .special-note {
        background: rgba(249, 212, 35, 0.1);
        border-left: 3px solid #f9d423;
        padding: 0.75rem;
        margin-top: 1rem;
        border-radius: 0 8px 8px 0;
        font-size: 0.85rem;
        color: #f9d423;
    }

    .kds-card-footer {
        padding: 1.25rem;
        background: rgba(0,0,0,0.2);
    }

    /* Glass Buttons */
    .kds-btn {
        width: 100%;
        padding: 1rem;
        border-radius: 12px;
        border: none;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .btn-prepare { background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%); color: #fff; }
    .btn-ready { background: linear-gradient(135deg, #00f260 0%, #0575E6 100%); color: #fff; }
    
    .kds-btn:hover {
        transform: scale(0.98);
        filter: brightness(1.2);
        box-shadow: 0 0 20px rgba(0, 210, 255, 0.3);
    }

    /* Stats Dashboard */
    .stat-pill {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        background: rgba(255,255,255,0.03);
        border-radius: 15px;
        border: 1px solid rgba(255,255,255,0.05);
    }

    .stat-val { font-size: 1.5rem; font-weight: 800; color: #fff; }
    .stat-label { font-size: 0.75rem; color: #808080; text-transform: uppercase; }

    /* Empty State */
    .kitchen-empty {
        text-align: center;
        padding: 5rem 0;
        color: #4b4b4b;
    }
    .kitchen-empty i { font-size: 6rem; margin-bottom: 1.5rem; opacity: 0.2; }

    /* Responsive Mobile Overrides */
    @media (max-width: 991.98px) {
        .kds-container {
            padding: 1rem;
        }
        .order-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .kds-header {
            flex-direction: column;
            align-items: flex-start !important;
            padding: 1.25rem;
        }
        .kds-header > div:first-child {
            width: 100%;
        }
        .kds-header .gap-4 {
            gap: 0.5rem !important;
            flex-wrap: wrap;
            margin-top: 1rem !important;
        }
        .stat-pill {
            padding: 0.75rem 1rem;
            flex: 1 1 calc(50% - 0.5rem);
            min-width: 140px;
        }
        .kds-header .text-end {
            text-align: left !important;
            margin-top: 1.5rem;
            width: 100%;
        }
        .timer-badge {
            padding: 0.4rem 0.6rem;
            font-size: 0.9rem;
        }
        .order-num {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 575.98px) {
        .stat-pill {
            flex: 1 1 100%;
        }
        .btn-sync-text {
            display: none;
        }
    }
</style>

<div class="kds-container">
    <!-- Header / Stats -->
    <div class="kds-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-black mb-1 text-white">KITCHEN OPS</h2>
            <div class="d-flex gap-4 mt-2">
                <div class="stat-pill border-warning">
                    <i class="ri ri-time-line fs-3 text-warning"></i>
                    <div>
                        <div class="stat-val" id="total-pending">{{ $stats['pending_count'] }}</div>
                        <div class="stat-label">Pendings</div>
                    </div>
                </div>
                <div class="stat-pill border-info">
                    <i class="ri ri-fire-line fs-3 text-info"></i>
                    <div>
                        <div class="stat-val" id="total-cooking">{{ $stats['preparing_count'] }}</div>
                        <div class="stat-label">In Prep</div>
                    </div>
                </div>
                <div class="stat-pill border-success">
                    <i class="ri ri-check-double-line fs-3 text-success"></i>
                    <div>
                        <div class="stat-val">{{ $stats['ready_count'] }}</div>
                        <div class="stat-label">Ready</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-end">
            <div class="stat-label mb-2">Live Status</div>
            <button class="btn btn-outline-light rounded-pill px-4" onclick="location.reload()">
                <span class="spinner-grow spinner-grow-sm text-success me-2"></span> <span class="btn-sync-text">DATA SYNCED</span>
            </button>
        </div>
    </div>

    <!-- Order Display Grid -->
    <div class="order-grid" id="kds-grid">
        @forelse($activeOrders as $order)
            <div class="kds-card status-{{ $order->status }}" data-order-id="{{ $order->id }}" data-time="{{ $order->created_at->format('Y-m-d H:i:s') }}">
                <div class="kds-card-header">
                    <div>
                        <div class="order-num">#{{ substr($order->order_number, -4) }}</div>
                        <span class="table-tag">{{ $order->table_number ?? 'STATION' }}</span>
                    </div>
                    <div class="timer-badge" id="timer-{{ $order->id }}">
                        <i class="ri ri-time-line"></i> 
                        <span class="timer-val">00:00</span>
                    </div>
                </div>
                
                <div class="kds-card-body">
                    <div class="items-list">
                        @foreach($order->items as $item)
                            @if(!$item->menuItem->category->is_alcohol)
                                <div class="item-row">
                                    <div class="d-flex align-items-center">
                                        <span class="item-qty">{{ $item->quantity }}</span>
                                        <span class="item-name">{{ $item->menuItem->name }}</span>
                                    </div>
                                </div>
                                @if($item->special_instructions)
                                    <div class="special-note">
                                        <strong>REQ:</strong> {{ $item->special_instructions }}
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>

                    @if($order->notes)
                        <div class="mt-3 opacity-50 small">
                            <i class="ri ri-chat-1-line me-1"></i> {{ $order->notes }}
                        </div>
                    @endif
                </div>

                <div class="kds-card-footer">
                    @if($order->status === 'saved' || $order->status === 'pending')
                        <button class="kds-btn btn-prepare" onclick="updateKdsStatus(this, {{ $order->id }}, 'preparing')">
                            <i class="ri ri-fire-line"></i> FIRE ORDER
                        </button>
                    @elseif($order->status === 'preparing')
                        <button class="kds-btn btn-ready" onclick="updateKdsStatus(this, {{ $order->id }}, 'ready')">
                            <i class="ri ri-check-double-line"></i> MARK READY
                        </button>
                    @elseif($order->status === 'ready')
                        <div class="glass-alert border d-flex align-items-center justify-content-center py-3 bg-success rounded-3 fw-black text-white" style="letter-spacing: 2px;">
                            ALREADY READY
                        </div>
                        <button class="btn btn-link text-muted btn-sm mt-2 w-100" onclick="updateKdsStatus(this, {{ $order->id }}, 'served')">
                             FORCE SERVED
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="kitchen-empty">
                <i class="ri ri-restaurant-line"></i>
                <h2>KITCHEN IS CLEAR</h2>
                <p>No active orders in the queue.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Audio Assets -->
<audio id="new-order-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

@push('scripts')
<script>
    // Timer Logic
    function updateTimers() {
        const cards = document.querySelectorAll('.kds-card');
        const now = new Date();

        cards.forEach(card => {
            const startTime = new Date(card.dataset.time);
            const diff = now - startTime;
            
            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            
            const timerVal = card.querySelector('.timer-val');
            const timerBadge = card.querySelector('.timer-badge');
            
            timerVal.innerText = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            // Urgency Logic
            if (minutes >= 15) {
                card.classList.add('urgency-critical');
                timerBadge.classList.add('timer-urgent');
            } else if (minutes >= 8) {
                card.classList.add('urgency-warning');
                timerBadge.classList.add('timer-urgent');
            }
        });
    }

    setInterval(updateTimers, 1000);
    updateTimers();

    function updateKdsStatus(btnElement, orderId, status) {
        const originalHtml = btnElement.innerHTML;
        btnElement.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
        btnElement.disabled = true;

        fetch('/kitchen/order/' + orderId + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') 
                    ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                    : '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
                btnElement.innerHTML = originalHtml;
                btnElement.disabled = false;
            }
        })
        .catch(function(e) {
            console.error('FIRE ORDER error:', e);
            alert('Connection error. Please refresh and try again.');
            btnElement.innerHTML = originalHtml;
            btnElement.disabled = false;
        });
    }

    // Auto-refresh logic (checks for new orders)
    let lastOrderCount = {{ count($activeOrders) }};
    setInterval(() => {
        fetch(location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newOrders = doc.querySelectorAll('.kds-card').length;
                
                if (newOrders > lastOrderCount) {
                    document.getElementById('new-order-sound').play().catch(e => {});
                }
                
                // For a true "pro" feel, we could surgically update the grid here 
                // but a simple reload is robust for this session.
                if (newOrders !== lastOrderCount) {
                    location.reload();
                }
            });
    }, 15000);
</script>
@endpush
@endsection
