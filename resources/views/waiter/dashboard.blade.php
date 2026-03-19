@extends('settings._layout-base')

@section('title', 'Professional Service HUD')

@section('content')
<!-- Professional Service HUD Styles -->
<style>
    /* Global Dark Theme for Waiter Dashboard */
    body {
        background-color: #0b0d11 !important;
        color: #f1f2f6;
    }

    .service-container {
        padding: 0.75rem 1rem;
        height: 100vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: #0b0d11;
    }

    @media (max-width: 991.98px) {
        .service-container {
            height: auto;
            overflow: visible;
        }
    }

    .hud-header {
        background: linear-gradient(90deg, #161b22 0%, #0d1117 100%);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        padding: 0.6rem 1.25rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
    }
    .hud-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1rem;
        flex: 1;
        min-height: 0; /* Important for flex-overflow */
    }

    @media (max-width: 991.98px) {
        .hud-layout {
            grid-template-columns: 1fr;
            display: flex;
            flex-direction: column;
        }
    }

    .hud-primary {
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    .hud-sidebar {
        display: flex;
        flex-direction: column;
        background: rgba(22, 27, 34, 0.5);
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 0.85rem;
        min-height: 0;
    }
    .scroll-area {
        flex: 1;
        overflow-y: auto;
        padding-right: 0.5rem;
    }
    @media (max-width: 991.98px) {
        .scroll-area {
            overflow-y: visible;
            max-height: none !important;
        }
    }
    .scroll-area::-webkit-scrollbar { width: 5px; }
    .scroll-area::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    /* Section Headers */
    .section-label {
        font-family: 'Outfit', sans-serif;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #8b949e;
        font-weight: 800;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-label i { color: #00d2ff; }

    /* READY QUEUE - High Visibility */
    .ready-queue {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding: 0 0 0.5rem 0;
        margin-bottom: 0.75rem !important;
        scroll-behavior: smooth;
    }
    .ready-order-card {
        min-width: 200px;
        background: #1a1d23;
        border-radius: 12px;
        border: 1px solid rgba(0, 242, 96, 0.15);
        overflow: hidden;
    }

    .ready-queue::-webkit-scrollbar { height: 6px; }
    .ready-queue::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    .ready-order-card {
        min-width: 260px;
        background: #1a1d23;
        border-radius: 16px;
        border: 1px solid rgba(0, 242, 96, 0.2);
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 242, 96, 0.05);
        animation: slide-in 0.5s ease-out;
        position: relative;
    }

    @keyframes slide-in {
        from { transform: translateX(50px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .ready-pulse {
        position: absolute;
        top: 10px;
        right: 15px;
        width: 10px;
        height: 10px;
        background: #00f260;
        border-radius: 50%;
        box-shadow: 0 0 10px #00f260;
        animation: pulse-green 1.5s infinite;
    }

    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 242, 96, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(0, 242, 96, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 242, 96, 0); }
    }

    .ready-card-header {
        background: rgba(0, 242, 96, 0.05);
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }

    .ready-table-num {
        font-size: 1.1rem;
        font-weight: 900;
        color: #fff;
    }

    .ready-items {
        padding: 0.75rem 1rem;
        max-height: 100px;
        overflow-y: auto;
    }

    .ready-item {
        font-size: 0.9rem;
        color: #ced4da;
        margin-bottom: 0.4rem;
        display: flex;
        gap: 0.75rem;
    }

    .ready-btn {
        width: 100%;
        padding: 0.75rem;
        border: none;
        background: linear-gradient(135deg, #00f260 0%, #0575e6 100%);
        color: #fff;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.8rem;
    }

    .ready-btn:hover { filter: brightness(1.2); }

    /* FLOOR MAP - Interative Grid */
    .floor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 0.75rem;
        margin-bottom: 0.75rem !important;
    }

    @media (max-width: 575.98px) {
        .floor-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.5rem;
        }
    }

    .table-orb {
        background: linear-gradient(145deg, #1c2128 0%, #161b22 100%);
        border-radius: 16px;
        padding: 1.25rem 1rem;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    @media (max-width: 575.98px) {
        .table-orb {
            padding: 1rem 0.5rem;
        }
    }
    .table-orb:hover {
        transform: translateY(-3px);
        background: linear-gradient(145deg, #21262d 0%, #1c2128 100%);
        border-color: rgba(0, 210, 255, 0.3);
    }

    .table-orb:hover {
        transform: translateY(-8px);
        background: #252a33;
        border-color: rgba(0, 210, 255, 0.4);
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }

    .table-icon {
        width: 44px;
        height: 44px;
        background: rgba(255,255,255,0.03);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        font-size: 1.25rem;
        color: #8b949e;
    }

    .table-occupied .table-icon { color: #ff9f43; background: rgba(255, 159, 67, 0.1); }
    .table-available .table-icon { color: #00d2ff; background: rgba(0, 210, 255, 0.1); }

    .table-num { font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 0.1rem; }
    .table-status { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .bg-available { background: #00d2ff; }
    .bg-occupied { background: #ff9f43; }

    /* Empty States */
    .service-empty {
        background: rgba(255,255,255,0.01);
        border: 1px dashed rgba(255,255,255,0.03);
        border-radius: 8px;
        padding: 0.4rem;
        text-align: center;
        color: #4b4b4b;
    }

    /* Modal Styling */
    .modal-content-glass {
        background: rgba(13, 17, 23, 0.95) !important;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 24px !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    
    .nav-pills.bg-dark .nav-link {
        color: #8b949e;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 0.6rem 1rem;
    }
    .nav-pills.bg-dark .nav-link.active {
        background: #00d2ff !important;
        color: #000 !important;
        box-shadow: 0 0 15px rgba(0, 210, 255, 0.4);
    }

    @media (max-width: 991.98px) {
        .border-lg-end {
            border-right: none !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 2rem;
        }
    }

    .hover-bg-light-dark:hover {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: #00d2ff !important;
        transform: translateY(-2px);
    }
    .hover-bg-light-dark {
        transition: all 0.2s ease;
    }

    .truncate-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    #modalMemberSuggestions {
        background: #161b22;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    #modal-cart-summary {
        background: linear-gradient(135deg, rgba(0, 210, 255, 0.1) 0%, rgba(58, 123, 213, 0.1) 100%);
    }

    .bg-label-dark { background: rgba(255, 255, 255, 0.05); }
    .bg-label-info { background: rgba(0, 210, 255, 0.1); color: #00d2ff; }
    .bg-label-warning { background: rgba(255, 171, 0, 0.1); color: #ffab00; }
    .bg-label-success { background: rgba(0, 242, 96, 0.1); color: #00f260; }
</style>

<div class="service-container">
    <!-- Header -->
    <div class="hud-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-black mb-0 text-white fs-5">
                SERVICE HUD 
                <span class="badge bg-label-info ms-2" style="font-size: 0.8rem; background: rgba(0, 210, 255, 0.1) !important; color: #00d2ff !important; border: 1px solid rgba(0, 210, 255, 0.2);">
                    <i class="ri ri-wallet-3-line me-1"></i> TZS {{ number_format($revenueToday) }}
                </span>
            </h2>
            <p class="text-muted mb-0 fw-bold uppercase tracking-widest" style="font-size: 0.55rem; letter-spacing: 2px;">LIVE FLOOR TRACKING SYSTEM</p>
        </div>
        
        <div class="d-flex gap-3 align-items-center">
            <div class="text-end me-2">
                <div class="text-muted fw-bold" style="font-size: 0.55rem;">SYSTEM STATUS</div>
                <div class="text-success fw-black" style="font-size: 0.7rem;"><i class="ri ri-checkbox-circle-fill"></i> LIVE SYNC</div>
            </div>
            <button class="btn btn-dark rounded-circle border-0 shadow-sm" style="width: 36px; height: 36px; padding: 0;" onclick="location.reload()">
                <i class="ri ri-refresh-line"></i>
            </button>
        </div>
    </div>

    <!-- MAIN HUD LAYOUT -->
    <div class="hud-layout">
        <!-- PRIMARY: FLOOR MAP -->
        <div class="hud-primary">
            <div class="section-label">
                <i class="ri ri-layout-grid-fill"></i> TABLE STATUS / FLOOR MAP
            </div>
            <div class="scroll-area">
                <div class="floor-grid pe-2">
                    @foreach($tables as $table)
                        <div class="table-orb table-{{ $table->status }}" onclick="showTableOptions({{ json_encode($table) }})">
                            <div class="table-icon">
                                <i class="ri ri-hotel-bed-line"></i>
                            </div>
                            <div class="table-num">{{ $table->table_number }}</div>
                            <div class="table-status {{ $table->status === 'available' ? 'text-info' : 'text-warning' }}">
                                <span class="status-dot bg-{{ $table->status }}"></span>
                                {{ $table->status }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- SIDEBAR: READY & TRACKER -->
        <div class="hud-sidebar">
            <!-- Ready to Serve -->
            <div class="section-label mb-2">
                <i class="ri ri-fire-fill text-danger"></i> READY TO SERVE
            </div>
            
            <div class="scroll-area mb-3" style="max-height: 45%;">
                @forelse($readyOrders as $order)
                    <div class="ready-order-card mb-2" id="ready-{{ $order->id }}">
                        <div class="ready-pulse"></div>
                        <div class="ready-card-header d-flex justify-content-between p-2">
                            <div>
                                <div class="ready-table-num">T-{{ $order->table_number ?? 'CTR' }}</div>
                                <div class="extreme-small fw-bold text-success uppercase">Ready</div>
                            </div>
                            <div class="text-end">
                                <div class="extreme-small text-muted">#{{ substr($order->order_number, -4) }}</div>
                                <div class="extreme-small fw-bold">{{ $order->updated_at->diffForHumans(null, true) }}</div>
                            </div>
                        </div>
                        <div class="ready-items px-2 pb-2">
                            @foreach($order->items as $item)
                                <div class="ready-item extreme-small">
                                    <span class="text-white fw-bold">{{ $item->quantity }}x</span>
                                    <span>{{ $item->menuItem->name }}</span>
                                </div>
                            @endforeach
                        </div>
                        <button class="ready-btn py-2" onclick="serveOrder(this, {{ $order->id }})">
                            <i class="ri ri-hand-coin-line me-1"></i> SERVE GUEST
                        </button>
                    </div>
                @empty
                    <div class="service-empty py-4">
                        <i class="ri ri-checkbox-circle-line fs-4 mb-2 d-block"></i>
                        <div class="extreme-small text-muted uppercase fw-bold">No Pending Items</div>
                    </div>
                @endforelse
            </div>

            <!-- In-Progress Tracker -->
            <div class="section-label mb-2">
                <i class="ri ri-time-line text-warning"></i> ACTIVE TRACKER
            </div>

            <div class="scroll-area">
                @forelse($activeTableOrders->where('status', '!=', 'ready') as $order)
                    <div class="card bg-dark border-0 mb-2 shadow-sm" style="border-radius: 10px; border-left: 3px solid #ff9f43 !important; background: #1c2128 !important;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-label-warning extreme-small fw-bold">T-{{ $order->table_number }}</span>
                                <span class="text-white fw-bold extreme-small">#{{ substr($order->order_number, -4) }}</span>
                            </div>
                            <div class="text-muted extreme-small mb-1">
                                @foreach($order->items as $item)
                                    <div class="truncate-1">• {{ $item->quantity }}x {{ $item->menuItem->name }}</div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-secondary">
                                <span class="text-warning fw-bold text-uppercase" style="font-size: 0.55rem;">
                                    <span class="spinner-grow spinner-grow-sm me-1" style="width: 5px; height: 5px;"></span>
                                    {{ $order->status }}
                                </span>
                                <span class="text-muted extreme-small">{{ $order->created_at->diffForHumans(null, true) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="service-empty py-3">
                        <div class="extreme-small text-muted uppercase fw-bold">No Active Kitchen Passes</div>
                    </div>
                @endforelse
            </div>

            <!-- Daily Sales Log (New) -->
            <div class="section-label mb-2 mt-3">
                <i class="ri ri-line-chart-fill text-info"></i> DAILY SALES LOG
            </div>
            
            <div class="scroll-area" style="max-height: 35%;">
                @forelse($salesToday as $sale)
                    <div class="card bg-dark border-0 mb-2 shadow-sm" style="border-radius: 10px; border-left: 3px solid #00d2ff !important; background: rgba(0, 210, 255, 0.05) !important;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-white fw-black small">TZS {{ number_format($sale->amount) }}</span>
                                <span class="extreme-small text-muted fw-bold">#{{ substr($sale->transaction_id, -4) }}</span>
                            </div>
                            <div class="d-flex justify-content-between extreme-small mt-1">
                                <span class="text-info truncate-1">{{ $sale->customer_name ?: 'Walk-in Guest' }}</span>
                                <span class="text-muted">{{ $sale->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="service-empty py-3">
                        <div class="extreme-small text-muted uppercase fw-bold">No Sales Yet Today</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Table Action Modal -->
<div class="modal fade" id="waiterTableModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-lg-down">
        <div class="modal-content modal-content-glass">
            <div class="modal-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-label-dark me-3 rounded-circle" id="modal_table_icon">
                            <i class="ri ri-table-2 fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black text-white mb-0" id="modal_table_title">TABLE --</h4>
                            <span class="badge rounded-pill px-2 py-0 mt-1" id="modal_table_badge" style="font-size: 0.65rem;">STATUS</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="row g-4">
                    {{-- Left Pane: Active Orders + Order Form --}}
                    <div class="col-lg-4 border-lg-end border-secondary">
                        <div class="section-label mb-3">
                            <i class="ri ri-list-check"></i> CURRENT ACCOUNT
                        </div>
                        
                        {{-- Member Selection (Only if needed or to update) --}}
                        <div class="mb-3 position-relative">
                            <input type="text" class="form-control form-control-sm bg-dark border-secondary text-white" 
                                   id="modal_member_search" placeholder="Search Member..." autocomplete="off">
                            <div id="modal_member_suggestions" class="list-group position-absolute w-100 shadow-lg border-0 mt-1"
                                 style="z-index:2000; display:none; max-height:200px; overflow-y:auto;"></div>
                            <input type="hidden" id="modal_member_id">
                        </div>

                        <div id="modal_member_info" class="p-2 mb-3 rounded bg-dark border border-secondary" style="display:none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-white fw-bold small" id="modal_member_name">Name</span>
                                <span class="text-success fw-black small" id="modal_member_balance">TZS 0</span>
                            </div>
                        </div>

                        <div id="modal_orders_container" class="mb-4 text-start" style="max-height: 400px; overflow-y: auto;">
                            <!-- Active Orders -->
                        </div>

                        {{-- Order Total / Action --}}
                        <div id="modal_cart_summary" class="p-3 rounded-4 bg-primary-subtle border border-primary mt-4" style="display:none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-white fw-bold">Cart Total:</span>
                                <h4 class="text-white fw-black mb-0" id="modal_cart_total">TZS 0</h4>
                            </div>
                            <button class="btn btn-primary w-100 py-3 fw-black rounded-pill" onclick="submitModalOrder()">
                                SEND TO KITCHEN / COUNTER
                            </button>
                        </div>
                    </div>

                    {{-- Right Pane: Tabs for Food/Alch/Non-Alch --}}
                    <div class="col-lg-8">
                        <ul class="nav nav-pills nav-fill mb-4 bg-dark p-1 rounded-pill" id="orderTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active rounded-pill fw-bold" data-bs-toggle="tab" data-bs-target="#tab-food">FOOD</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link rounded-pill fw-bold" data-bs-toggle="tab" data-bs-target="#tab-alcohol">ALCOHOLIC</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link rounded-pill fw-bold" data-bs-toggle="tab" data-bs-target="#tab-non-alcohol">NON-ALCOHOL</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="orderTabContent">
                            <div class="tab-pane fade show active" id="tab-food">
                                <div class="row g-2" id="foodItems"></div>
                            </div>
                            <div class="tab-pane fade" id="tab-alcohol">
                                <div class="row g-2" id="alcoholItems"></div>
                            </div>
                            <div class="tab-pane fade" id="tab-non-alcohol">
                                <div class="row g-2" id="nonAlcoholItems"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sound Alert -->
<audio id="ready-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const waiterModal = new bootstrap.Modal(document.getElementById('waiterTableModal'));
    const allActiveOrders = @json($activeTableOrders);
    const menuCategories = @json($menuCategories);
    let currentCart = [];
    let currentTableNumber = null;

    function showTableOptions(table) {
        currentTableNumber = table.table_number;
        currentCart = [];
        updateModalCart();

        document.getElementById('modal_table_title').innerText = 'TABLE ' + table.table_number.toUpperCase();
        const badge = document.getElementById('modal_table_badge');
        badge.innerText = table.status.toUpperCase();
        badge.className = 'badge rounded-pill px-4 py-2 mb-4 ' + (table.status === 'available' ? 'bg-info' : 'bg-warning');
        
        const iconDiv = document.getElementById('modal_table_icon');
        iconDiv.className = 'avatar avatar-md bg-label-dark me-3 rounded-circle ' + (table.status === 'available' ? 'bg-label-info' : 'bg-label-warning text-warning');

        // Reset search and info
        document.getElementById('modal_member_search').value = '';
        document.getElementById('modal_member_id').value = '';
        document.getElementById('modal_member_info').style.display = 'none';

        // Render Active Orders in Modal
        const ordersContainer = document.getElementById('modal_orders_container');
        const tableOrders = allActiveOrders.filter(o => o.table_number == table.table_number && !['complete', 'cancelled'].includes(o.status));
        
        if (tableOrders.length > 0) {
            let html = '<div class="text-muted extreme-small fw-bold mb-2 uppercase tracking-widest">Active Table Session</div>';
            tableOrders.forEach(order => {
                // Auto-select member from existing order
                if (order.member_id) {
                    selectModalMember(order.member_id, order.customer_name, order.customer_upi, order.member?.balance || 0);
                }

                html += `
                <div class="bg-dark p-3 rounded-4 mb-2 border border-secondary shadow-lg">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white fw-bold small">Order #${order.order_number.slice(-4)}</span>
                        <span class="badge ${order.status == 'ready' ? 'bg-success' : 'bg-label-warning'} extreme-small">${order.status.toUpperCase()}</span>
                    </div>
                    ${order.items.map(item => `
                        <div class="text-muted extreme-small">• ${item.quantity}x ${item.menu_item?.name || 'Item'}</div>
                    `).join('')}
                </div>`;
            });

            // Add clear table option if it has orders
            html += `
                <button class="btn btn-sm btn-outline-danger w-100 mt-2 py-2 fw-bold" onclick="clearTable(${table.id})">
                    <i class="ri ri-checkbox-blank-circle-line me-1"></i> CLEAR TABLE SESSION
                </button>`;

            ordersContainer.innerHTML = html;
        } else {
            ordersContainer.innerHTML = '<div class="text-center py-5 opacity-25 small text-muted">No active session found.</div>';
        }

        renderMenuItems();
        waiterModal.show();
    }

    function renderMenuItems() {
        const foodItemsDiv = document.getElementById('foodItems');
        const alcoholItemsDiv = document.getElementById('alcoholItems');
        const nonAlcoholItemsDiv = document.getElementById('nonAlcoholItems');

        foodItemsDiv.innerHTML = '';
        alcoholItemsDiv.innerHTML = '';
        nonAlcoholItemsDiv.innerHTML = '';

        menuCategories.forEach(cat => {
            let targetDiv = nonAlcoholItemsDiv;
            if (cat.is_food) targetDiv = foodItemsDiv;
            else if (cat.is_alcohol) targetDiv = alcoholItemsDiv;

            if (cat.items.length > 0) {
                let catHtml = `<div class="col-12 mt-2"><div class="section-label" style="font-size:0.6rem;">${cat.name.toUpperCase()}</div></div>`;
                targetDiv.insertAdjacentHTML('beforeend', catHtml);

                cat.items.forEach(item => {
                    let itemHtml = `
                    <div class="col-md-6">
                        <div class="p-2 rounded bg-dark border border-secondary d-flex justify-content-between align-items-center hover-bg-light-dark" style="cursor:pointer" onclick="addToModalCart(${item.id}, '${item.name}', ${item.price})">
                            <div class="flex-grow-1">
                                <div class="text-white fw-bold truncate-1" style="font-size:0.8rem;">${item.name}</div>
                                <div class="text-muted small fw-bold">TZS ${item.price.toLocaleString()}</div>
                            </div>
                            <i class="ri ri-add-circle-fill text-primary fs-4"></i>
                        </div>
                    </div>`;
                    targetDiv.insertAdjacentHTML('beforeend', itemHtml);
                });
            }
        });
    }

    function addToModalCart(id, name, price) {
        const existing = currentCart.find(i => i.id === id);
        if (existing) {
            existing.quantity++;
        } else {
            currentCart.push({ id, name, price, quantity: 1 });
        }
        updateModalCart();
    }

    function updateModalCart() {
        const cartDiv = document.getElementById('modal_cart_summary');
        const totalEl = document.getElementById('modal_cart_total');
        
        if (currentCart.length === 0) {
            cartDiv.style.display = 'none';
            return;
        }

        cartDiv.style.display = 'block';
        let total = 0;
        currentCart.forEach(i => total += (i.price * i.quantity));
        totalEl.innerText = 'TZS ' + total.toLocaleString();
    }

    function selectModalMember(id, name, card, balance) {
        const idInput = document.getElementById('modal_member_id');
        const nameEl = document.getElementById('modal_member_name');
        const balanceEl = document.getElementById('modal_member_balance');
        const infoDiv = document.getElementById('modal_member_info');
        const box = document.getElementById('modal_member_suggestions');
        const searchInput = document.getElementById('modal_member_search');

        if (idInput) idInput.value = id;
        if (nameEl) nameEl.innerText = name;
        if (balanceEl) {
            const val = parseFloat(balance) || 0;
            balanceEl.innerText = 'TZS ' + val.toLocaleString();
        }
        if (infoDiv) infoDiv.style.display = 'block';
        if (box) box.style.display = 'none';
        if (searchInput) {
            searchInput.value = name;
            // Set focused to false to prevent immediate re-trigger if needed
        }
        
        console.log('Member selected:', name);
    }

    function submitModalOrder() {
        const memberId = document.getElementById('modal_member_id').value;
        if (!memberId) {
            showError('Please select a member first');
            return;
        }

        const btn = event.target;
        const origText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'PROCESSING...';

        fetch('{{ route("services.orders.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                member_id: memberId,
                customer_name: document.getElementById('modal_member_name').innerText,
                table_number: currentTableNumber,
                payment_method: 'balance',
                source: 'waiter_dashboard',
                items: currentCart.map(i => ({ menu_item_id: i.id, quantity: i.quantity }))
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ title: 'Order Sent!', icon: 'success', timer: 1500, showConfirmButton: false }).then(() => location.reload());
            } else {
                showError(data.message);
                btn.disabled = false;
                btn.innerText = origText;
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerText = origText;
        });
    }

    // Member search for Modal
    let modalSearchTimeout = null;
    document.getElementById('modal_member_search')?.addEventListener('input', function() {
        const q = this.value;
        if (q.length < 2) { document.getElementById('modal_member_suggestions').style.display = 'none'; return; }
        clearTimeout(modalSearchTimeout);
        modalSearchTimeout = setTimeout(() => {
            fetch('{{ url("payments/members/search") }}?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(members => {
                const box = document.getElementById('modal_member_suggestions');
                box.innerHTML = '';
                if (!members.length) { box.style.display = 'none'; return; }
                members.forEach(m => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item list-group-item-action bg-dark text-white border-secondary small cursor-pointer';
                    div.style.cursor = 'pointer';
                    div.innerHTML = `<strong>${m.name}</strong><br><span class="text-muted">${m.card_number || m.member_id || ''}</span>`;
                    // Use mousedown to trigger before blur if any
                    div.onmousedown = (e) => {
                        e.preventDefault(); 
                        selectModalMember(m.id, m.name, m.card_number, m.balance);
                    };
                    box.appendChild(div);
                });
                box.style.display = 'block';
            });
        }, 300);
    });

    function clearTable(tableId) {
        showConfirm('Confirm clearing this table session? This will mark the table as available.', 'Clear Table?').then((result) => {
            if (!result.isConfirmed) return;

            fetch('/waiter/table/' + tableId + '/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message, 'Table Cleared!').then(() => location.reload());
                } else {
                    showError(data.message);
                }
            })
            .catch(() => {
                showError('Failed to clear table');
            });
        });
    }
    function serveOrder(btnElement, orderId) {
        const card = document.getElementById('ready-' + orderId);

        btnElement.disabled = true;
        btnElement.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('/waiter/order/' + orderId + '/serve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                if (card) {
                    card.style.transform = 'translateY(-20px)';
                    card.style.opacity = '0';
                }
                setTimeout(() => location.reload(), 400);
            }
        })
        .catch(e => location.reload());
    }

    // Auto-refresh logic (checks for ready orders)
    let lastReadyCount = {{ $readyOrders->count() }};
    setInterval(() => {
        if (!document.querySelector('.modal.show')) {
            fetch(location.href)
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newReady = doc.querySelectorAll('.ready-order-card').length;
                    
                    if (newReady > lastReadyCount) {
                        document.getElementById('ready-sound').play().catch(e => {});
                    }
                    
                    if (newReady !== lastReadyCount) {
                        location.reload();
                    }
                });
        }
    }, 15000);
</script>
@endpush
@endsection
