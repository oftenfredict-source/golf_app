@extends('settings._layout-base')

@section('title', 'Orders')
@section('description', 'Orders - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Hospitality /</span> Orders
</h4>

<!-- Filters -->
<div class="card mb-6">
  <div class="card-header">
    <h5 class="mb-0">Filters</h5>
  </div>
  <div class="card-body">
    <form class="row g-4">
      <div class="col-md-3">
        <div class="form-floating form-floating-outline">
          <input type="date" class="form-control" id="ord_from" />
          <label for="ord_from">From</label>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-floating form-floating-outline">
          <input type="date" class="form-control" id="ord_to" />
          <label for="ord_to">To</label>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-floating form-floating-outline">
          <select class="form-select" id="ord_status">
            <option value="">All Status</option>
            <option value="new">New</option>
            <option value="preparing">Preparing</option>
            <option value="ready">Ready</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <label for="ord_status">Status</label>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-floating form-floating-outline">
          <select class="form-select" id="ord_channel">
            <option value="">All Channels</option>
            <option value="counter">Counter</option>
            <option value="table">Table</option>
            <option value="delivery">Delivery</option>
          </select>
          <label for="ord_channel">Channel</label>
        </div>
      </div>
      <div class="col-md-2">
        <button type="button" class="btn btn-primary w-100 h-100">Apply</button>
      </div>
    </form>
  </div>
</div>

<!-- Orders list -->
<div class="card mb-6">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Orders</h5>
    <div class="d-flex gap-2">
      <button class="btn btn-label-secondary btn-sm">Refresh</button>
      <button class="btn btn-label-primary btn-sm">Export</button>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Channel</th>
            <th>Items</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="8" class="text-center py-4 text-body-secondary">No orders</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Order timeline -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Order Timeline</h5>
    <small class="text-body">Latest events</small>
  </div>
  <div class="card-body">
    <ul class="timeline">
      <li class="timeline-item">
        <span class="timeline-indicator timeline-indicator-primary"><i class="icon-base ri ri-restaurant-line"></i></span>
        <div class="timeline-event">
          <div class="timeline-header">
            <h6 class="mb-0">No events</h6>
          </div>
          <p class="mb-0 text-body-secondary">Order updates will appear here</p>
        </div>
      </li>
    </ul>
  </div>
</div>
@endsection




