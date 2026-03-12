@extends('settings._layout-base')

@section('title', 'Counter Management')
@section('description', 'Counter Management - Golf Club Management System')

@section('content')
<h4 class="fw-bold mb-4">
  <span class="text-muted fw-light">Hospitality /</span> Counter Management
</h4>

<!-- Summary -->
<div class="row mb-6">
  <div class="col-md-3 col-6">
    <div class="card"><div class="card-body"><p class="mb-1 text-body-secondary">Active Counters</p><h5 class="mb-0">0</h5></div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card"><div class="card-body"><p class="mb-1 text-body-secondary">On Duty Staff</p><h5 class="mb-0">0</h5></div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card"><div class="card-body"><p class="mb-1 text-body-secondary">Pending Orders</p><h5 class="mb-0">0</h5></div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card"><div class="card-body"><p class="mb-1 text-body-secondary">Today Sales</p><h5 class="mb-0">TZS 0</h5></div></div>
  </div>
</div>

<!-- Counters table -->
<div class="card mb-6">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Counters</h5>
    <button class="btn btn-sm btn-primary">Add Counter</button>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Counter</th>
            <th>Location</th>
            <th>Assigned Staff</th>
            <th>Status</th>
            <th>Shift</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="6" class="text-center py-4 text-body-secondary">No counters defined</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Staff assignment -->
<div class="card mb-6">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Assign Staff</h5>
    <small class="text-body">Counter staffing</small>
  </div>
  <div class="card-body">
    <form class="row g-4">
      <div class="col-md-4">
        <div class="form-floating form-floating-outline">
          <select class="form-select" id="assign_counter">
            <option value="">Select Counter</option>
          </select>
          <label for="assign_counter">Counter</label>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-floating form-floating-outline">
          <select class="form-select" id="assign_staff">
            <option value="">Select Staff</option>
          </select>
          <label for="assign_staff">Staff</label>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-floating form-floating-outline">
          <input type="text" class="form-control" id="assign_shift" placeholder="08:00 - 16:00" />
          <label for="assign_shift">Shift</label>
        </div>
      </div>
      <div class="col-12">
        <button type="button" class="btn btn-primary">Assign</button>
      </div>
    </form>
  </div>
</div>

<!-- Recent transactions -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Recent Counter Transactions</h5>
    <button class="btn btn-label-secondary btn-sm">Refresh</button>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Txn ID</th>
            <th>Counter</th>
            <th>Cashier</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="6" class="text-center py-4 text-body-secondary">No transactions yet</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection




