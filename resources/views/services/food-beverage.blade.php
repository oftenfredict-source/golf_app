@extends('settings._layout-base')

@section('title', 'Food & Beverage')
@section('description', 'Food & Beverage - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Club Services /</span> Food & Beverage
</h4>

{{-- ============================================================ --}}
{{-- HERO STATS BANNER (Ball Management style) --}}
{{-- ============================================================ --}}
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="card-body p-0">
        <div class="row g-0">

          {{-- Left: Today's activity stats --}}
          <div class="col-md-8 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0 fw-bold"><i class="ri ri-restaurant-line me-2 text-primary"></i>F&B Live Dashboard</h5>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                  <i class="ri ri-folder-add-line me-1"></i> Category
                </button>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMenuItemModal">
                  <i class="ri ri-add-line me-1"></i> Menu Item
                </button>
              </div>
            </div>

            {{-- Order progress bar --}}
            @php
              $totalToday = ($stats['open_orders'] ?? 0) + \App\Models\Order::whereDate('created_at', today())->whereIn('status',['complete','completed'])->count();
              $completedPct = $totalToday > 0 ? ((\App\Models\Order::whereDate('created_at', today())->whereIn('status',['complete','completed'])->count()) / $totalToday) * 100 : 0;
              $savedPct = $totalToday > 0 ? (($stats['open_orders'] ?? 0) / $totalToday) * 100 : 0;
            @endphp
            <div class="progress mb-4" style="height: 42px; border-radius: 12px; background-color: #f1f3f9;">
              <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completedPct }}%">
                <span class="fw-bold">{{ \App\Models\Order::whereDate('created_at', today())->whereIn('status',['complete','completed'])->count() }} Completed</span>
              </div>
              <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $savedPct }}%">
                <span class="fw-bold">{{ $stats['open_orders'] ?? 0 }} Saved</span>
              </div>
            </div>

            <div class="row g-4">
              <div class="col-md-3">
                <div class="p-3 border rounded bg-light-subtle">
                  <small class="text-muted d-block mb-1">Total Today</small>
                  <h4 class="mb-0 fw-bold">{{ $totalToday }} <span class="fs-6 fw-normal text-muted">Orders</span></h4>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 border border-success rounded bg-success-subtle">
                  <small class="text-success d-block mb-1">Completed</small>
                  <h4 class="mb-0 fw-bold">{{ \App\Models\Order::whereDate('created_at', today())->whereIn('status',['complete','completed'])->count() }}</h4>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 border border-warning rounded bg-warning-subtle text-warning">
                  <small class="d-block mb-1">Saved / Open</small>
                  <h4 class="mb-0 fw-bold">{{ $stats['open_orders'] ?? 0 }}</h4>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 border border-info rounded bg-info-subtle text-info">
                  <small class="d-block mb-1">Menu Items</small>
                  <h4 class="mb-0 fw-bold">{{ count($menuItems ?? []) }}</h4>
                </div>
              </div>
            </div>
          </div>

          {{-- Right: Revenue summary --}}
          <div class="col-md-4 bg-primary text-white p-4">
            <h5 class="text-white fw-bold mb-4">Today's Revenue Summary</h5>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>F&B Revenue</span>
                <strong>TZS {{ number_format($stats['revenue_today'] ?? 0) }}</strong>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-white" style="width: 80%"></div>
              </div>
            </div>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>Orders Completed</span>
                <strong>{{ \App\Models\Order::whereDate('created_at', today())->whereIn('status',['complete','completed'])->count() }}</strong>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 60%"></div>
              </div>
            </div>
            <div class="pt-2">
              <div class="alert bg-white text-primary border-0 mb-0 py-3">
                <small class="d-block fw-semibold mb-1">Total Revenue</small>
                <h4 class="mb-0 fw-bold">TZS {{ number_format($stats['revenue_today'] ?? 0) }}</h4>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================ --}}
{{-- PRIMARY ACTIONS: Quick Order (col-8) + Saved Orders (col-4) --}}
{{-- ============================================================ --}}
<div class="row mb-6">

  {{-- Quick Order Form (col-8) --}}
  <div class="col-md-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="ri ri-shopping-cart-line me-2"></i>Quick Order (POS)</h5>
      </div>
      <div class="card-body">
        <form id="quickOrderForm">
          @csrf
          <div class="row">
            {{-- Left half: Member search --}}
            <div class="col-md-6">
              <div class="mb-4 position-relative">
                <label class="form-label fw-bold">1. Find Member</label>
                <div class="input-group input-group-lg border rounded shadow-sm">
                  <span class="input-group-text bg-white border-0"><i class="ri ri-search-2-line text-muted"></i></span>
                  <input type="text" class="form-control border-0 px-1" id="customer_name" name="customer_name"
                         placeholder="Search by name or card #..." required autocomplete="off">
                </div>
                <div id="customerSuggestions" class="list-group position-absolute w-100 shadow-lg border-0 mt-1"
                     style="z-index:1000; display:none; max-height:280px; overflow-y:auto; border-radius:0 0 12px 12px;"></div>
                <input type="hidden" id="member_id" name="member_id">
              </div>

              {{-- Member info card --}}
              <div class="card bg-label-primary border-0 mb-4" id="memberInfoCard" style="display:none;">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                      <span class="avatar-initial rounded bg-primary"><i class="ri ri-user-star-line"></i></span>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0 fw-bold" id="memberInfoName">-</h6>
                      <small class="text-primary" id="memberInfoCard">-</small>
                    </div>
                    <div class="text-end">
                      <small class="text-muted d-block">Balance</small>
                      <h6 class="mb-0 fw-bold text-success" id="memberInfoBalance">TZS 0</h6>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Special instructions --}}
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="order_notes" name="notes" placeholder="Optional notes">
                <label>Special Instructions</label>
              </div>
            </div>

            {{-- Right half: Item + Qty + Table --}}
            <div class="col-md-6 border-start">
              <label class="form-label fw-bold">2. Select Menu Item</label>
              <select class="form-select form-select-lg mb-3" id="menu_item" name="menu_item_id" required>
                <option value="">Select item...</option>
                @foreach($menuItems ?? [] as $item)
                <option value="{{ $item->id }}" data-price="{{ $item->price }}">
                  {{ $item->name }} — TZS {{ number_format($item->price) }}
                </option>
                @endforeach
              </select>

              <label class="form-label fw-bold">3. Quantity & Table</label>
              <div class="row g-2 mb-3">
                <div class="col-8">
                  <div class="input-group">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">−</button>
                    <input type="number" class="form-control text-center fw-bold fs-5" id="quantity" name="quantity" value="1" min="1">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">+</button>
                  </div>
                </div>
                <div class="col-4">
                  <input type="text" class="form-control" id="table_number" name="table_number" placeholder="Table #">
                </div>
              </div>

              <div class="alert alert-primary d-flex justify-content-between align-items-center p-3 mb-0 shadow-sm border-2">
                <h5 class="mb-0 fw-bold text-primary">Total:</h5>
                <h3 class="mb-0 fw-bold text-primary" id="totalAmount">TZS 0</h3>
              </div>
              <div id="balanceWarningFb" class="alert alert-danger py-2 px-3 mt-2 mb-0 small" style="display:none;">
                <i class="ri ri-error-warning-line me-1"></i>Insufficient member balance!
              </div>
            </div>
          </div>

          <div class="mt-4">
            <input type="hidden" id="payment_method" name="payment_method" value="balance">
            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow-sm fw-black" id="orderBtn" style="letter-spacing: 1px;">
              <i class="ri ri-restaurant-2-line me-2"></i> SEND TO KITCHEN
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Saved Orders Panel (col-4) --}}
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-success"><i class="ri ri-list-check me-2"></i>Saved Orders</h5>
        <button class="btn btn-sm btn-label-secondary" onclick="location.reload()">
          <i class="ri ri-refresh-line"></i>
        </button>
      </div>
      <div class="card-body p-0">
        <div class="alert alert-info border-0 mb-0 py-2 px-4 rounded-0">
          <small><i class="ri ri-information-line me-1"></i>Click an order to view or complete it.</small>
        </div>
        <div class="list-group list-group-flush" id="savedOrdersList">
          @forelse($activeOrders ?? [] as $order)
          <div class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
            <div>
              <h6 class="mb-0 fw-bold small"><code class="text-primary">{{ $order->order_number }}</code> — {{ $order->customer_name }}</h6>
              <small class="text-muted">{{ $order->items->count() }} item(s) • {{ $order->created_at->format('H:i') }}
                @if($order->table_number) • Table {{ $order->table_number }} @endif
              </small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <strong class="text-success small">TZS {{ number_format($order->total_amount) }}</strong>
              <div class="dropdown">
                <button class="btn btn-sm btn-icon btn-label-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                  <i class="ri ri-more-2-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                  <li><a class="dropdown-item view-order-btn" href="javascript:void(0)" data-order-id="{{ $order->id }}">
                    <i class="ri ri-eye-line me-2 text-primary"></i>View Details
                  </a></li>
                  @if(in_array($order->status, ['saved','pending']))
                  <li><a class="dropdown-item text-success complete-order-btn" href="javascript:void(0)" data-order-id="{{ $order->id }}">
                    <i class="ri ri-checkbox-circle-line me-2"></i>Complete
                  </a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-danger cancel-order-btn" href="javascript:void(0)" data-order-id="{{ $order->id }}">
                    <i class="ri ri-close-circle-line me-2"></i>Cancel
                  </a></li>
                  @endif
                </ul>
              </div>
            </div>
          </div>
          @empty
          <div class="text-center py-5 text-muted px-4">
            <i class="ri ri-restaurant-line ri-2x d-block mb-2 opacity-25"></i>
            <p class="mb-0 fw-semibold">No saved orders</p>
            <small>Use Quick Order to add one.</small>
          </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================ --}}
{{-- MENU MANAGEMENT (full-width, Ball Management table style) --}}
{{-- ============================================================ --}}
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold"><i class="ri ri-restaurant-line me-2 text-primary"></i>Menu Catalog</h5>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-label-secondary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="ri ri-folder-add-line me-1"></i> Add Category
          </button>
          <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuItemModal">
            <i class="ri ri-add-line me-1"></i> Add Item
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4 text-uppercase small fw-bold">Item</th>
                <th class="text-uppercase small fw-bold">Category</th>
                <th class="text-uppercase small fw-bold text-end">Price</th>
                <th class="text-uppercase small fw-bold text-center">Prep Time</th>
                <th class="text-uppercase small fw-bold text-center">Status</th>
                <th class="pe-4 text-uppercase small fw-bold text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($menuItems ?? [] as $item)
              <tr>
                <td class="ps-4">
                  <h6 class="mb-0 fw-bold">{{ $item->name }}</h6>
                  @if($item->description)
                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($item->description, 50) }}</small>
                  @endif
                  @if($item->category && $item->category->is_alcohol)
                    <span class="badge bg-label-danger py-0 px-1" style="font-size: 0.6rem;"><i class="ri ri-goblet-line"></i> Alcohol</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-label-secondary">{{ $item->category->name ?? 'Uncategorized' }}</span>
                </td>
                <td class="text-end">
                  <strong class="text-primary">TZS {{ number_format($item->price) }}</strong>
                </td>
                <td class="text-center">
                  <span class="badge bg-label-info"><i class="ri ri-timer-line me-1"></i>{{ $item->prep_time_minutes }} min</span>
                </td>
                <td class="text-center">
                  @if($item->is_available)
                    <span class="badge bg-label-success">Available</span>
                  @else
                    <span class="badge bg-label-danger">Off Menu</span>
                  @endif
                </td>
                <td class="pe-4 text-center">
                  <button class="btn btn-sm btn-icon btn-label-success add-to-order-btn"
                          data-item-id="{{ $item->id }}"
                          data-item-name="{{ $item->name }}"
                          data-item-price="{{ $item->price }}"
                          title="Add to Quick Order">
                    <i class="ri ri-shopping-cart-2-line"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="ri ri-restaurant-line ri-2x d-block mb-2 opacity-25"></i>
                  No menu items yet. Click "Add Item" to get started.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Category Management (Collapsible) --}}
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold text-muted"><i class="ri ri-folder-2-line me-2"></i>Category Management</h5>
        <button class="btn btn-sm btn-label-primary" type="button" data-bs-toggle="collapse" data-bs-target="#categoryListCollapse">
          <i class="ri ri-arrow-down-s-line"></i> View All
        </button>
      </div>
      <div id="categoryListCollapse" class="collapse">
        <div class="card-body p-0 border-top">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-4">Name</th>
                  <th>Type</th>
                  <th class="text-center">Items</th>
                  <th class="pe-4 text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($categories ?? [] as $cat)
                <tr>
                  <td class="ps-4">
                    <span class="fw-bold">{{ $cat->name }}</span>
                    @if($cat->description) <small class="text-muted d-block text-truncate" style="max-width: 200px;">{{ $cat->description }}</small> @endif
                  </td>
                  <td>
                    @if($cat->is_alcohol) <span class="badge bg-label-danger py-0 px-1 me-1">Alcohol</span> @endif
                    @if($cat->is_food) <span class="badge bg-label-warning py-0 px-1">Food</span> @endif
                    @if(!$cat->is_alcohol && !$cat->is_food) <span class="badge bg-label-secondary py-0 px-1">General</span> @endif
                  </td>
                  <td class="text-center"><span class="badge rounded-pill bg-primary">{{ $cat->items_count ?? 0 }}</span></td>
                  <td class="pe-4 text-end">
                    <div class="btn-group">
                      <button class="btn btn-sm btn-icon" onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description) }}', {{ $cat->is_alcohol?1:0 }}, {{ $cat->is_food?1:0 }})">
                        <i class="ri ri-edit-line text-primary"></i>
                      </button>
                      <button class="btn btn-sm btn-icon" onclick="deleteCategory({{ $cat->id }})">
                        <i class="ri ri-delete-bin-line text-danger"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================ --}}
{{-- MODALS --}}
{{-- ============================================================ --}}

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-label-secondary">
        <h5 class="modal-title fw-bold"><i class="ri ri-folder-add-line me-2"></i>Add Menu Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addCategoryForm">
        <div class="modal-body p-4">
          <div class="row mb-4">
            <div class="col-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="category_name" name="name" required placeholder="e.g., Beverages">
                <label for="category_name">Category Name *</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-check form-switch pt-2">
                <input type="checkbox" class="form-check-input" id="category_is_alcohol" name="is_alcohol" value="1" />
                <label class="form-check-label" for="category_is_alcohol">Contains Alcohol</label>
              </div>
              <div class="form-check form-switch pt-2">
                <input type="checkbox" class="form-check-input" id="category_is_food" name="is_food" value="1" />
                <label class="form-check-label" for="category_is_food">Is Food/Kitchen</label>
              </div>
            </div>
          </div>
          <div class="form-floating form-floating-outline">
            <textarea class="form-control" name="description" style="height:90px" placeholder="Optional..."></textarea>
            <label>Description</label>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light py-3">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold">Add Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Add Menu Item Modal --}}
<div class="modal fade" id="addMenuItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-label-secondary">
        <h5 class="modal-title fw-bold"><i class="ri ri-add-box-line me-2"></i>Add Menu Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addMenuItemForm">
        <div class="modal-body p-4">
          <div class="row g-4">
            <div class="col-md-8">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control form-control-lg fw-bold" id="item_name" name="name" required placeholder="e.g., Cheeseburger">
                <label>Item Name *</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <select class="form-select fw-bold" id="item_category" name="category_id" required>
                  <option value="">Select...</option>
                  @foreach($categories ?? [] as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                  @endforeach
                </select>
                <label>Category *</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control fw-bold" id="item_price" name="price" required min="0" step="100" placeholder="0">
                <label>Price (TZS) *</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" name="prep_time_minutes" value="10" min="1" placeholder="10">
                <label>Prep Time (min)</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline">
                <select class="form-select" name="is_available">
                  <option value="1" selected>Available</option>
                  <option value="0">Unavailable</option>
                </select>
                <label>Status</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" name="description" style="height:80px" placeholder="Optional..."></textarea>
                <label>Description</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light py-3">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold">Add Item</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Order Details Modal --}}
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-label-secondary">
        <h5 class="modal-title fw-bold"><i class="ri ri-file-list-3-line me-2"></i>Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="orderDetailsContent"></div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
.bg-label-primary   { background-color: #e7e7ff !important; color: #696cff !important; }
.bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
.bg-label-success   { background-color: #e8fadf !important; color: #71dd37 !important; }
.bg-label-info      { background-color: #d7f5fc !important; color: #03c3ec !important; }
.bg-label-warning   { background-color: #fff2d6 !important; color: #ffab00 !important; }
.bg-label-danger    { background-color: #ffe5e5 !important; color: #ff3e1d !important; }
.bg-success-subtle  { background-color: #d1f2c0 !important; }
.bg-warning-subtle  { background-color: #fff2cc !important; }
.bg-info-subtle     { background-color: #cdf4fe !important; }
</style>
@endpush

@push('scripts')
<script>
const activeOrdersData = {!! json_encode($activeOrders ?? []) !!};
let selectedMemberBalance = 0;
let selectedMemberId = null;

// Auto-fill table number from URL
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  const tableNum = urlParams.get('table');
  if (tableNum) {
    const tableInput = document.getElementById('table_number');
    if (tableInput) {
      tableInput.value = tableNum;
      tableInput.classList.add('bg-label-info', 'fw-bold');
    }
  }
});

// ============================================================
// Event delegation
// ============================================================
document.addEventListener('click', function(e) {
  if (e.target.closest('.add-to-order-btn')) {
    e.preventDefault();
    const btn = e.target.closest('.add-to-order-btn');
    window.addToOrder?.(parseInt(btn.dataset.itemId), btn.dataset.itemName, parseFloat(btn.dataset.itemPrice));
    return;
  }
  if (e.target.closest('.view-order-btn')) { e.preventDefault(); window.viewOrder?.(parseInt(e.target.closest('.view-order-btn').dataset.orderId), e); return; }
  if (e.target.closest('.complete-order-btn')) { e.preventDefault(); window.updateOrderStatus?.(parseInt(e.target.closest('.complete-order-btn').dataset.orderId), 'complete', e); return; }
  if (e.target.closest('.cancel-order-btn')) { e.preventDefault(); window.cancelOrder?.(parseInt(e.target.closest('.cancel-order-btn').dataset.orderId), e); return; }
  if (!e.target.closest('#customer_name') && !e.target.closest('#customerSuggestions')) {
    document.getElementById('customerSuggestions').style.display = 'none';
  }
});

// ============================================================
// Member search
// ============================================================
document.getElementById('customer_name').addEventListener('input', function() {
  const q = this.value.trim();
  if (q.length < 2) { document.getElementById('customerSuggestions').style.display = 'none'; return; }
  loadMembers(q);
});
document.getElementById('customer_name').addEventListener('focus', function() { loadMembers(this.value.trim()); });

function loadMembers(query) {
  fetch('{{ url("payments/members/search") }}?tier=1&q=' + encodeURIComponent(query), {
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  })
  .then(r => r.json())
  .then(members => {
    const box = document.getElementById('customerSuggestions');
    if (!members?.length) {
      box.innerHTML = '<div class="list-group-item text-muted text-center small">No members found</div>';
      box.style.display = 'block'; return;
    }
    box.innerHTML = members.map(m => {
      const safeName = (m.name || '').replace(/'/g, "\\'");
      const safeCard = (m.card_number || '').replace(/'/g, "\\'");
      return `<div class="list-group-item list-group-item-action" style="cursor:pointer" onclick="selectMember(${m.id}, '${safeName}', '${safeCard}', ${m.balance || 0})">
        <div class="d-flex justify-content-between align-items-center">
          <div><strong>${m.name || 'N/A'}</strong><br><small class="text-muted">${m.card_number || ''}</small></div>
          <span class="badge bg-label-${m.balance > 0 ? 'success' : 'secondary'}">TZS ${parseFloat(m.balance || 0).toLocaleString()}</span>
        </div>
      </div>`;
    }).join('');
    box.style.display = 'block';
  });
}

function selectMember(memberId, name, card, balance) {
  selectedMemberId = memberId;
  selectedMemberBalance = parseFloat(balance || 0);
  document.getElementById('member_id').value = memberId;
  document.getElementById('customer_name').value = name + (card ? ' (' + card + ')' : '');
  document.getElementById('customerSuggestions').style.display = 'none';

  document.getElementById('memberInfoName').textContent = name;
  document.getElementById('memberInfoCard').textContent = card || '-';
  document.getElementById('memberInfoBalance').textContent = 'TZS ' + selectedMemberBalance.toLocaleString();
  document.getElementById('memberInfoCard_display').style.display = 'block';

  updateTotal();
}

// ============================================================
// Total
// ============================================================
function changeQuantity(delta) {
  const input = document.getElementById('quantity');
  input.value = Math.max(1, (parseInt(input.value) || 1) + delta);
  updateTotal();
}

document.getElementById('menu_item').addEventListener('change', updateTotal);
document.getElementById('quantity').addEventListener('input', updateTotal);

function updateTotal() {
  const menuEl = document.getElementById('menu_item');
  const opt = menuEl.options[menuEl.selectedIndex];
  const qty = parseInt(document.getElementById('quantity').value) || 1;
  const price = parseFloat(opt?.dataset.price || 0);
  const total = price * qty;
  document.getElementById('totalAmount').textContent = 'TZS ' + total.toLocaleString();

  const warn = document.getElementById('balanceWarningFb');
  warn.style.display = (selectedMemberId && price > 0 && selectedMemberBalance < total) ? 'block' : 'none';
}

// ============================================================
// addToOrder from menu catalog
// ============================================================
function addToOrder(itemId, itemName, price) {
  document.getElementById('menu_item').value = itemId;
  document.getElementById('quantity').value = 1;
  updateTotal();
  document.getElementById('quickOrderForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
  if (typeof Swal !== 'undefined') {
    Swal.fire({ title: 'Added: ' + itemName, toast: true, position: 'top-end', timer: 1800, showConfirmButton: false, icon: 'success', background: '#696cff', color: '#fff', iconColor: '#fff' });
  }
}
window.addToOrder = addToOrder;

// ============================================================
// Quick Order submit
// ============================================================
document.getElementById('quickOrderForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const menuEl = document.getElementById('menu_item');
  const qty = parseInt(document.getElementById('quantity').value) || 1;
  const memberId = document.getElementById('member_id').value;

  if (!menuEl.value) { showWarning('Please select a menu item'); return; }
  if (!memberId) { showWarning('Please select a member first'); return; }

  const price = parseFloat(menuEl.options[menuEl.selectedIndex].dataset.price) || 0;
  const total = price * qty;
  if (selectedMemberBalance < total) {
    showError('Insufficient balance. Required: TZS ' + total.toLocaleString() + ', Available: TZS ' + selectedMemberBalance.toLocaleString());
    return;
  }

  const btn = document.getElementById('orderBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

  fetch('{{ route("services.orders.store") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify({
      member_id: parseInt(memberId),
      customer_name: document.getElementById('customer_name').value,
      table_number: document.getElementById('table_number').value,
      payment_method: 'balance',
      notes: document.getElementById('order_notes').value,
      items: [{ menu_item_id: parseInt(menuEl.value), quantity: qty }]
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showTransactionSuccess({ title: 'Order Sent to Kitchen!', amount: data.order.total_amount, new_balance: data.new_balance, order_number: data.order.order_number })
        .then(() => location.reload());
    } else {
      showError(data.message || 'Failed to create order');
      btn.disabled = false;
      btn.innerHTML = '<i class="ri ri-restaurant-2-line me-2"></i> SEND TO KITCHEN';
    }
  })
  .catch(() => {
    showError('An error occurred. Please try again.');
    btn.disabled = false;
    btn.innerHTML = '<i class="ri ri-restaurant-2-line me-2"></i> SEND TO KITCHEN';
  });
});

// ============================================================
// Order management
// ============================================================
function updateOrderStatus(orderId, status, event) {
  const msg = status === 'cancelled' ? 'Cancel this order? Balance will be refunded.' : 'Mark this order as complete?';
  showConfirm(msg).then(result => {
    if (!result.isConfirmed) return;
    fetch('{{ url("services/orders") }}/' + orderId + '/status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
      body: JSON.stringify({ status })
    }).then(r => r.json()).then(data => {
      if (data.success) showSuccess(data.message || 'Order updated').then(() => location.reload());
      else showError(data.message || 'Failed');
    });
  });
}
function cancelOrder(orderId, event) {
  showConfirm('Cancel this order? Balance will be refunded.').then(r => { if (r.isConfirmed) updateOrderStatus(orderId, 'cancelled', event); });
}
function viewOrder(orderId, event) {
  const content = document.getElementById('orderDetailsContent');
  content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
  new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
  let order = activeOrdersData.find(o => parseInt(o.id) === parseInt(orderId));
  if (order?.items?.length) { displayOrderDetails(order, content); return; }
  fetch('{{ url("services/orders") }}/' + orderId, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(r => r.json())
    .then(data => data.success ? displayOrderDetails(data.order, content) : Promise.reject(data.message))
    .catch(err => { content.innerHTML = '<div class="alert alert-danger">Failed to load: ' + err + '</div>'; });
}
function displayOrderDetails(order, container) {
  const sc = ['saved','pending'].includes(order.status) ? 'warning' : ['complete','completed'].includes(order.status) ? 'success' : 'secondary';
  const items = (order.items || []).map((item, i) => {
    const name = item.menu_item?.name || item.menuItem?.name || item.name || 'Item';
    const qty = item.quantity || 0;
    const price = parseFloat(item.unit_price || item.price || 0);
    const sub = parseFloat(item.subtotal || price * qty);
    return `<tr><td>${i+1}</td><td><strong>${name}</strong></td><td class="text-center"><span class="badge bg-label-primary">${qty}</span></td><td class="text-end">TZS ${price.toLocaleString()}</td><td class="text-end"><strong>TZS ${sub.toLocaleString()}</strong></td></tr>`;
  }).join('') || '<tr><td colspan="5" class="text-center text-muted">No items</td></tr>';
  container.innerHTML = `
    <div class="row g-3 mb-4">
      <div class="col-md-4"><small class="text-muted d-block">Order No.</small><strong class="text-primary">${order.order_number || '-'}</strong></div>
      <div class="col-md-4"><small class="text-muted d-block">Customer</small><strong>${order.customer_name || '-'}</strong></div>
      <div class="col-md-4"><small class="text-muted d-block">Table</small><strong>${order.table_number || 'N/A'}</strong></div>
      <div class="col-md-4"><small class="text-muted d-block">Status</small><span class="badge bg-label-${sc}">${order.status ? order.status.charAt(0).toUpperCase()+order.status.slice(1) : '-'}</span></div>
      <div class="col-md-4"><small class="text-muted d-block">Payment</small><span class="badge bg-label-success">Member Balance</span></div>
      <div class="col-md-4"><small class="text-muted d-block">Total</small><h5 class="mb-0 text-primary">TZS ${parseFloat(order.total_amount || 0).toLocaleString()}</h5></div>
    </div>
    <table class="table table-hover"><thead class="table-light">
      <tr><th>#</th><th>Item</th><th class="text-center">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Subtotal</th></tr>
    </thead><tbody>${items}</tbody>
    <tfoot><tr class="fw-bold"><td colspan="4" class="text-end">Total:</td><td class="text-end text-primary">TZS ${parseFloat(order.total_amount || 0).toLocaleString()}</td></tr></tfoot></table>
    ${order.notes ? `<div class="p-3 bg-light rounded mt-2"><small class="fw-bold text-muted d-block">Notes:</small>${order.notes}</div>` : ''}`;
}
window.updateOrderStatus = updateOrderStatus;
window.viewOrder = viewOrder;
window.cancelOrder = cancelOrder;

// ============================================================
// Category & Menu Item management
// ============================================================
document.getElementById('addCategoryForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  fetch('{{ route("services.categories.store") }}', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: new FormData(this) })
    .then(r=>r.json()).then(data => { if (data.success) { bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide(); location.reload(); } else showError(data.message); });
});
function editCategory(id, name, description, isAlcohol, isFood) {
  const newName = prompt('Edit category name:', name);
  if (newName && (newName !== name || true)) {
    fetch('{{ url("services/categories") }}/' + id, { 
      method:'PUT', 
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, 
      body: JSON.stringify({
        name: newName, 
        description: description||'',
        is_alcohol: isAlcohol,
        is_food: isFood,
        is_alcohol_toggle: true // Flag to tell controller to handle checkboxes
      }) 
    })
    .then(r=>r.json()).then(data => data.success ? location.reload() : showError(data.message));
  }
}
function deleteCategory(id) {
  showConfirm('Delete this category? Items will become uncategorized.').then(r => {
    if (!r.isConfirmed) return;
    fetch('{{ url("services/categories") }}/' + id, { method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'} })
      .then(r=>r.json()).then(data => data.success ? location.reload() : showError(data.message));
  });
}
document.getElementById('addMenuItemForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.set('is_available', formData.get('is_available') === '1' ? 1 : 0);
  const btn = this.querySelector('button[type="submit"]');
  const orig = btn?.innerHTML;
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...'; }
  fetch('{{ route("services.menu-items.store") }}', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: formData })
    .then(r=>r.json()).then(data => {
      if (data.success) { bootstrap.Modal.getInstance(document.getElementById('addMenuItemModal'))?.hide(); showSuccess(data.message||'Item added!').then(()=>location.reload()); }
      else { showError(data.message||'Failed'); if (btn) { btn.disabled=false; btn.innerHTML=orig; } }
    });
});

// Fix: expose memberInfoCard element reference
document.getElementById('memberInfoCard_display')?.remove(); // cleanup if exists
const el = document.getElementById('memberInfoCard');
if (el) el.id = 'memberInfoCard';

// Auto-refresh
setInterval(() => {
  if (document.visibilityState !== 'visible') return;
  fetch('{{ route("services.food-beverage") }}', { headers: { 'Accept': 'text/html' } })
    .then(r => r.text()).then(html => {
      const doc = new DOMParser().parseFromString(html, 'text/html');
      const nb = doc.querySelector('#savedOrdersList');
      if (nb) document.getElementById('savedOrdersList').innerHTML = nb.innerHTML;
    }).catch(()=>{});
}, 30000);
</script>
@endpush
@endsection
