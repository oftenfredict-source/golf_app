@extends('settings._layout-base')

@section('title', 'Counter Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Services /</span> Counter Dashboard
        </h4>
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($counter)
                <button class="btn btn-xs btn-sm-sm btn-outline-success fw-bold" onclick="openAddItemModal()">
                    <i class="ri ri-add-box-line me-1"></i> ADD ITEM
                </button>
                <button class="btn btn-xs btn-sm-sm btn-outline-primary fw-bold" onclick="openStockModal()">
                    <i class="ri ri-archive-line me-1"></i> STOCK
                </button>
                <button class="btn btn-xs btn-sm-sm btn-success fw-bold" onclick="openPosModal()">
                    <i class="ri ri-add-circle-line me-1"></i> NEW ORDER
                </button>
                <span class="badge bg-label-primary px-3 py-2 rounded-pill">
                    <i class="ri ri-store-line me-1"></i> {{ $counter->name }}
                </span>
                @if($counter->is_alcohol)
                    <span class="badge bg-label-danger px-3 py-2 rounded-pill">
                        <i class="ri ri-goblet-line me-1"></i> Alcohol Specialist
                    </span>
                @endif
            @endif
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="ri ri-refresh-line me-1"></i> Refresh
            </button>
        </div>
    </div>

    @if(!$counter)
        <div class="card shadow-none border-0 overflow-hidden text-center p-5" style="border-radius: 20px; background: #f8f9fa;">
            <div class="card-body">
                <div class="avatar avatar-xl bg-label-warning mx-auto mb-4" style="width: 100px; height: 100px;">
                    <i class="ri ri-error-warning-line display-3"></i>
                </div>
                <h3 class="fw-bold mb-2">Counter Not Assigned</h3>
                <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                    You are not currently assigned to any service counter. Please contact an administrator to be assigned to a counter before you can start fulfilling orders.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4">Go to Main Dashboard</a>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('services.counter-management') }}" class="btn btn-primary px-4">Manage Counters</a>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- Counter Stats -->
        <div class="row g-3 mb-5">
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
                    <div class="card-body d-flex align-items-center p-2 p-md-4">
                        <div class="avatar avatar-md avatar-md-lg bg-label-primary rounded me-2 me-md-3">
                            <i class="ri ri-shopping-bag-line fs-4 fs-md-3"></i>
                        </div>
                        <div>
                            <p class="text-body-secondary mb-0 small">Orders</p>
                            <h4 class="fw-bold mb-0">{{ $stats['orders_today'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
                    <div class="card-body d-flex align-items-center p-2 p-md-4 text-primary">
                        <div class="avatar avatar-md avatar-md-lg bg-primary rounded me-2 me-md-3">
                            <i class="ri ri-money-dollar-circle-line fs-4 fs-md-3 text-white"></i>
                        </div>
                        <div class="text-truncate">
                            <p class="text-primary opacity-75 mb-0 small">Revenue</p>
                            <h4 class="fw-bold mb-0 text-primary text-truncate">TZS {{ number_format($stats['revenue_today']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px; background: linear-gradient(135deg, #ffab00 0%, #ffc107 100%);">
                    <div class="card-body d-flex align-items-center p-3 p-md-4 text-white">
                        <div class="avatar avatar-md avatar-md-lg bg-white rounded me-3">
                            <i class="ri ri-time-line fs-4 fs-md-3 text-warning"></i>
                        </div>
                        <div>
                            <p class="mb-0 opacity-75 small">Pending Fulfilment</p>
                            <h4 class="fw-bold mb-0 text-white">{{ $stats['pending_count'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-transparent border-bottom py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold"><i class="ri ri-list-check me-2 text-primary"></i>Orders Queue</h5>
                            <span class="badge bg-label-info">{{ count($activeOrders) }} New Orders</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @forelse($activeOrders as $order)
                            <div class="order-item p-4 border-bottom @if($loop->first) bg-light-subtle @endif">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <h6 class="mb-0 fw-bold fs-5 me-3">#{{ $order->order_number }}</h6>
                                            <span class="badge bg-label-primary px-2">{{ $order->status }}</span>
                                            @if($order->table_number)
                                                <span class="badge bg-label-info ms-2"><i class="ri ri-table-line me-1"></i>Table {{ $order->table_number }}</span>
                                            @endif
                                        </div>
                                        <h5 class="mb-1 fw-bold">{{ $order->customer_name }}</h5>
                                        <div class="text-body-secondary small">
                                            <i class="ri ri-time-line me-1"></i> {{ $order->created_at->format('H:i A') }} ({{ $order->created_at->diffForHumans() }})
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                        <div class="d-inline-flex flex-column align-items-md-end">
                                            <div class="mb-3">
                                                <h4 class="fw-bold mb-0 text-primary">TZS {{ number_format($order->total_amount) }}</h4>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-primary" onclick="viewOrder({{ $order->id }})">
                                                    <i class="ri ri-eye-line me-1"></i> Details
                                                </button>
                                                @if($order->status === 'ready')
                                                    <button class="btn btn-primary px-4" onclick="updateOrderStatus({{ $order->id }}, '{{ $order->order_number }}', 'served')">
                                                        <i class="ri ri-hand-coin-line me-1"></i> MARK SERVED
                                                    </button>
                                                @else
                                                    <button class="btn btn-success px-4" onclick="updateOrderStatus({{ $order->id }}, '{{ $order->order_number }}', 'ready')">
                                                        <i class="ri ri-check-double-line me-1"></i> MARK READY
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <div class="row">
                                                @foreach($order->items as $item)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-semibold">
                                                                <span class="badge bg-primary rounded-pill me-2">{{ $item->quantity }}x</span> 
                                                                {{ $item->menuItem->name ?? 'Unknown Item' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if($order->notes)
                                                <div class="mt-2 text-warning fw-bold small">
                                                    <i class="ri ri-information-line me-1"></i> NOTES: {{ $order->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 my-5">
                                <div class="avatar avatar-xl bg-label-info mx-auto mb-4" style="width: 120px; height: 120px; opacity: 0.3;">
                                    <i class="ri ri-restaurant-line display-1"></i>
                                </div>
                                <h3 class="fw-bold text-muted">Queue is Empty</h3>
                                <p class="text-body-secondary">Sit back and relax! New orders will appear here automatically.</p>
                                <button class="btn btn-label-primary mt-3" onclick="location.reload()">
                                    <i class="ri ri-refresh-line me-1"></i> Check Again
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- POS Modal -->
<div class="modal fade" id="posModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; height: 85vh;">
            <div class="modal-header bg-primary text-white p-4 border-0">
                <h5 class="modal-title fw-bold text-white"><i class="ri ri-shopping-cart-2-line me-2"></i>New Counter Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 d-flex flex-column flex-md-row overflow-hidden">
                <!-- Left: Member Search & Cart -->
                <div class="col-md-5 border-end d-flex flex-column p-3 p-md-4 overflow-auto" style="max-height: 50vh; max-height: md-none;">
                    <div class="mb-4">
                        <label class="form-label fw-bold">1. Select Member</label>
                        <div class="position-relative">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ri ri-search-line"></i></span>
                                <input type="text" class="form-control" id="pos_member_search" placeholder="Name, Phone or Card No." autocomplete="off">
                            </div>
                            <div id="posMemberSuggestions" class="list-group mt-1 shadow-lg position-absolute w-100 d-none bg-white border rounded-3" style="max-height: 250px; overflow-y: auto; z-index: 1060; top: 100%;"></div>
                        </div>
                    </div>

                    <div id="selectedMemberInfo" class="p-3 rounded-3 bg-light mb-4 d-none border border-primary border-dashed">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted d-block">MEMBER ACCOUNT</small>
                                <h6 class="fw-bold mb-0" id="display_member_name">-</h6>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">BALANCE</small>
                                <h6 class="fw-bold mb-0 text-success" id="display_member_balance">TZS 0</h6>
                            </div>
                        </div>
                        <input type="hidden" id="pos_member_id">
                    </div>

                    <div class="flex-grow-1 overflow-auto mb-4">
                        <label class="form-label fw-bold d-block border-bottom pb-2">2. Order Summary</label>
                        <div id="posCartItems">
                            <div class="text-center py-5 text-muted small">
                                <i class="ri ri-shopping-basket-line display-4 d-block mb-2 opacity-25"></i>
                                Cart is empty
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto p-4 bg-light rounded-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">TOTAL</h5>
                            <h5 class="mb-0 fw-bold text-primary" id="posCartTotal">TZS 0</h5>
                        </div>
                        <div class="alert alert-danger px-3 py-2 small d-none mb-3" id="insufficientBalanceAlert">
                            <i class="ri ri-error-warning-line me-1"></i> Insufficient balance!
                        </div>
                        <button class="btn btn-primary w-100 py-3 fw-bold shadow-sm" id="btnPlaceOrder" disabled onclick="placeOrder()">
                            PLACE ORDER & DEDUCT
                        </button>
                    </div>
                </div>

                <!-- Right: Menu Catalog -->
                <div class="col-md-7 d-flex flex-column bg-light-subtle overflow-auto" style="flex-grow: 1;">
                    <div class="p-4 border-bottom bg-white overflow-auto d-flex gap-2" id="posCategoryFilters">
                        <button class="btn btn-sm btn-label-primary px-3 rounded-pill active" data-category="all">All Items</button>
                        @foreach($categories as $cat)
                            <button class="btn btn-sm btn-outline-secondary px-3 rounded-pill" data-category="{{ $cat->id }}">{{ $cat->name }}</button>
                        @endforeach
                    </div>
                    
                    <div class="p-4 overflow-auto flex-grow-1">
                        <div class="row g-3" id="posMenuItems">
                            @foreach($categories as $cat)
                                @foreach($cat->items as $item)
                                    <div class="col-md-6 menu-card" data-category-id="{{ $cat->id }}">
                                        <div class="card h-100 border-0 shadow-none border-dashed p-3 cursor-pointer item-select" 
                                             id="item-card-{{ $item->id }}"
                                             onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})"
                                             style="transition: all 0.2s;">
                                            <div class="selected-check" onclick="event.stopPropagation(); removeFromCart({{ $item->id }})" title="Unmark item">
                                                <i class="ri-check-line"></i>
                                            </div>
                                            <div class="minus-badge" onclick="event.stopPropagation(); changeQuantity({{ $item->id }}, -1)" title="Reduce quantity">
                                                <i class="ri-subtract-line"></i>
                                            </div>
                                            <div class="quantity-badge" id="item-qty-{{ $item->id }}">1</div>
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold mb-0 text-wrap">{{ $item->name }}</h6>
                                                <span class="badge bg-label-primary px-2">TZS {{ number_format($item->price) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <small class="text-muted">{{ $cat->name }}</small>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-xs btn-label-warning" onclick="event.stopPropagation(); quickPriceAdjust({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})">
                                                        <i class="ri ri-price-tag-3-line"></i> Price
                                                    </button>
                                                    <button class="btn btn-xs btn-label-secondary" onclick="event.stopPropagation(); quickAdjustStock({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->stock_quantity }})">
                                                        <i class="ri ri-add-line"></i> Stock
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-label-success p-4 border-0">
                <h5 class="modal-title fw-bold"><i class="ri ri-add-box-line me-2"></i>Add New Item to Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addItemForm" onsubmit="submitAddItem(event)">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Name</label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="e.g., Budweiser Large" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        @if($categories->count() > 0)
                            <div class="p-3 border rounded-3 bg-light">
                                <i class="ri ri-price-tag-3-line me-1 text-primary"></i>
                                <span class="fw-bold">{{ $categories->first()->name }}</span>
                                <input type="hidden" name="category_id" value="{{ $categories->first()->id }}">
                                <small class="text-muted d-block mt-1">Items will be automatically added to your primary counter category.</small>
                            </div>
                        @else
                            <div class="alert alert-warning p-2 mb-0">
                                <i class="ri ri-error-warning-line me-1"></i> No categories available!
                            </div>
                        @endif
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Price (TZS)</label>
                            <input type="number" name="price" class="form-control form-control-lg" placeholder="0" required min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Starting Stock</label>
                            <input type="number" name="stock_quantity" class="form-control form-control-lg" value="0" min="0">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Low Stock Warning At</label>
                        <input type="number" name="low_stock_threshold" class="form-control form-control-lg" value="10" min="0">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-label-secondary w-100" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold" id="btnSubmitItem">CREATE & ADD TO STOCK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock Management Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-label-primary p-4 border-0">
                <h5 class="modal-title fw-bold"><i class="ri ri-archive-line me-2"></i>Manage Counter Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item Name</th>
                                <th class="text-center">Current Stock</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="counterStockTable">
                            @foreach($categories as $cat)
                                @foreach($cat->items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $item->name }}</div>
                                            <small class="text-muted">{{ $cat->name }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $item->stock_quantity <= $item->low_stock_threshold ? 'bg-label-warning' : 'bg-label-success' }} px-3 py-2">
                                                {{ $item->stock_quantity }} units
                                            </span>
                                        </td>
                                         <td class="text-end">
                                             <div class="d-flex justify-content-end gap-2">
                                                 <button class="btn btn-sm btn-outline-warning" onclick="editItemDetails({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})">
                                                     <i class="ri ri-edit-line me-1"></i> Edit Item
                                                 </button>
                                                 <button class="btn btn-sm btn-outline-info" onclick="quickAdjustStock({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->stock_quantity }}, 'set')">
                                                     <i class="ri ri-edit-box-line me-1"></i> Edit Stock
                                                 </button>
                                                 <button class="btn btn-sm btn-primary" onclick="quickAdjustStock({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->stock_quantity }}, 'add')">
                                                     <i class="ri ri-add-line me-1"></i> Add Stock
                                                 </button>
                                             </div>
                                         </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Adjust Modal -->
<div class="modal fade" id="quickAdjustModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" id="quickAdjustTitle">Add Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickAdjustForm" onsubmit="submitQuickAdjust(event)">
                <div class="modal-body p-4 pt-0">
                    <input type="hidden" id="quick_adjust_id">
                    <input type="hidden" id="quick_adjust_type" value="add">
                    <div class="mb-3">
                        <label class="form-label fw-bold" id="quickAdjustLabel">Quantity to Add</label>
                        <input type="number" class="form-control form-control-lg text-center fw-bold" id="quick_adjust_qty" required min="1" value="1">
                        <div class="alert alert-info py-2 small mt-2" id="quickAdjustNote">
                            Note: This will add to the current stock.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-label-secondary w-100" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" id="btnSubmitAdjust">UPDATE STOCK</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Item Modal (Name & Price) -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" id="editItemTitle">Edit Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editItemForm" onsubmit="submitEditItem(event)">
                <div class="modal-body p-4 pt-0">
                    <input type="hidden" id="edit_item_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Name</label>
                        <input type="text" class="form-control form-control-lg" id="edit_item_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Unit Price (TZS)</label>
                        <input type="number" class="form-control form-control-lg text-center fw-bold text-primary" id="edit_item_price" required min="0">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-label-secondary w-100" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning w-100 py-2 fw-bold" id="btnSubmitEditItem">SAVE CHANGES</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function updateOrderStatus(orderId, orderNumber, newStatus) {
        let confirmMsg = 'Are you sure you want to mark order #' + orderNumber + ' as ' + newStatus + '?';
        if (newStatus === 'served') confirmMsg = 'Confirm order #' + orderNumber + ' has been picked up/served?';

        showConfirm(confirmMsg).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ url("services/orders", [], false) }}/' + orderId + '/status', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('Order ' + newStatus + '!').then(() => location.reload());
                    } else {
                        showError(data.message || 'Error updating status');
                    }
                })
                .catch(err => showError('Error: ' + err.message));
            }
        });
    }

    function viewOrder(orderId) {
        const content = document.getElementById('orderDetailsContent');
        content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
        new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
        
        fetch('{{ url("services/orders", [], false) }}/' + orderId, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const order = data.order;
                let itemsHtml = (order.items || []).map(item => `
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3">
                        <div>
                            <h6 class="mb-0 fw-bold">${item.menu_item?.name || 'Item'}</h6>
                            <small class="text-muted">Quantity: ${item.quantity}</small>
                        </div>
                        <h6 class="mb-0 fw-bold">TZS ${number_format(item.price * item.quantity)}</h6>
                    </div>
                `).join('');

                content.innerHTML = `
                    <div class="row mb-4">
                        <div class="col-6">
                            <p class="text-muted mb-1">CUSTOMER</p>
                            <h5 class="fw-bold mb-0">${order.customer_name}</h5>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted mb-1">TABLE</p>
                            <h5 class="fw-bold mb-0">${order.table_number || 'N/A'}</h5>
                        </div>
                    </div>
                    <div class="divider my-4">
                        <div class="divider-text">ORDER ITEMS</div>
                    </div>
                    ${itemsHtml}
                    <div class="mt-4 p-4 rounded-3" style="background: rgba(105, 108, 255, 0.05);">
                        <div class="d-flex justify-content-between align-items-center fw-bold text-primary">
                            <h5 class="mb-0 fw-bold">Total Amount</h5>
                            <h5 class="mb-0 fw-bold">TZS ${number_format(order.total_amount)}</h5>
                        </div>
                    </div>
                    ${order.notes ? `
                        <div class="mt-4">
                            <p class="text-warning fw-bold mb-1"><i class="ri ri-information-line me-1"></i> NOTES</p>
                            <div class="p-3 bg-label-warning rounded-3">${order.notes}</div>
                        </div>
                    ` : ''}
                    <div class="mt-4">
                        <button class="btn btn-success w-100 py-3 fw-bold" onclick="readyOrder(${order.id}, '${order.order_number}')">
                            MARK AS READY & NOTIFY WAITER
                        </button>
                    </div>
                `;
            }
        })
        .catch(err => {
            content.innerHTML = '<div class="alert alert-danger">Error loading details: ' + err.message + '</div>';
        });
    }

    function number_format(n) {
        return parseFloat(n).toLocaleString();
    }

    // --- POS Logic ---
    let cart = [];
    let selectedMember = null;
    let posModal;

    function openPosModal() {
        if (!posModal) posModal = new bootstrap.Modal(document.getElementById('posModal'));
        cart = [];
        selectedMember = null;
        updateCartUi();
        document.getElementById('pos_member_id').value = '';
        document.getElementById('pos_member_search').value = '';
        document.getElementById('selectedMemberInfo').classList.add('d-none');
        posModal.show();
    }

    // Member Search in POS
    document.getElementById('pos_member_search')?.addEventListener('input', function() {
        const q = this.value.trim();
        const suggestionsBox = document.getElementById('posMemberSuggestions');
        
        if (q.length < 2) {
            suggestionsBox.classList.add('d-none');
            return;
        }

        fetch('{{ url("payments/members/search", [], false) }}?q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(members => {
            if (!members || members.length === 0) {
                suggestionsBox.innerHTML = '<div class="list-group-item text-muted small">No members found</div>';
                suggestionsBox.classList.remove('d-none');
                return;
            }

            suggestionsBox.innerHTML = members.map(m => `
                <div class="list-group-item list-group-item-action cursor-pointer" onclick='selectPosMember(${JSON.stringify(m).replace(/'/g, "&apos;")})'>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">${m.name}</div>
                            <small class="text-muted">${m.card_number || 'No Card'}</small>
                        </div>
                        <span class="badge bg-label-success">TZS ${number_format(m.balance)}</span>
                    </div>
                </div>
            `).join('');
            suggestionsBox.classList.remove('d-none');
        });
    });

    function selectPosMember(member) {
        selectedMember = member;
        document.getElementById('pos_member_id').value = member.id;
        document.getElementById('display_member_name').textContent = member.name;
        document.getElementById('display_member_balance').textContent = 'TZS ' + number_format(member.balance);
        document.getElementById('selectedMemberInfo').classList.remove('d-none');
        document.getElementById('posMemberSuggestions').classList.add('d-none');
        document.getElementById('pos_member_search').value = member.name;
        updateCartUi();
    }

    // Category Filtering
    document.getElementById('posCategoryFilters')?.addEventListener('click', function(e) {
        const btn = e.target.closest('button');
        if (!btn) return;

        // UI State
        this.querySelectorAll('button').forEach(b => b.classList.replace('btn-label-primary', 'btn-outline-secondary'));
        this.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        btn.classList.replace('btn-outline-secondary', 'btn-label-primary');
        btn.classList.add('active');

        const catId = btn.dataset.category;
        document.querySelectorAll('.menu-card').forEach(card => {
            if (catId === 'all' || card.dataset.categoryId === catId) {
                card.classList.remove('d-none');
            } else {
                card.classList.add('d-none');
            }
        });
    });

    function addToCart(id, name, price) {
        const existing = cart.find(i => i.id === id);
        if (existing) {
            existing.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1 });
        }
        updateCartUi();
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        updateCartUi();
    }

    function changeQuantity(id, delta) {
        const item = cart.find(i => i.id === id);
        if (item) {
            item.quantity += delta;
            if (item.quantity <= 0) {
                removeFromCart(id);
            } else {
                updateCartUi();
            }
        }
    }

    function updateCartUi() {
        const cartItemsBox = document.getElementById('posCartItems');
        const totalBox = document.getElementById('posCartTotal');
        const btnPlaceOrder = document.getElementById('btnPlaceOrder');

        if (cart.length === 0) {
            cartItemsBox.innerHTML = '<div class="text-center py-5 text-muted small"><i class="ri ri-shopping-basket-line display-4 d-block mb-2 opacity-25"></i>Cart is empty</div>';
            totalBox.textContent = 'TZS 0';
            btnPlaceOrder.disabled = true;
            return;
        }

        let total = 0;
        cartItemsBox.innerHTML = cart.map(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            return `
                <div class="d-flex justify-content-between align-items-center p-3 mb-2 rounded-3 bg-white border shadow-sm">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-truncate" style="max-width: 150px;">${item.name}</div>
                        <div class="d-flex align-items-center mt-1">
                            <div class="btn-group btn-group-sm mb-0">
                                <button class="btn btn-outline-primary py-0 px-2" onclick="changeQuantity(${item.id}, -1)"><i class="ri-subtract-line"></i></button>
                                <button class="btn btn-outline-primary py-0 px-2 fw-bold disabled" style="min-width: 35px; opacity: 1; color: #696cff !important; background: transparent !important;">${item.quantity}</button>
                                <button class="btn btn-outline-primary py-0 px-2" onclick="changeQuantity(${item.id}, 1)"><i class="ri-add-line"></i></button>
                            </div>
                            <span class="ms-2 text-muted small">@ TZS ${number_format(item.price)}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="fw-bold text-dark">TZS ${number_format(itemTotal)}</div>
                        <button class="btn btn-sm btn-label-danger p-1" onclick="removeFromCart(${item.id})">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        totalBox.textContent = 'TZS ' + number_format(total);
        
        // Update Catalog Visual State (Marking/Unmarking/Quantity)
        document.querySelectorAll('.menu-card .card').forEach(card => card.classList.remove('selected'));
        cart.forEach(item => {
            const card = document.getElementById('item-card-' + item.id);
            if (card) {
                card.classList.add('selected');
                const qBadge = document.getElementById('item-qty-' + item.id);
                if (qBadge) qBadge.textContent = item.quantity;
            }
        });

        // Validation
        const insufficient = selectedMember && selectedMember.balance < total;
        document.getElementById('insufficientBalanceAlert').classList.toggle('d-none', !insufficient);
        btnPlaceOrder.disabled = !selectedMember || insufficient || cart.length === 0;
    }

    function placeOrder() {
        if (!selectedMember || cart.length === 0) return;

        const btn = document.getElementById('btnPlaceOrder');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div> PLACING...';

        const orderData = {
            member_id: selectedMember.id,
            counter_id: {{ $counter->id ?? 'null' }},
            items: cart.map(i => ({ menu_item_id: i.id, quantity: i.quantity })),
            customer_name: selectedMember.name,
            total_amount: cart.reduce((sum, i) => sum + (i.price * i.quantity), 0)
        };

        fetch('{{ route("services.orders.store", [], false) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showSuccess('Order placed successfully! Transaction completed.').then(() => location.reload());
            } else {
                showError(data.message || 'Failed to place order');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(err => {
            showError('Communication error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // Auto refresh every 60 seconds
    setInterval(() => {
        if (document.hidden) return;
        location.reload();
    }, 60000);
    // --- Add Item Logic ---
    let addItemModal;
    
    function openAddItemModal() {
        if (!addItemModal) addItemModal = new bootstrap.Modal(document.getElementById('addItemModal'));
        document.getElementById('addItemForm').reset();
        addItemModal.show();
    }

    function submitAddItem(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitItem');
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';

        fetch('{{ route("services.menu-items.store", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showSuccess('Item added to system').then(() => {
                    location.reload();
                });
            } else {
                showError(data.message || 'Failed to add item');
                btn.disabled = false;
                btn.innerHTML = 'CREATE & ADD TO STOCK';
            }
        })
        .catch(err => {
            showError('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = 'CREATE & ADD TO STOCK';
        });
    }

    // --- Stock Management Logic ---
    let stockModal;
    let quickAdjustModal;

    function openStockModal() {
        if (!stockModal) stockModal = new bootstrap.Modal(document.getElementById('stockModal'));
        stockModal.show();
    }

    function quickAdjustStock(id, name, currentStock, type = 'add') {
        if (!quickAdjustModal) quickAdjustModal = new bootstrap.Modal(document.getElementById('quickAdjustModal'));
        document.getElementById('quick_adjust_id').value = id;
        document.getElementById('quick_adjust_type').value = type;
        
        if (type === 'set') {
            document.getElementById('quickAdjustTitle').innerHTML = '<i class="ri-edit-box-line me-2"></i>Edit Stock: ' + name;
            document.getElementById('quickAdjustLabel').textContent = 'New Absolute Stock Level';
            document.getElementById('quick_adjust_qty').placeholder = 'Current: ' + currentStock;
            document.getElementById('quickAdjustNote').textContent = 'Note: This will set the stock to EXACTLY this number.';
            document.getElementById('btnSubmitAdjust').textContent = 'SAVE NEW STOCK LEVEL';
        } else {
            document.getElementById('quickAdjustTitle').innerHTML = '<i class="ri-add-line me-2"></i>Add Stock: ' + name;
            document.getElementById('quickAdjustLabel').textContent = 'Quantity to Add';
            document.getElementById('quick_adjust_qty').placeholder = 'Enter quantity to add';
            document.getElementById('quickAdjustNote').textContent = 'Note: This will add to the current stock.';
            document.getElementById('btnSubmitAdjust').textContent = 'ADD TO STOCK';
        }
        
        document.getElementById('quick_adjust_qty').value = '';
        quickAdjustModal.show();
    }

    function submitQuickAdjust(e) {
        e.preventDefault();
        const id = document.getElementById('quick_adjust_id').value;
        const qty = document.getElementById('quick_adjust_qty').value;
        const type = document.getElementById('quick_adjust_type').value;
        const btn = document.getElementById('btnSubmitAdjust');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

        fetch('{{ url("inventory/menu-items", [], false) }}/' + id + '/adjust', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                type: type,
                quantity: qty
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showSuccess('Stock updated successfully').then(() => {
                    location.reload(); 
                });
            } else {
                showError(data.message || 'Failed to update stock');
                btn.disabled = false;
                btn.innerHTML = 'UPDATE STOCK';
            }
        })
        .catch(err => {
            showError('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = 'UPDATE STOCK';
        });
    }

    // --- Item Details Logic ---
    let editItemModal;
    function editItemDetails(id, name, currentPrice) {
        if (!editItemModal) editItemModal = new bootstrap.Modal(document.getElementById('editItemModal'));
        document.getElementById('edit_item_id').value = id;
        document.getElementById('edit_item_name').value = name;
        document.getElementById('edit_item_price').value = currentPrice;
        editItemModal.show();
    }

    function submitEditItem(e) {
        e.preventDefault();
        const id = document.getElementById('edit_item_id').value;
        const name = document.getElementById('edit_item_name').value;
        const price = document.getElementById('edit_item_price').value;
        const btn = document.getElementById('btnSubmitEditItem');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

        fetch('{{ url("services/menu-items", [], false) }}/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                _method: 'PUT',
                name: name,
                price: price
            })
        })
        .then(async r => {
            if (!r.ok) {
                let errorMessage = 'Network response was not ok (' + r.status + ')';
                try {
                    const errorData = await r.json();
                    if (errorData.message) errorMessage = errorData.message;
                } catch (e) {}
                throw new Error(errorMessage);
            }
            return r.json();
        })
        .then(data => {
            if (data.success) {
                showSuccess('Item updated successfully').then(() => location.reload());
            } else {
                showError(data.message || 'Failed to update item');
                btn.disabled = false;
                btn.innerHTML = 'SAVE CHANGES';
            }
        })
        .catch(err => {
            showError('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = 'SAVE CHANGES';
        });
    }
</script>
@endpush

<style>
    .order-item:hover {
        background-color: #fcfcfd;
        transition: background-color 0.3s ease;
    }
    .badge.bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .badge.bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .badge.bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .badge.bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .badge.bg-label-danger { background-color: #ffe5e5 !important; color: #ff3e1d !important; }

    .menu-card .card:hover { border-color: #696cff !important; background-color: #f8f9ff !important; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(105, 108, 255, 0.1); }
    .menu-card .card.selected { border: 2px solid #696cff !important; background-color: #f0f2ff !important; position: relative; }
    .menu-card .card .selected-check { display: none; position: absolute; top: -10px; right: -10px; background: #696cff; color: white; border-radius: 50%; width: 28px; height: 28px; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); z-index: 10; cursor: pointer; transition: transform 0.2s; }
    .menu-card .card .selected-check:hover { transform: scale(1.1); background: #ff3e1d; }
    .menu-card .card .selected-check:hover i::before { content: "\f00d"; font-family: "remixicon"; } /* Change check to X on hover */
    .menu-card .card.selected .selected-check { display: flex; }
    .menu-card .card .quantity-badge { display: none; position: absolute; bottom: 10px; right: 10px; background: #696cff; color: white; border-radius: 4px; padding: 2px 8px; font-size: 0.75rem; font-weight: bold; z-index: 5; }
    .menu-card .card.selected .quantity-badge { display: block; }
    .item-select {
        cursor: pointer;
        user-select: none;
    }
    #posCategoryFilters::-webkit-scrollbar {
        height: 0px;
    }
    .z-index-2 {
        z-index: 1060;
    }
    .bg-light-subtle {
        background-color: #fbfcfe !important;
    }
    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.7rem;
        line-height: 1;
        border-radius: 0.2rem;
    }
    .menu-card .card .minus-badge {
        display: none;
        position: absolute;
        bottom: 10px;
        right: 45px; /* Positioned to the left of quantity badge */
        background: #ff3e1d;
        color: white;
        border-radius: 4px;
        width: 24px;
        height: 24px;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        z-index: 5;
        cursor: pointer;
        transition: transform 0.1s;
    }
    .menu-card .card .minus-badge:hover {
        transform: scale(1.1);
        background: #e6381a;
    }
    .menu-card .card.selected .minus-badge {
        display: flex;
    }
</style>
