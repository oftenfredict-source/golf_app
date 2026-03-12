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
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="mb-2 text-white fw-bold">
              <i class="icon-base ri ri-archive-line me-2"></i>Inventory Management
            </h4>
            <p class="mb-0 opacity-75">Manage golf equipment, supplies, and stock levels</p>
          </div>
          <div class="d-flex gap-2 mt-3 mt-md-0">
            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addItemModal">
              <i class="icon-base ri ri-add-line me-1"></i>Add Item
            </button>
            <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
              <i class="icon-base ri ri-folder-add-line me-1"></i>Add Category
            </button>
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
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="icon-base ri ri-list-check me-2"></i>Inventory Items</h5>
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-success" onclick="exportInventory()">
            <i class="icon-base ri ri-download-line me-1"></i>Export
          </button>
          <button class="btn btn-sm btn-outline-primary" onclick="printInventory()">
            <i class="icon-base ri ri-printer-line me-1"></i>Print
          </button>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="inventoryTable">
            <thead class="table-light">
              <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Value</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="inventoryTableBody">
              <!-- Items will be loaded here -->
            </tbody>
          </table>
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

document.addEventListener('DOMContentLoaded', function() {
  loadCategories();
  loadInventory();
});

function loadCategories() {
  // Load from localStorage or use defaults
  categories = JSON.parse(localStorage.getItem('inventoryCategories') || '[]');
  
  if (categories.length === 0) {
    categories = [
      { id: 1, name: 'Golf Balls', description: 'All types of golf balls' },
      { id: 2, name: 'Golf Clubs', description: 'Drivers, irons, putters, etc.' },
      { id: 3, name: 'Golf Bags', description: 'Carry bags, cart bags, stand bags' },
      { id: 4, name: 'Accessories', description: 'Gloves, tees, markers, etc.' },
      { id: 5, name: 'Apparel', description: 'Golf shirts, pants, caps' },
      { id: 6, name: 'Equipment', description: 'Carts, range equipment' }
    ];
    localStorage.setItem('inventoryCategories', JSON.stringify(categories));
  }
  
  updateCategoryDropdowns();
}

function updateCategoryDropdowns() {
  const dropdowns = ['filterCategory', 'item_category', 'edit_item_category'];
  dropdowns.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      const currentValue = el.value;
      el.innerHTML = '<option value="">Select Category</option>';
      if (id === 'filterCategory') {
        el.innerHTML = '<option value="">All Categories</option>';
      }
      categories.forEach(cat => {
        el.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
      });
      el.value = currentValue;
    }
  });
}

function loadInventory() {
  // Load from localStorage or use sample data
  inventoryItems = JSON.parse(localStorage.getItem('inventoryItems') || '[]');
  
  if (inventoryItems.length === 0) {
    inventoryItems = [
      { id: 1, code: 'GB001', name: 'Titleist Pro V1', category: 1, quantity: 150, unit_price: 15000, reorder_level: 50, unit: 'dozen', location: 'Shelf A1', description: 'Premium golf balls' },
      { id: 2, code: 'GB002', name: 'Callaway Chrome Soft', category: 1, quantity: 80, unit_price: 12000, reorder_level: 30, unit: 'dozen', location: 'Shelf A2', description: 'Soft feel golf balls' },
      { id: 3, code: 'GC001', name: 'TaylorMade Driver', category: 2, quantity: 5, unit_price: 850000, reorder_level: 2, unit: 'pcs', location: 'Display B1', description: 'Latest model driver' },
      { id: 4, code: 'GA001', name: 'Golf Gloves', category: 4, quantity: 25, unit_price: 25000, reorder_level: 10, unit: 'pair', location: 'Shelf C1', description: 'Leather golf gloves' },
      { id: 5, code: 'GT001', name: 'Wooden Tees', category: 4, quantity: 500, unit_price: 500, reorder_level: 100, unit: 'pcs', location: 'Shelf C2', description: 'Standard wooden tees' },
      { id: 6, code: 'GB003', name: 'Range Balls', category: 1, quantity: 8, unit_price: 5000, reorder_level: 50, unit: 'box', location: 'Storage D1', description: 'Practice range balls' }
    ];
    localStorage.setItem('inventoryItems', JSON.stringify(inventoryItems));
  }
  
  renderInventory();
  updateStats();
}

function renderInventory(items = null) {
  const tbody = document.getElementById('inventoryTableBody');
  const displayItems = items || inventoryItems;
  
  if (displayItems.length === 0) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No items found</td></tr>';
    document.getElementById('showingCount').textContent = '0';
    return;
  }
  
  tbody.innerHTML = displayItems.map(item => {
    const category = categories.find(c => c.id == item.category);
    const totalValue = item.quantity * item.unit_price;
    let statusBadge = '';
    
    if (item.quantity === 0) {
      statusBadge = '<span class="badge bg-danger">Out of Stock</span>';
    } else if (item.quantity <= item.reorder_level) {
      statusBadge = '<span class="badge bg-warning">Low Stock</span>';
    } else {
      statusBadge = '<span class="badge bg-success">In Stock</span>';
    }
    
    return `
      <tr>
        <td><code>${item.code}</code></td>
        <td><strong>${item.name}</strong></td>
        <td>${category ? category.name : '-'}</td>
        <td>${item.quantity} ${item.unit}</td>
        <td>TZS ${item.unit_price.toLocaleString()}</td>
        <td>TZS ${totalValue.toLocaleString()}</td>
        <td>${statusBadge}</td>
        <td>
          <div class="dropdown">
            <button class="btn btn-sm btn-label-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
              Actions
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="javascript:void(0)" onclick="editItem(${item.id})"><i class="icon-base ri ri-edit-line me-2"></i>Edit</a></li>
              <li><a class="dropdown-item" href="javascript:void(0)" onclick="openStockAdjust(${item.id})"><i class="icon-base ri ri-add-circle-line me-2"></i>Adjust Stock</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteItem(${item.id})"><i class="icon-base ri ri-delete-bin-line me-2"></i>Delete</a></li>
            </ul>
          </div>
        </td>
      </tr>
    `;
  }).join('');
  
  document.getElementById('showingCount').textContent = displayItems.length;
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
  const category = document.getElementById('filterCategory').value;
  const status = document.getElementById('filterStatus').value;
  
  let filtered = inventoryItems;
  
  if (search) {
    filtered = filtered.filter(i => 
      i.name.toLowerCase().includes(search) || 
      i.code.toLowerCase().includes(search)
    );
  }
  
  if (category) {
    filtered = filtered.filter(i => i.category == category);
  }
  
  if (status) {
    if (status === 'in_stock') {
      filtered = filtered.filter(i => i.quantity > i.reorder_level);
    } else if (status === 'low_stock') {
      filtered = filtered.filter(i => i.quantity > 0 && i.quantity <= i.reorder_level);
    } else if (status === 'out_of_stock') {
      filtered = filtered.filter(i => i.quantity === 0);
    }
  }
  
  renderInventory(filtered);
}

function saveItem(e) {
  e.preventDefault();
  
  const newItem = {
    id: Date.now(),
    code: document.getElementById('item_code').value,
    name: document.getElementById('item_name').value,
    category: parseInt(document.getElementById('item_category').value),
    quantity: parseInt(document.getElementById('item_quantity').value),
    unit_price: parseInt(document.getElementById('item_price').value),
    reorder_level: parseInt(document.getElementById('item_reorder').value) || 10,
    unit: document.getElementById('item_unit').value,
    location: document.getElementById('item_location').value,
    description: document.getElementById('item_description').value
  };
  
  inventoryItems.push(newItem);
  localStorage.setItem('inventoryItems', JSON.stringify(inventoryItems));
  
  bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide();
  document.getElementById('addItemForm').reset();
  
  renderInventory();
  updateStats();
  alert('Item added successfully!');
}

function editItem(id) {
  const item = inventoryItems.find(i => i.id === id);
  if (!item) return;
  
  document.getElementById('edit_item_id').value = item.id;
  document.getElementById('edit_item_code').value = item.code;
  document.getElementById('edit_item_name').value = item.name;
  document.getElementById('edit_item_category').value = item.category;
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
  
  const id = parseInt(document.getElementById('edit_item_id').value);
  const index = inventoryItems.findIndex(i => i.id === id);
  
  if (index === -1) return;
  
  inventoryItems[index] = {
    ...inventoryItems[index],
    code: document.getElementById('edit_item_code').value,
    name: document.getElementById('edit_item_name').value,
    category: parseInt(document.getElementById('edit_item_category').value),
    quantity: parseInt(document.getElementById('edit_item_quantity').value),
    unit_price: parseInt(document.getElementById('edit_item_price').value),
    reorder_level: parseInt(document.getElementById('edit_item_reorder').value) || 10,
    unit: document.getElementById('edit_item_unit').value,
    location: document.getElementById('edit_item_location').value,
    description: document.getElementById('edit_item_description').value
  };
  
  localStorage.setItem('inventoryItems', JSON.stringify(inventoryItems));
  
  bootstrap.Modal.getInstance(document.getElementById('editItemModal')).hide();
  
  renderInventory();
  updateStats();
  alert('Item updated successfully!');
}

function deleteItem(id) {
  if (!confirm('Are you sure you want to delete this item?')) return;
  
  inventoryItems = inventoryItems.filter(i => i.id !== id);
  localStorage.setItem('inventoryItems', JSON.stringify(inventoryItems));
  
  renderInventory();
  updateStats();
  alert('Item deleted successfully!');
}

function openStockAdjust(id) {
  const item = inventoryItems.find(i => i.id === id);
  if (!item) return;
  
  document.getElementById('adjust_item_id').value = item.id;
  document.getElementById('adjust_item_name').textContent = item.name;
  document.getElementById('adjust_current_stock').textContent = item.quantity + ' ' + item.unit;
  document.getElementById('adjust_quantity').value = '';
  document.getElementById('adjust_reason').value = '';
  
  new bootstrap.Modal(document.getElementById('stockAdjustModal')).show();
}

function adjustStock(e) {
  e.preventDefault();
  
  const id = parseInt(document.getElementById('adjust_item_id').value);
  const type = document.getElementById('adjust_type').value;
  const quantity = parseInt(document.getElementById('adjust_quantity').value);
  
  const index = inventoryItems.findIndex(i => i.id === id);
  if (index === -1) return;
  
  if (type === 'add') {
    inventoryItems[index].quantity += quantity;
  } else if (type === 'remove') {
    inventoryItems[index].quantity = Math.max(0, inventoryItems[index].quantity - quantity);
  } else if (type === 'set') {
    inventoryItems[index].quantity = quantity;
  }
  
  localStorage.setItem('inventoryItems', JSON.stringify(inventoryItems));
  
  bootstrap.Modal.getInstance(document.getElementById('stockAdjustModal')).hide();
  
  renderInventory();
  updateStats();
  alert('Stock adjusted successfully!');
}

function saveCategory(e) {
  e.preventDefault();
  
  const newCategory = {
    id: Date.now(),
    name: document.getElementById('category_name').value,
    description: document.getElementById('category_description').value
  };
  
  categories.push(newCategory);
  localStorage.setItem('inventoryCategories', JSON.stringify(categories));
  
  bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
  document.getElementById('addCategoryForm').reset();
  
  updateCategoryDropdowns();
  alert('Category added successfully!');
}

function exportInventory() {
  let csv = 'Item Code,Item Name,Category,Quantity,Unit,Unit Price,Total Value,Reorder Level,Location\n';
  
  inventoryItems.forEach(item => {
    const category = categories.find(c => c.id == item.category);
    const totalValue = item.quantity * item.unit_price;
    csv += `"${item.code}","${item.name}","${category ? category.name : ''}",${item.quantity},"${item.unit}",${item.unit_price},${totalValue},${item.reorder_level},"${item.location || ''}"\n`;
  });
  
  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'inventory_' + new Date().toISOString().split('T')[0] + '.csv';
  a.click();
}

function printInventory() {
  window.print();
}
</script>
@endpush
