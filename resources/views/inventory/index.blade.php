@extends('settings._layout-base')

@section('title', 'Inventory Management')
@section('description', 'Inventory Management - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Management /</span> Inventory
</h4>

<!-- Header Card -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%);">
      <div class="card-body text-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
          <div>
            <h4 class="mb-2 text-white fw-bold text-center text-md-start">
              <i class="icon-base ri ri-archive-line me-2"></i>Inventory Management
            </h4>
            <div class="card-header border-bottom p-0 border-0">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
          <ul class="nav nav-pills card-header-pills mb-2 mb-md-0 overflow-auto flex-nowrap w-100 w-md-auto" role="tablist">
            @if(auth()->user()->role !== 'counter')
            <li class="nav-item">
              <button class="nav-link active px-3" data-bs-toggle="tab" data-bs-target="#tab-inventory" onclick="switchMode('general')">Inventory</button>
            </li>
            @endif
            <li class="nav-item">
              <button class="nav-link {{ auth()->user()->role === 'counter' ? 'active' : '' }} px-3" data-bs-toggle="tab" data-bs-target="#tab-menu-items" onclick="switchMode('menu')">Menu Items</button>
            </li>
          </ul>
          @if(auth()->user()->role !== 'counter')
          <div class="d-flex gap-2 w-100 w-md-auto justify-content-center">
            <button class="btn btn-xs btn-sm-sm btn-label-secondary flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
              <i class="icon-base ri ri-folder-add-line me-1"></i> CATEGORY
            </button>
            <button class="btn btn-xs btn-sm-sm btn-primary flex-grow-1 flex-md-grow-0" data-bs-toggle="modal" data-bs-target="#addItemModal">
              <i class="icon-base ri ri-add-line me-1"></i> ADD ITEM
            </button>
          </div>
          @endif
        </div>
      </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-primary rounded">
              <i class="icon-base ri ri-archive-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Total Items</p>
            <h4 class="mb-0" id="totalItems">0</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-success rounded">
              <i class="icon-base ri ri-checkbox-circle-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">In Stock</p>
            <h4 class="mb-0" id="inStockItems">0</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-warning rounded">
              <i class="icon-base ri ri-error-warning-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Low Stock</p>
            <h4 class="mb-0" id="lowStockItems">0</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3">
            <div class="avatar-initial bg-danger rounded">
              <i class="icon-base ri ri-close-circle-line icon-24px"></i>
            </div>
          </div>
          <div>
            <p class="mb-0 text-body-secondary">Out of Stock</p>
            <h4 class="mb-0" id="outOfStockItems">0</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter & Search -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="searchItem" placeholder="Search..." onkeyup="filterItems()">
              <label for="searchItem">Search Items</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating form-floating-outline">
              <select class="form-select" id="filterCategory" onchange="filterItems()">
                <option value="">All Categories</option>
              </select>
              <label for="filterCategory">Category</label>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-floating form-floating-outline">
              <select class="form-select" id="filterStatus" onchange="filterItems()">
                <option value="">All Status</option>
                <option value="in_stock">In Stock</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
              </select>
              <label for="filterStatus">Status</label>
            </div>
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary w-100 h-100" onclick="filterItems()">
              <i class="icon-base ri ri-filter-line me-1"></i>Filter
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Inventory Table -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="mb-0"><i class="icon-base ri ri-list-check me-2"></i>Inventory Items</h5>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-success flex-grow-1 flex-md-grow-0" onclick="exportInventory()">
            <i class="icon-base ri ri-download-line me-1"></i>Export
          </button>
          <button class="btn btn-sm btn-outline-primary flex-grow-1 flex-md-grow-0" onclick="printInventory()">
            <i class="icon-base ri ri-printer-line me-1"></i>Print
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="tab-content border-0 p-0">
        <div class="tab-pane fade {{ auth()->user()->role !== 'counter' ? 'show active' : '' }}" id="tab-inventory" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Code</th>
                  <th>Item Name</th>
                  <th>Category</th>
                  <th>On Hand</th>
                  <th>Unit Price</th>
                  <th>Total Value</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="inventoryTableBody">
                {{-- Data loaded via AJAX --}}
              </tbody>
            </table>
          </div>
        </div>
        <div class="tab-pane fade {{ auth()->user()->role === 'counter' ? 'show active' : '' }}" id="tab-menu-items" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Item Name</th>
                  <th>Category</th>
                  <th>Price</th>
                  <th>Stock Quantity</th>
                  <th>Threshold</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="menuItemsTableBody">
                {{-- Data loaded via AJAX --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>
      </div>
      <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
          <span class="text-muted">Showing <span id="showingCount">0</span> items</span>
          <nav>
            <ul class="pagination pagination-sm mb-0" id="pagination">
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-add-line me-2"></i>Add Inventory Item</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addItemForm" onsubmit="saveItem(event)">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="item_code" name="item_code" placeholder="Item Code" required>
                <label for="item_code">Item Code *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="item_name" name="item_name" placeholder="Item Name" required>
                <label for="item_name">Item Name *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="item_category" name="category" required>
                  <option value="">Select Category</option>
                </select>
                <label for="item_category">Category *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="item_quantity" name="quantity" placeholder="Quantity" min="0" required>
                <label for="item_quantity">Quantity *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="item_price" name="unit_price" placeholder="Unit Price" min="0" step="100" required>
                <label for="item_price">Unit Price (TZS) *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="item_reorder" name="reorder_level" placeholder="Reorder Level" min="0" value="10">
                <label for="item_reorder">Reorder Level</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="item_unit" name="unit">
                  <option value="pcs">Pieces</option>
                  <option value="box">Box</option>
                  <option value="set">Set</option>
                  <option value="pair">Pair</option>
                  <option value="dozen">Dozen</option>
                </select>
                <label for="item_unit">Unit</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="item_location" name="location" placeholder="Storage Location">
                <label for="item_location">Storage Location</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="item_description" name="description" placeholder="Description" style="height: 80px"></textarea>
                <label for="item_description">Description</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-save-line me-1"></i>Save Item
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-edit-line me-2"></i>Edit Inventory Item</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editItemForm" onsubmit="updateItem(event)">
        <input type="hidden" id="edit_item_id" name="id">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="edit_item_code" name="item_code" placeholder="Item Code" required>
                <label for="edit_item_code">Item Code *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="edit_item_name" name="item_name" placeholder="Item Name" required>
                <label for="edit_item_name">Item Name *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="edit_item_category" name="category" required>
                  <option value="">Select Category</option>
                </select>
                <label for="edit_item_category">Category *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="edit_item_quantity" name="quantity" placeholder="Quantity" min="0" required>
                <label for="edit_item_quantity">Quantity *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="edit_item_price" name="unit_price" placeholder="Unit Price" min="0" step="100" required>
                <label for="edit_item_price">Unit Price (TZS) *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="edit_item_reorder" name="reorder_level" placeholder="Reorder Level" min="0">
                <label for="edit_item_reorder">Reorder Level</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="edit_item_unit" name="unit">
                  <option value="pcs">Pieces</option>
                  <option value="box">Box</option>
                  <option value="set">Set</option>
                  <option value="pair">Pair</option>
                  <option value="dozen">Dozen</option>
                </select>
                <label for="edit_item_unit">Unit</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" id="edit_item_location" name="location" placeholder="Storage Location">
                <label for="edit_item_location">Storage Location</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control" id="edit_item_description" name="description" placeholder="Description" style="height: 80px"></textarea>
                <label for="edit_item_description">Description</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-save-line me-1"></i>Update Item
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-folder-add-line me-2"></i>Add Category</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addCategoryForm" onsubmit="saveCategory(event)">
        <div class="modal-body">
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <input type="text" class="form-control" id="category_name" name="name" placeholder="Category Name" required>
              <label for="category_name">Category Name *</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <textarea class="form-control" id="category_description" name="description" placeholder="Description" style="height: 80px"></textarea>
              <label for="category_description">Description</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-save-line me-1"></i>Save Category
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #b30000 100%); color: white;">
        <h5 class="modal-title text-white"><i class="icon-base ri ri-add-circle-line me-2"></i>Adjust Stock</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="stockAdjustForm" onsubmit="adjustStock(event)">
        <input type="hidden" id="adjust_item_id" name="item_id">
        <input type="hidden" id="adjust_mode" value="general">
        <div class="modal-body">
          <div class="alert alert-info">
            <strong>Item:</strong> <span id="adjust_item_name"></span><br>
            <strong>Current Stock:</strong> <span id="adjust_current_stock"></span>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <select class="form-select" id="adjust_type" name="type" required>
                <option value="add">Add Stock</option>
                <option value="remove">Remove Stock</option>
                <option value="set">Set Stock Level</option>
              </select>
              <label for="adjust_type">Adjustment Type *</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <input type="number" class="form-control" id="adjust_quantity" name="quantity" placeholder="Quantity" min="0" required>
              <label for="adjust_quantity">Quantity *</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-floating form-floating-outline">
              <textarea class="form-control" id="adjust_reason" name="reason" placeholder="Reason" style="height: 80px"></textarea>
              <label for="adjust_reason">Reason</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ri ri-check-line me-1"></i>Apply Adjustment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let inventoryItems = [];
let categories = [];
let menuItems = [];
let currentMode = 'general';

document.addEventListener('DOMContentLoaded', function() {
  refreshData();
});

function refreshData() {
  fetch('{{ route("inventory.index") }}', {
    headers: { 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    inventoryItems = data.items;
    categories = data.categories;
    menuItems = data.menuItems;
    updateCategoryDropdowns();
    renderInventory();
    renderMenuItems();
    updateStats();
  });
}

function switchMode(mode) {
  currentMode = mode;
}

function updateCategoryDropdowns() {
  const dropdowns = ['filterCategory', 'item_category', 'edit_item_category'];
  dropdowns.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      const currentValue = el.value;
      el.innerHTML = id === 'filterCategory' ? '<option value="">All Categories</option>' : '<option value="">Select Category</option>';
      categories.forEach(cat => {
        el.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
      });
      el.value = currentValue;
    }
  });
}

function renderInventory(items = null) {
  // Existing render logic...
}

function renderMenuItems() {
  const tbody = document.getElementById('menuItemsTableBody');
  if (!tbody) return;
  
  if (menuItems.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">No menu items found</td></tr>';
    return;
  }
  
  tbody.innerHTML = menuItems.map(item => {
    const categoryName = item.category ? item.category.name : '-';
    let statusBadge = '';
    
    if (item.stock_quantity === 0) {
      statusBadge = '<span class="badge bg-danger">Out of Stock</span>';
    } else if (item.stock_quantity <= item.low_stock_threshold) {
      statusBadge = '<span class="badge bg-warning">Low Stock</span>';
    } else {
      statusBadge = '<span class="badge bg-success">In Stock</span>';
    }
    
    return `
      <tr>
        <td><strong>${item.name}</strong></td>
        <td>${categoryName}</td>
        <td>TZS ${number_format(item.price)}</td>
        <td>${item.stock_quantity} units</td>
        <td>${item.low_stock_threshold}</td>
        <td>${statusBadge}</td>
        <td>
          <button class="btn btn-sm btn-label-primary" onclick="openStockAdjust(${item.id}, 'menu')">
            <i class="icon-base ri ri-add-circle-line me-1"></i>Adjust Stock
          </button>
        </td>
      </tr>
    `;
  }).join('');
}

function number_format(n) {
  return parseFloat(n).toLocaleString();
}

function updateStats() {
  const total = inventoryItems.length;
  const inStock = inventoryItems.filter(i => i.quantity > i.reorder_level).length;
  const lowStock = inventoryItems.filter(i => i.quantity > 0 && i.quantity <= i.reorder_level).length;
  const outOfStock = inventoryItems.filter(i => i.quantity === 0).length;
  
  document.getElementById('totalItems').textContent = total;
  document.getElementById('inStockItems').textContent = inStock;
  document.getElementById('lowStockItems').textContent = lowStock;
  document.getElementById('outOfStockItems').textContent = outOfStock;
}

function filterItems() {
  const search = document.getElementById('searchItem').value.toLowerCase();
  const categoryId = document.getElementById('filterCategory').value;
  const status = document.getElementById('filterStatus').value;
  
  let filtered = inventoryItems;
  
  if (search) {
    filtered = filtered.filter(i => i.name.toLowerCase().includes(search) || i.item_code.toLowerCase().includes(search));
  }
  if (categoryId) {
    filtered = filtered.filter(i => i.category_id == categoryId);
  }
  if (status) {
    if (status === 'in_stock') filtered = filtered.filter(i => i.quantity > i.reorder_level);
    else if (status === 'low_stock') filtered = filtered.filter(i => i.quantity > 0 && i.quantity <= i.reorder_level);
    else if (status === 'out_of_stock') filtered = filtered.filter(i => i.quantity === 0);
  }
  renderInventory(filtered);
}

function saveCategory(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());

  fetch('{{ route("inventory.categories.store") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => {
    if(res.success) {
      bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
      e.target.reset();
      refreshData();
      alert('Category added successfully');
    } else {
      alert('Error: ' + JSON.stringify(res.errors));
    }
  });
}

function saveItem(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());
  data.category_id = data.category; // Mapping for backend

  fetch('{{ route("inventory.items.store") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => {
    if(res.success) {
      bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide();
      e.target.reset();
      refreshData();
      alert('Item added successfully');
    } else {
      alert('Error: ' + JSON.stringify(res.errors));
    }
  });
}

function editItem(id) {
  const item = inventoryItems.find(i => i.id === id);
  if (!item) return;
  document.getElementById('edit_item_id').value = item.id;
  document.getElementById('edit_item_code').value = item.item_code;
  document.getElementById('edit_item_name').value = item.name;
  document.getElementById('edit_item_category').value = item.category_id;
  document.getElementById('edit_item_quantity').value = item.quantity;
  document.getElementById('edit_item_price').value = item.unit_price;
  document.getElementById('edit_item_reorder').value = item.reorder_level;
  document.getElementById('edit_item_unit').value = item.unit;
  document.getElementById('edit_item_location').value = item.location || '';
  document.getElementById('edit_item_description').value = item.description || '';
  new bootstrap.Modal(document.getElementById('editItemModal')).show();
}

function updateItem(e) {
  e.preventDefault();
  const id = document.getElementById('edit_item_id').value;
  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());
  data.category_id = data.category;

  fetch(`{{ url('inventory/items') }}/${id}`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => {
    if(res.success) {
      bootstrap.Modal.getInstance(document.getElementById('editItemModal')).hide();
      refreshData();
      alert('Item updated successfully');
    }
  });
}

function openStockAdjust(id, mode = 'general') {
  const item = mode === 'menu' ? menuItems.find(i => i.id === id) : inventoryItems.find(i => i.id === id);
  if (!item) return;
  document.getElementById('adjust_item_id').value = item.id;
  document.getElementById('adjust_item_name').textContent = item.name;
  document.getElementById('adjust_current_stock').textContent = (mode === 'menu' ? item.stock_quantity : item.quantity) + ' units';
  document.getElementById('adjust_quantity').value = '';
  document.getElementById('adjust_reason').value = '';
  document.getElementById('adjust_mode').value = mode; // Hidden field to track mode
  new bootstrap.Modal(document.getElementById('stockAdjustModal')).show();
}

function adjustStock(e) {
  e.preventDefault();
  const id = document.getElementById('adjust_item_id').value;
  const mode = document.getElementById('adjust_mode').value;
  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());

  const url = mode === 'menu' ? `{{ url('inventory/menu-items') }}/${id}/adjust` : `{{ url('inventory/items') }}/${id}/adjust`;

  fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(res => {
    if(res.success) {
      bootstrap.Modal.getInstance(document.getElementById('stockAdjustModal')).hide();
      refreshData();
      alert('Stock adjusted successfully');
    }
  });
}

function deleteItem(id) {
  if (!confirm('Are you sure you want to delete this item?')) return;
  fetch(`{{ url('inventory/items') }}/${id}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(res => {
    if(res.success) {
      refreshData();
      alert('Item deleted successfully');
    }
  });
}

function exportInventory() {
  let csv = 'Item Code,Item Name,Category,Quantity,Unit,Unit Price,Total Value,Reorder Level,Location\n';
  inventoryItems.forEach(item => {
    const categoryName = item.category ? item.category.name : '';
    const totalValue = item.quantity * item.unit_price;
    csv += `"${item.item_code}","${item.name}","${categoryName}",${item.quantity},"${item.unit}",${item.unit_price},${totalValue},${item.reorder_level},"${item.location || ''}"\n`;
  });
  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'inventory_' + new Date().toISOString().split('T')[0] + '.csv';
  a.click();
}

function printInventory() { window.print(); }
</script>
@endpush
