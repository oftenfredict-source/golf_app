@extends('settings._layout-base')

@section('title', 'Food & Beverage')
@section('description', 'Food & Beverage - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Hospitality /</span> Food & Beverage
</h4>

<!-- Summary cards -->
<div class="row mb-6">
  <div class="col-md-3 col-6">
    <div class="card">
      <div class="card-body">
        <p class="mb-1 text-body-secondary">Open Orders</p>
        <h5 class="mb-0">0</h5>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card">
      <div class="card-body">
        <p class="mb-1 text-body-secondary">In-Prep</p>
        <h5 class="mb-0">0</h5>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card">
      <div class="card-body">
        <p class="mb-1 text-body-secondary">Ready for Pickup</p>
        <h5 class="mb-0">0</h5>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card">
      <div class="card-body">
        <p class="mb-1 text-body-secondary">Today Revenue</p>
        <h5 class="mb-0">TZS 0</h5>
      </div>
    </div>
  </div>
</div>

<!-- Quick order + current orders -->
<div class="row gy-6 mb-6">
  <div class="col-xl-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Quick Order</h5>
        <small class="text-body">POS</small>
      </div>
      <div class="card-body">
        <form>
          <div class="form-floating form-floating-outline mb-4">
            <input type="text" class="form-control" id="qb_customer" placeholder="Search customer" />
            <label for="qb_customer">Customer / Member / UPI</label>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <select class="form-select" id="qb_item">
              <option value="">Select Menu Item</option>
              <option>Club Sandwich</option>
              <option>Cold Drink</option>
              <option>Snacks</option>
            </select>
            <label for="qb_item">Menu Item</label>
          </div>
          <div class="row mb-4">
            <div class="col-6">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control" id="qb_qty" value="1" min="1" />
                <label for="qb_qty">Qty</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-floating form-floating-outline">
                <input type="number" step="0.01" class="form-control" id="qb_price" placeholder="0" />
                <label for="qb_price">Price (TZS)</label>
              </div>
            </div>
          </div>
          <div class="form-floating form-floating-outline mb-4">
            <select class="form-select" id="qb_payment">
              <option value="upi">UPI</option>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
            </select>
            <label for="qb_payment">Payment Method</label>
          </div>
          <button type="button" class="btn btn-primary w-100">Add Order</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-xl-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Active Orders</h5>
        <div>
          <button class="btn btn-label-secondary btn-sm">Refresh</button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Time</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="7" class="text-center py-4 text-body-secondary">No active orders</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Menu items and low stock -->
<div class="row gy-6">
  <div class="col-xl-7">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Menu Items</h5>
        <button class="btn btn-sm btn-primary">Add Item</button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Item</th>
                <th>Category</th>
                <th>Price</th>
                <th>Prep Time</th>
                <th>Availability</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Club Sandwich</td>
                <td>Snacks</td>
                <td>TZS 8,500</td>
                <td>8 min</td>
                <td><span class="badge bg-label-success">Available</span></td>
                <td><button class="btn btn-sm btn-label-primary">Edit</button></td>
              </tr>
              <tr>
                <td>Fresh Juice</td>
                <td>Beverage</td>
                <td>TZS 4,000</td>
                <td>3 min</td>
                <td><span class="badge bg-label-warning">Low Stock</span></td>
                <td><button class="btn btn-sm btn-label-primary">Edit</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-5">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Low Stock Alerts</h5>
        <small class="text-body">Inventory</small>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Ingredient</th>
                <th>Remaining</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Chicken Breast</td>
                <td><span class="badge bg-label-danger">5 kg</span></td>
                <td><button class="btn btn-sm btn-label-primary">Restock</button></td>
              </tr>
              <tr>
                <td>Fresh Juice Base</td>
                <td><span class="badge bg-label-warning">3 L</span></td>
                <td><button class="btn btn-sm btn-label-primary">Restock</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection




