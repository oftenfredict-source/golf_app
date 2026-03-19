@extends('settings._layout-base')

@section('title', 'Counter Home')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Welcome Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #696cff 0%, #3f51b5 100%);">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-md-7 text-white">
                            <h2 class="fw-bold text-white mb-2">Welcome Back, {{ auth()->user()->name }}!</h2>
                            <p class="opacity-75 mb-4 fs-5">Track your duty performance and manage your station from one central place.</p>
                            
                            @if($counter)
                                <div class="d-flex align-items-center flex-wrap gap-2 mt-3">
                                    <div class="bg-white px-3 py-2 rounded-3 shadow-sm border-start border-4 border-warning" style="min-width: 160px;">
                                        <small class="d-block text-muted text-uppercase fw-semibold" style="font-size: 0.65rem;">Assigned Station</small>
                                        <span class="fw-bold text-dark">{{ $counter->name }}</span>
                                    </div>
                                    <div class="bg-white px-3 py-2 rounded-3 shadow-sm border-start border-4 border-{{ $counter->is_alcohol ? 'danger' : 'info' }}" style="min-width: 160px;">
                                        <small class="d-block text-muted text-uppercase fw-semibold" style="font-size: 0.65rem;">Station Duty</small>
                                        <span class="fw-bold text-dark">
                                            @if($counter->is_alcohol)
                                                <i class="ri ri-goblet-line me-1 text-danger"></i> Alcohol Specialist
                                            @else
                                                <i class="ri ri-cup-line me-1 text-info"></i> Soft Drinks
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="alert bg-white bg-opacity-10 border-0 text-white mb-0">
                                    <i class="ri ri-error-warning-line me-2"></i> No station assigned. Please contact your administrator.
                                </div>
                            @endif
                        </div>
                        <div class="col-md-5 d-none d-md-block text-end">
                            <i class="ri ri-store-2-line display-1 text-white opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-5">
        <div class="col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="avatar avatar-md bg-label-primary rounded mb-3">
                        <i class="ri ri-shopping-bag-3-line fs-3"></i>
                    </div>
                    <h6 class="text-muted mb-1">Today's Orders</h6>
                    <h3 class="fw-bold mb-0">{{ $stats['orders_today'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="avatar avatar-md bg-label-success rounded mb-3">
                        <i class="ri ri-money-dollar-circle-line fs-3"></i>
                    </div>
                    <h6 class="text-muted mb-1">Today's Revenue</h6>
                    <h3 class="fw-bold mb-0">TZS {{ number_format($stats['revenue_today']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: #f8f9fa;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">Ready to start?</h5>
                        <p class="text-muted mb-0 small">Open your workspace to process orders and serve members.</p>
                    </div>
                    <a href="{{ route('services.counter.dashboard') }}" class="btn btn-primary px-4 py-2">
                        Open Station Dashboard <i class="ri ri-arrow-right-line ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Activity -->
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="mb-0 fw-bold">Your Recent Orders</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Order #</th>
                                    <th class="border-0">Customer</th>
                                    <th class="border-0">Amount</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 text-end">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_orders'] as $order)
                                    <tr>
                                        <td class="fw-bold text-primary">#{{ $order->order_number }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>TZS {{ number_format($order->total_amount) }}</td>
                                        <td>
                                            <span class="badge rounded-pill bg-label-{{ $order->status === 'complete' ? 'success' : 'primary' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">No orders found for today.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="mb-0 fw-bold">Duty Resources</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        <div class="p-3 rounded-3 bg-light border border-dashed">
                            <h6 class="fw-bold mb-1"><i class="ri ri-book-line me-2 text-primary"></i>Service Standards</h6>
                            <p class="small text-muted mb-0">Guidelines for serving members professionally.</p>
                        </div>
                        <div class="p-3 rounded-3 bg-light border border-dashed">
                            <h6 class="fw-bold mb-1"><i class="ri ri-shield-check-line me-2 text-success"></i>Compliance Info</h6>
                            <p class="small text-muted mb-0">Essential rules for alcoholic beverage handling.</p>
                        </div>
                        <div class="p-3 rounded-3 bg-light border border-dashed">
                            <h6 class="fw-bold mb-1"><i class="ri ri-question-line me-2 text-warning"></i>Internal Support</h6>
                            <p class="small text-muted mb-0">Need help? Contact technical support or your manager.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
</style>
@endpush
