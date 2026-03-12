@extends('settings._layout-base')

@section('title', 'Equipment Sales')
@section('description', 'Equipment Sales - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Golf Services /</span> Pro Shop Sales
</h4>

{{-- ============================================================ --}}
{{-- HERO INVENTORY BANNER (Ball Management style) --}}
{{-- ============================================================ --}}
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm overflow-hidden">
      <div class="card-body p-0">
        <div class="row g-0">

          {{-- Left: inventory overview --}}
          <div class="col-md-8 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0 fw-bold"><i class="ri ri-store-2-line me-2 text-primary"></i>Live Inventory Overview</h5>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                  <i class="ri ri-add-line me-1"></i> Add Product
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                  <i class="ri ri-refresh-line me-1"></i> Refresh
                </button>
              </div>
            </div>

            @php
              $totalItems  = $stats['items_in_stock'] ?? 0;
              $lowStock    = $stats['low_stock'] ?? 0;
              $salesToday  = $stats['sales_today'] ?? 0;
              $inStockQty  = $totalItems > 0 ? $totalItems - $lowStock : $totalItems;
              $inStockPct  = $totalItems > 0 ? ($inStockQty / $totalItems) * 100 : 0;
              $lowPct      = $totalItems > 0 ? ($lowStock / $totalItems) * 100 : 0;
            @endphp

            {{-- Product stock progress bar --}}
            <div class="progress mb-4" style="height: 42px; border-radius: 12px; background-color: #f1f3f9;">
              <div class="progress-bar bg-success" role="progressbar" style="width: {{ $inStockPct }}%"
                   title="In Stock: {{ $inStockQty }}">
                <span class="fw-bold">{{ number_format($inStockQty) }} In Stock</span>
              </div>
              <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $lowPct }}%"
                   title="Low Stock: {{ $lowStock }}">
                <span class="fw-bold">{{ $lowStock }}</span>
              </div>
            </div>

            <div class="row g-4">
              <div class="col-md-4">
                <div class="p-3 border rounded bg-light-subtle">
                  <small class="text-muted d-block mb-1">Total Products</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($totalItems) }} <span class="fs-6 fw-normal text-muted">Items</span></h4>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 border border-success rounded bg-success-subtle">
                  <small class="text-success d-block mb-1">In Stock</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($inStockQty) }}</h4>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 border border-warning rounded bg-warning-subtle text-warning">
                  <small class="d-block mb-1">Low / Out of Stock</small>
                  <h4 class="mb-0 fw-bold">{{ number_format($lowStock) }}</h4>
                </div>
              </div>
            </div>
          </div>

          {{-- Right: revenue summary --}}
          <div class="col-md-4 bg-primary text-white p-4">
            <h5 class="text-white fw-bold mb-4">Today's Sales Summary</h5>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>Sales Count</span>
                <strong>{{ $salesToday }}</strong>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-white" style="width: 70%"></div>
              </div>
            </div>
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span>Revenue</span>
                <strong>TZS {{ number_format($stats['revenue_today'] ?? 0) }}</strong>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 80%"></div>
              </div>
            </div>
            <div class="pt-2">
              <div class="alert bg-white text-primary border-0 mb-0 py-3">
                <small class="d-block fw-semibold mb-1">Total Revenue Today</small>
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
{{-- PRIMARY ACTIONS: Quick Sale (col-8) + Recent Sales (col-4) --}}
{{-- ============================================================ --}}
<div class="row mb-6">

  {{-- Quick Sale Form (col-8) --}}
  <div class="col-md-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="ri ri-price-tag-3-line me-2"></i>Quick Sale (POS)</h5>
      </div>
      <div class="card-body">
        <div class="row">

          {{-- Left half: member search --}}
          <div class="col-md-6">
            <div class="mb-4 position-relative">
              <label class="form-label fw-bold">1. Find Member</label>
              <div class="input-group input-group-lg border rounded shadow-sm">
                <span class="input-group-text bg-white border-0"><i class="ri ri-search-2-line text-muted"></i></span>
                <input type="text" class="form-control border-0 px-1" id="qs_member_search"
                       placeholder="Search by name or card #..." autocomplete="off">
              </div>
              <div id="qsMemberSuggestions" class="list-group position-absolute w-100 shadow-lg border-0 mt-1"
                   style="z-index:1000; display:none; max-height:260px; overflow-y:auto; border-radius:0 0 12px 12px;"></div>
              <input type="hidden" id="qs_member_id">
            </div>

            {{-- Member info card (shown after selection) --}}
            <div class="card bg-label-primary border-0 mb-4" id="qsMemberInfoCard" style="display:none;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-primary"><i class="ri ri-user-star-line"></i></span>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold" id="qsMemberName">-</h6>
                    <small class="text-primary" id="qsMemberCard">-</small>
                  </div>
                  <div class="text-end">
                    <small class="text-muted d-block">Balance</small>
                    <h6 class="mb-0 fw-bold text-success" id="qsMemberBalance">TZS 0</h6>
                  </div>
                </div>
              </div>
            </div>

            {{-- Notes --}}
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="qs_notes" placeholder="Optional notes">
              <label for="qs_notes">Notes (optional)</label>
            </div>
          </div>

          {{-- Right half: product + qty + discount --}}
          <div class="col-md-6 border-start">
            <label class="form-label fw-bold">2. Select Product</label>
            <select class="form-select form-select-lg mb-2" id="qs_product" onchange="qsUpdateProduct()">
              <option value="">Select product...</option>
              @foreach($equipment ?? [] as $eq)
              <option value="{{ $eq->id }}"
                      data-price="{{ $eq->sale_price }}"
                      data-stock="{{ $eq->available_quantity }}"
                      data-name="{{ $eq->name }}">
                {{ $eq->name }} — TZS {{ number_format($eq->sale_price) }}
              </option>
              @endforeach
            </select>
            {{-- Stock badge --}}
            <div id="qsStockBadge" class="mb-3" style="display:none;">
              <span class="badge fs-7 py-1 px-2" id="qsStockLabel">0 in stock</span>
            </div>

            <label class="form-label fw-bold">3. Quantity &amp; Discount</label>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <div class="input-group">
                  <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('qs_qty').value=Math.max(1,parseInt(document.getElementById('qs_qty').value||1)-1);qsUpdateTotal()">−</button>
                  <input type="number" class="form-control text-center fw-bold fs-5" id="qs_qty" value="1" min="1" onchange="qsUpdateTotal()">
                  <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('qs_qty').value=(parseInt(document.getElementById('qs_qty').value||1)+1);qsUpdateTotal()">+</button>
                </div>
              </div>
              <div class="col-6">
                <div class="form-floating form-floating-outline">
                  <input type="number" class="form-control" id="qs_discount" value="0" min="0" onchange="qsUpdateTotal()" placeholder="0">
                  <label>Discount (TZS)</label>
                </div>
              </div>
            </div>

            <div class="alert alert-primary d-flex justify-content-between align-items-center p-3 mb-0 shadow-sm">
              <h5 class="mb-0 fw-bold text-primary">Grand Total:</h5>
              <h3 class="mb-0 fw-bold text-primary" id="qs_total">TZS 0</h3>
            </div>
            <div id="qsBalanceWarning" class="alert alert-danger py-2 px-3 mt-2 mb-0 small" style="display:none;">
              <i class="ri ri-error-warning-line me-1"></i>Insufficient member balance!
            </div>
          </div>
        </div>

        <div class="mt-4">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="form-check mb-0">
              <input class="form-check-input" type="checkbox" id="qs_send_sms" checked>
              <label class="form-check-label" for="qs_send_sms">
                <i class="ri ri-message-2-line me-1"></i>Send SMS receipt
              </label>
            </div>
          </div>
          <button type="button" class="btn btn-primary btn-lg w-100 py-3 shadow-sm fw-bold" id="qsSellBtn" onclick="quickSell()" disabled>
            <i class="ri ri-check-double-line me-2"></i> CONFIRM &amp; SELL
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Recent Transactions (col-4) --}}
  <div class="col-md-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-success"><i class="ri ri-history-line me-2"></i>Recent Sales</h5>
        <button class="btn btn-sm btn-label-secondary" onclick="location.reload()">
          <i class="ri ri-refresh-line"></i>
        </button>
      </div>
      <div class="card-body p-0">
        <div class="alert alert-info border-0 mb-0 py-2 px-4 rounded-0">
          <small><i class="ri ri-information-line me-1"></i>Today's completed transactions.</small>
        </div>
        <div class="list-group list-group-flush">
          @forelse($todaySales ?? [] as $sale)
          <div class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
            <div>
              <h6 class="mb-0 fw-bold small"><code class="text-primary">#{{ $sale->id }}</code> — {{ $sale->customer_name }}</h6>
              <small class="text-muted">{{ $sale->items->count() }} item(s) • {{ $sale->created_at->format('H:i') }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <strong class="text-success small text-nowrap">TZS {{ number_format($sale->total_amount) }}</strong>
              <div class="d-flex gap-1">
                <button class="btn btn-sm btn-icon btn-label-primary" onclick="viewSale({{ $sale->id }})" title="View">
                  <i class="ri ri-eye-line"></i>
                </button>
                <button class="btn btn-sm btn-icon btn-label-secondary" onclick="printReceipt({{ $sale->id }})" title="Print">
                  <i class="ri ri-printer-line"></i>
                </button>
              </div>
            </div>
          </div>
          @empty
          <div class="text-center py-5 text-muted">
            <i class="ri ri-shopping-cart-line ri-2x d-block mb-2 opacity-25"></i>
            <p class="mb-0 fw-semibold">No sales today</p>
            <small>Completed transactions will appear here.</small>
          </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================ --}}
{{-- EQUIPMENT CATALOG (full-width table) --}}
{{-- ============================================================ --}}
<div class="row mb-6">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold"><i class="ri ri-store-2-line me-2 text-primary"></i>Equipment Catalog</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
          <i class="ri ri-add-line me-1"></i> Add New Product
        </button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="ps-4 text-uppercase small fw-bold">Product</th>
                <th class="text-uppercase small fw-bold">SKU</th>
                <th class="text-uppercase small fw-bold">Category</th>
                <th class="text-end text-uppercase small fw-bold">Price (TZS)</th>
                <th class="text-center text-uppercase small fw-bold">Stock</th>
                <th class="text-uppercase small fw-bold">Status</th>
                <th class="pe-4 text-uppercase small fw-bold">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($equipment ?? [] as $eq)
              <tr>
                <td class="ps-4">
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <div class="avatar-initial bg-label-primary rounded-circle">
                        <i class="ri ri-golf-ball-line"></i>
                      </div>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-bold">{{ $eq->name }}</h6>
                      <small class="text-muted text-truncate d-inline-block" style="max-width:180px;">{{ $eq->description ?? '-' }}</small>
                    </div>
                  </div>
                </td>
                <td><code class="text-primary fw-medium">{{ $eq->sku }}</code></td>
                <td><span class="badge bg-label-secondary text-capitalize">{{ $eq->category }}</span></td>
                <td class="text-end fw-bold text-dark">{{ number_format($eq->sale_price) }}</td>
                <td class="text-center">
                  <span class="badge {{ $eq->available_quantity > $eq->low_stock_threshold ? 'bg-label-success' : ($eq->available_quantity > 0 ? 'bg-label-warning' : 'bg-label-danger') }} rounded-pill px-3">
                    {{ $eq->available_quantity }}
                  </span>
                </td>
                <td>
                  @if($eq->available_quantity > $eq->low_stock_threshold)
                  <span class="text-success small fw-medium text-nowrap"><i class="ri ri-checkbox-circle-line me-1"></i>In Stock</span>
                  @elseif($eq->available_quantity > 0)
                  <span class="text-warning small fw-medium text-nowrap"><i class="ri ri-alert-line me-1"></i>Low Stock</span>
                  @else
                  <span class="text-danger small fw-medium text-nowrap"><i class="ri ri-close-circle-line me-1"></i>Out of Stock</span>
                  @endif
                </td>
                <td class="pe-4">
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-icon btn-label-success" title="Quick Sell"
                            onclick="selectProductForSale({{ $eq->id }}, '{{ addslashes($eq->name) }}', {{ $eq->sale_price }}, {{ $eq->available_quantity }})">
                      <i class="ri ri-shopping-cart-2-line"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-label-primary" title="Edit" onclick="editProduct({{ $eq->id }})">
                      <i class="ri ri-pencil-line"></i>
                    </button>
                    <button class="btn btn-sm btn-icon btn-label-secondary" title="Add Stock"
                            onclick="openAddStockModal({{ $eq->id }}, '{{ addslashes($eq->name) }}', {{ $eq->total_quantity }})">
                      <i class="ri ri-add-circle-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="ri ri-inbox-line ri-3x opacity-20 d-block mb-2"></i>
                  No products in catalog. Add your first product.
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

{{-- ============================================================ --}}
{{-- MODALS --}}
{{-- ============================================================ --}}

{{-- View Sale Modal --}}
<div class="modal fade" id="viewSaleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header card-header-premium border-0 py-4">
        <h5 class="modal-title text-white fw-bold">
          <i class="ri ri-receipt-line me-2"></i>Sale Details #<span id="saleModalId">-</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="saleModalLoading" class="text-center py-5">
          <div class="spinner-border text-primary"></div>
          <p class="mt-2 text-muted">Loading...</p>
        </div>
        <div id="saleModalContent" style="display:none;">
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="card border">
                <div class="card-body">
                  <h6 class="text-muted mb-3">Sale Information</h6>
                  <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">Sale ID:</span><strong id="saleInfoId">-</strong></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">Date & Time:</span><strong id="saleInfoDate">-</strong></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">Status:</span><span id="saleInfoStatus"></span></div>
                  <div class="d-flex justify-content-between"><span class="text-body-secondary">Payment:</span><span id="saleInfoPayment"></span></div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border">
                <div class="card-body">
                  <h6 class="text-muted mb-3">Customer Information</h6>
                  <div class="mb-2"><div class="text-body-secondary small">Name:</div><strong id="saleCustomerName">-</strong></div>
                  <div class="mb-2"><div class="text-body-secondary small">Card Number:</div><strong id="saleCustomerCard">-</strong></div>
                  <div><div class="text-body-secondary small">Phone:</div><strong id="saleCustomerPhone">-</strong></div>
                </div>
              </div>
            </div>
          </div>
          <div class="card border mb-4">
            <div class="card-header bg-light"><h6 class="mb-0"><i class="ri ri-list-check me-2"></i>Items Purchased</h6></div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr><th>#</th><th>Product</th><th class="text-end">Unit Price</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr>
                  </thead>
                  <tbody id="saleItemsTableBody"></tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-8">
              <div class="card border"><div class="card-body"><h6 class="text-muted mb-2">Notes</h6><p id="saleNotes" class="mb-0 text-body">-</p></div></div>
            </div>
            <div class="col-md-4">
              <div class="card border">
                <div class="card-body">
                  <h6 class="text-muted mb-3">Financial Summary</h6>
                  <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">Subtotal:</span><strong id="saleSubtotal">TZS 0</strong></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">Discount:</span><strong id="saleDiscount">TZS 0</strong></div>
                  <hr>
                  <div class="d-flex justify-content-between"><span class="fw-bold">Total:</span><strong class="text-primary fs-5" id="saleTotal">TZS 0</strong></div>
                  <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between"><span class="text-body-secondary small">SMS Sent:</span><span id="saleSmsStatus"></span></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="saleModalError" style="display:none;" class="alert alert-danger">
          <i class="ri ri-error-warning-line me-2"></i><span id="saleModalErrorMessage">Failed to load sale details</span>
        </div>
      </div>
      <div class="modal-footer border-0 bg-light">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="printReceiptFromModal()">
          <i class="ri ri-printer-line me-1"></i> Print Receipt
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Edit Product Modal --}}
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header card-header-premium border-0 py-4">
        <h5 class="modal-title text-white fw-bold"><i class="ri ri-pencil-line me-2"></i>Edit Product Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="editProductForm">
          <input type="hidden" id="edit_product_id" name="id">
          <div class="row g-4">
            <div class="col-md-7">
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" class="form-control form-control-lg fw-bold" id="edit_product_name" name="name" required>
                <label>Product Name *</label>
              </div>
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control" id="edit_product_description" name="description" style="height:100px"></textarea>
                <label>Description</label>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" class="form-control fw-bold" id="edit_product_sku" name="sku" required>
                <label>SKU *</label>
              </div>
              <div class="form-floating form-floating-outline mb-4">
                <select class="form-select fw-bold" id="edit_product_category" name="category" required>
                  <option value="clubs">Golf Clubs</option>
                  <option value="balls">Golf Balls</option>
                  <option value="apparel">Apparel</option>
                  <option value="accessories">Accessories</option>
                  <option value="shoes">Shoes</option>
                </select>
                <label>Category *</label>
              </div>
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control fw-bold fs-5 text-primary" id="edit_product_price" name="sale_price" required>
                <label>Sale Price (TZS) *</label>
              </div>
            </div>
            <div class="col-12">
              <hr class="my-2">
              <div class="row g-4">
                <div class="col-md-6">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control fw-bold" id="edit_product_threshold" name="low_stock_threshold">
                    <label>Low Stock Alert Level</label>
                  </div>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                  <div class="form-check form-switch card p-2 px-3 border-0 bg-light w-100 mb-0">
                    <input type="checkbox" class="form-check-input" id="edit_product_sellable" name="is_sellable">
                    <label class="form-check-label fw-bold" for="edit_product_sellable">Available for Sale</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer bg-light border-0 py-3">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-4 fw-bold" onclick="updateProduct()">
          <i class="ri ri-save-line me-1"></i> UPDATE PRODUCT
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Add Stock Modal --}}
<div class="modal fade" id="addStockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header card-header-premium border-0 py-4">
        <h5 class="modal-title text-white fw-bold"><i class="ri ri-add-circle-line me-2"></i>Update Stock Level</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="avatar avatar-xl bg-label-primary rounded-circle mb-4 mx-auto">
          <i class="ri ri-store-2-line fs-2"></i>
        </div>
        <h5 class="mb-1 fw-bold" id="stockProductName">-</h5>
        <p class="text-muted mb-4">Current Stock: <span class="badge bg-label-info fw-bold" id="currentStockDisplay">0</span></p>
        <form id="addStockForm">
          <input type="hidden" id="stock_product_id" name="id">
          <div class="form-floating form-floating-outline mb-0">
            <input type="number" class="form-control form-control-lg fw-bold text-center" id="stock_to_add" name="quantity_to_add" placeholder="0" required>
            <label>Quantity to Add (Use negative to remove)</label>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 py-3 justify-content-center">
        <button type="button" class="btn btn-label-secondary px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-5 fw-bold" onclick="confirmAddStock()">
          <i class="ri ri-check-line me-1"></i> CONFIRM STOCK UPDATE
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Add Product Modal --}}
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header card-header-premium border-0 py-4">
        <h5 class="modal-title text-white fw-bold"><i class="ri ri-add-box-line me-2"></i>Add New Product</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="addProductForm">
          <div class="row g-4">
            <div class="col-md-7">
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" class="form-control form-control-lg fw-bold" id="product_name" name="name" placeholder="e.g., Titleist Pro V1" required>
                <label>Product Name *</label>
              </div>
              <div class="form-floating form-floating-outline mb-4">
                <textarea class="form-control" id="product_description" name="description" style="height:100px" placeholder="Optional details..."></textarea>
                <label>Description</label>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-floating form-floating-outline mb-4">
                <input type="text" class="form-control fw-bold" id="product_sku" name="sku" placeholder="Leave empty for AUTO">
                <label>SKU (Auto-generated if empty)</label>
              </div>
              <div class="form-floating form-floating-outline mb-4">
                <select class="form-select fw-bold" id="product_category" name="category" required>
                  <option value="">Select Category</option>
                  <option value="clubs">Golf Clubs</option>
                  <option value="balls">Golf Balls</option>
                  <option value="apparel">Apparel</option>
                  <option value="accessories">Accessories</option>
                  <option value="shoes">Shoes</option>
                </select>
                <label>Category *</label>
              </div>
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control fw-bold fs-5 text-primary" id="product_price" name="sale_price" placeholder="0" required>
                <label>Sale Price (TZS) *</label>
              </div>
            </div>
            <div class="col-12">
              <hr class="my-2">
              <h6 class="mb-3 text-muted">Inventory Settings</h6>
              <div class="row g-4">
                <div class="col-md-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control fw-bold" id="product_stock" name="total_quantity" value="0">
                    <label>Initial Stock</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating form-floating-outline">
                    <input type="number" class="form-control fw-bold" id="product_threshold" name="low_stock_threshold" value="5">
                    <label>Low Stock Alert</label>
                  </div>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                  <div class="form-check form-switch card p-2 px-3 border-0 bg-light w-100 mb-0">
                    <input type="checkbox" class="form-check-input" id="product_sellable" name="is_sellable" checked>
                    <label class="form-check-label fw-bold" for="product_sellable">Available for Sale</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer bg-light border-0 py-3">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-4 fw-bold" onclick="addProduct()">
          <i class="ri ri-check-line me-1"></i> SAVE PRODUCT
        </button>
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
.bg-white-transparent { background-color: rgba(255,255,255,0.2); }
.bg-success-subtle  { background-color: #d1f2c0 !important; }
.bg-warning-subtle  { background-color: #fff2cc !important; }
.cursor-pointer { cursor: pointer; }
.hover-bg-light:hover { background-color: #f8f9fa; }
.card-header-premium {
  background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%);
  color: white;
}
</style>
@endpush
@endsection

@push('scripts')
<script>
// ============================================================
// QUICK SALE
// ============================================================
let qsMember = null;

// Member autocomplete
document.getElementById('qs_member_search').addEventListener('input', function() {
  const query = this.value;
  const suggestions = document.getElementById('qsMemberSuggestions');
  if (query.length < 2) { suggestions.style.display = 'none'; return; }

  fetch(`{{ route('payments.members.search') }}?query=${encodeURIComponent(query)}`)
    .then(r => r.json())
    .then(data => {
      if (!data.length) { suggestions.style.display = 'none'; return; }
      suggestions.innerHTML = data.map(m => `
        <div class="list-group-item list-group-item-action" style="cursor:pointer"
             onclick="qsSelectMember(${m.id}, '${m.name.replace(/'/g, "\\'")}', '${m.card_number}', ${m.balance})">
          <div class="fw-bold">${m.name}</div>
          <div class="small text-muted d-flex justify-content-between">
            <span>${m.card_number}</span>
            <span class="text-primary fw-bold">TZS ${parseFloat(m.balance).toLocaleString()}</span>
          </div>
        </div>`).join('');
      suggestions.style.display = 'block';
    });
});

document.addEventListener('click', e => {
  if (!e.target.closest('#qs_member_search') && !e.target.closest('#qsMemberSuggestions')) {
    document.getElementById('qsMemberSuggestions').style.display = 'none';
  }
});

function qsSelectMember(id, name, card, balance) {
  qsMember = { id, name, card, balance };
  document.getElementById('qs_member_id').value = id;
  document.getElementById('qs_member_search').value = name;
  document.getElementById('qsMemberSuggestions').style.display = 'none';

  document.getElementById('qsMemberName').textContent = name;
  document.getElementById('qsMemberCard').textContent = card || '-';
  document.getElementById('qsMemberBalance').textContent = 'TZS ' + parseFloat(balance).toLocaleString();
  document.getElementById('qsMemberInfoCard').style.display = 'block';

  qsUpdateTotal();
}

function selectProductForSale(id, name, price, stock) {
  if (stock <= 0) { showWarning('Product is out of stock'); return; }
  document.getElementById('qs_product').value = id;
  qsUpdateProduct();
  document.getElementById('qs_member_search').scrollIntoView({ behavior: 'smooth', block: 'start' });
  if (typeof Swal !== 'undefined') {
    Swal.fire({ title: 'Product selected: ' + name, toast: true, position: 'top-end', timer: 1800, showConfirmButton: false, icon: 'success', background: '#696cff', color: '#fff', iconColor: '#fff' });
  }
}

function qsUpdateProduct() {
  const select = document.getElementById('qs_product');
  const opt = select.options[select.selectedIndex];
  if (!opt.value) { document.getElementById('qsStockBadge').style.display = 'none'; return; }
  const stock = parseInt(opt.dataset.stock);
  const label = document.getElementById('qsStockLabel');
  label.textContent = stock + ' in stock';
  label.className = 'badge fs-7 py-1 px-2 ' + (stock > 5 ? 'bg-label-success' : (stock > 0 ? 'bg-label-warning' : 'bg-label-danger'));
  document.getElementById('qs_qty').max = stock;
  document.getElementById('qsStockBadge').style.display = 'block';
  qsUpdateTotal();
}

function qsUpdateTotal() {
  const select = document.getElementById('qs_product');
  const opt = select.options[select.selectedIndex];
  const qty = parseInt(document.getElementById('qs_qty').value) || 1;
  const discount = parseFloat(document.getElementById('qs_discount').value) || 0;

  if (!opt.value) {
    document.getElementById('qs_total').textContent = 'TZS 0';
    document.getElementById('qsSellBtn').disabled = true;
    return;
  }

  const price = parseFloat(opt.dataset.price);
  const total = Math.max(0, (price * qty) - discount);
  document.getElementById('qs_total').textContent = 'TZS ' + total.toLocaleString();

  const warning = document.getElementById('qsBalanceWarning');
  let canSell = !!qsMember && !!opt.value && qty > 0;
  if (qsMember && total > qsMember.balance) { warning.style.display = 'block'; canSell = false; }
  else { warning.style.display = 'none'; }
  document.getElementById('qsSellBtn').disabled = !canSell;
}

function quickSell() {
  const select = document.getElementById('qs_product');
  const opt = select.options[select.selectedIndex];
  if (!qsMember) { showWarning('Please select a member'); return; }
  if (!opt.value) { showWarning('Please select a product'); return; }

  const qty = parseInt(document.getElementById('qs_qty').value) || 1;
  const stock = parseInt(opt.dataset.stock);
  if (qty > stock) { showWarning('Not enough stock. Available: ' + stock); return; }

  const discount = parseFloat(document.getElementById('qs_discount').value) || 0;
  const sendSms = document.getElementById('qs_send_sms').checked;
  const notes = document.getElementById('qs_notes').value;
  const total = Math.max(0, (parseFloat(opt.dataset.price) * qty) - discount);

  const btn = document.getElementById('qsSellBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

  fetch('{{ url("golf-services/equipment-sales") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({ member_id: qsMember.id, items: [{ equipment_id: opt.value, quantity: qty }], discount, send_sms: sendSms, notes })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showTransactionSuccess(qsMember.name, total, data.new_balance).then(() => location.reload());
    } else {
      showError(data.message || 'Sale failed');
      btn.disabled = false;
      btn.innerHTML = '<i class="ri ri-check-double-line me-2"></i> CONFIRM & SELL';
    }
  })
  .catch(() => {
    showError('Connection error');
    btn.disabled = false;
    btn.innerHTML = '<i class="ri ri-check-double-line me-2"></i> CONFIRM & SELL';
  });
}

// ============================================================
// Product management
// ============================================================
function addProduct() {
  const formData = new FormData(document.getElementById('addProductForm'));
  fetch('{{ url("golf-services/equipment") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: formData })
    .then(r => r.json()).then(data => { if (data.success) showSuccess('Product added!').then(() => location.reload()); else showError(data.message || 'Failed'); });
}

let currentSaleId = null;
function viewSale(id) {
  currentSaleId = id;
  const modal = new bootstrap.Modal(document.getElementById('viewSaleModal'));
  modal.show();
  document.getElementById('saleModalLoading').style.display = 'block';
  document.getElementById('saleModalContent').style.display = 'none';
  document.getElementById('saleModalError').style.display = 'none';

  fetch('{{ url("golf-services/equipment-sales") }}/' + id, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
    .then(r => { if (!r.ok) throw new Error('Failed'); return r.json(); })
    .then(data => {
      if (data.success && data.sale) { populateSaleModal(data.sale); document.getElementById('saleModalLoading').style.display = 'none'; document.getElementById('saleModalContent').style.display = 'block'; }
      else throw new Error(data.message || 'Failed');
    })
    .catch(err => { document.getElementById('saleModalLoading').style.display = 'none'; document.getElementById('saleModalError').style.display = 'block'; document.getElementById('saleModalErrorMessage').textContent = err.message; });
}

function populateSaleModal(sale) {
  document.getElementById('saleModalId').textContent = sale.id;
  document.getElementById('saleInfoId').textContent = '#' + sale.id;
  document.getElementById('saleInfoDate').textContent = new Date(sale.created_at).toLocaleString('en-GB', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
  const sc = { completed:'bg-label-success', refunded:'bg-label-warning', cancelled:'bg-label-danger' };
  document.getElementById('saleInfoStatus').innerHTML = `<span class="badge ${sc[sale.status] || 'bg-label-secondary'}">${sale.status}</span>`;
  const pc = { balance:'bg-label-success', cash:'bg-label-info', card:'bg-label-primary', mobile_money:'bg-label-warning', upi:'bg-label-secondary' };
  document.getElementById('saleInfoPayment').innerHTML = `<span class="badge ${pc[sale.payment_method] || 'bg-label-secondary'}">${sale.payment_method}</span>`;
  document.getElementById('saleCustomerName').textContent = sale.customer_name || '-';
  document.getElementById('saleCustomerCard').textContent = sale.customer_upi || sale.customer_card || '-';
  document.getElementById('saleCustomerPhone').textContent = sale.customer_phone || '-';

  const tbody = document.getElementById('saleItemsTableBody');
  if (sale.items?.length) {
    tbody.innerHTML = sale.items.map((item, i) => `
      <tr><td>${i+1}</td>
      <td><strong>${item.equipment?.name || 'Unknown'}</strong>${item.equipment?.sku ? `<br><small class="text-muted">SKU: ${item.equipment.sku}</small>` : ''}</td>
      <td class="text-end">TZS ${parseFloat(item.unit_price).toLocaleString()}</td>
      <td class="text-center"><span class="badge bg-label-primary">${item.quantity}</span></td>
      <td class="text-end"><strong>TZS ${parseFloat(item.subtotal).toLocaleString()}</strong></td></tr>`).join('');
  } else tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No items</td></tr>';

  document.getElementById('saleSubtotal').textContent = 'TZS ' + parseFloat(sale.subtotal || 0).toLocaleString();
  document.getElementById('saleDiscount').textContent = 'TZS ' + parseFloat(sale.discount || 0).toLocaleString();
  document.getElementById('saleTotal').textContent = 'TZS ' + parseFloat(sale.total_amount || 0).toLocaleString();
  document.getElementById('saleNotes').textContent = sale.notes || 'No notes';
  document.getElementById('saleSmsStatus').innerHTML = sale.sms_sent ? '<span class="badge bg-label-success">Sent</span>' : '<span class="badge bg-label-secondary">Not Sent</span>';
}

function printReceiptFromModal() { if (currentSaleId) printReceipt(currentSaleId); }
function printReceipt(id) { window.open('{{ url("golf-services/equipment-sales") }}/' + id + '/receipt', '_blank'); }

function editProduct(id) {
  fetch(`{{ url('golf-services/equipment') }}/${id}`).then(r => r.json()).then(data => {
    if (data.success) {
      const eq = data.equipment;
      document.getElementById('edit_product_id').value = eq.id;
      document.getElementById('edit_product_name').value = eq.name;
      document.getElementById('edit_product_description').value = eq.description || '';
      document.getElementById('edit_product_sku').value = eq.sku;
      document.getElementById('edit_product_category').value = eq.category;
      document.getElementById('edit_product_price').value = eq.sale_price;
      document.getElementById('edit_product_threshold').value = eq.low_stock_threshold;
      document.getElementById('edit_product_sellable').checked = eq.is_sellable;
      new bootstrap.Modal(document.getElementById('editProductModal')).show();
    }
  });
}

function updateProduct() {
  const id = document.getElementById('edit_product_id').value;
  const formData = new FormData(document.getElementById('editProductForm'));
  formData.append('_method', 'PUT');
  fetch(`{{ url('golf-services/equipment') }}/${id}`, { method:'POST', headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' }, body: formData })
    .then(r => r.json()).then(data => { if (data.success) showSuccess('Product updated!').then(() => location.reload()); else showError(data.message || 'Update failed'); });
}

function openAddStockModal(id, name, currentStock) {
  document.getElementById('stock_product_id').value = id;
  document.getElementById('stockProductName').textContent = name;
  document.getElementById('currentStockDisplay').textContent = currentStock;
  document.getElementById('stock_to_add').value = '';
  new bootstrap.Modal(document.getElementById('addStockModal')).show();
}

function confirmAddStock() {
  const id = document.getElementById('stock_product_id').value;
  const qtyToAdd = document.getElementById('stock_to_add').value;
  if (!qtyToAdd || qtyToAdd == 0) { showWarning('Please enter a quantity'); return; }
  const currentStock = parseInt(document.getElementById('currentStockDisplay').textContent);
  const newTotal = currentStock + parseInt(qtyToAdd);
  const formData = new FormData();
  formData.append('total_quantity', newTotal);
  formData.append('name', 'price_update_bypass');
  formData.append('_method', 'PUT');
  fetch(`{{ url('golf-services/equipment') }}/${id}`, { method:'POST', headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' }, body: formData })
    .then(r => r.json()).then(data => { if (data.success) showSuccess('Stock updated!').then(() => location.reload()); else showError(data.message || 'Update failed'); });
}

function showToast(message) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({ title: message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, background: '#696cff', color: '#fff', icon: 'success', iconColor: '#fff' });
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {});
</script>
@endpush
