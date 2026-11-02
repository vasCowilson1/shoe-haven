@extends('admin.layouts.admin')

@section('title', "Order #{$order->id} — Admin")
@section('page-title', "Order #{$order->id}")

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark btn-sm rounded-circle" style="width: 36px; height: 36px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Order #{{ $order->id }}</h4>
            <small class="text-muted">Placed {{ $order->created_at->format('M d, Y \a\t h:i A') }}</small>
        </div>
    </div>
    <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }} fs-6 px-3 py-2">
        {{ ucfirst($order->status) }}
    </span>
</div>

{{-- Status Timeline --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between position-relative">
            @php
                $steps = ['pending', 'confirmed', 'shipped', 'delivered'];
                $current = array_search($order->status, $steps);
                if ($order->status === 'cancelled') $current = -1;
            @endphp
            @foreach ($steps as $i => $step)
                <div class="text-center flex-fill position-relative">
                    @if (!$loop->first)
                        <div class="position-absolute top-50 start-0 translate-middle-y"
                             style="height: 3px; {{ $i <= $current ? 'background: var(--sh-orange);' : 'background: #e0e0e0;' }} width: 100%; z-index: 0;"></div>
                    @endif
                    <div class="position-relative z-1 d-inline-flex align-items-center justify-content-center rounded-circle fw-bold"
                         style="width: 40px; height: 40px; {{ $i <= $current ? 'background: var(--sh-orange); color: #fff;' : 'background: #e0e0e0; color: #999;' }}">
                        <i class="bi {{ $i < $current ? 'bi-check-lg' : ($i === $current ? 'bi-circle-fill' : 'bi-circle') }}"></i>
                    </div>
                    <small class="d-block mt-1 text-{{ $i <= $current ? 'dark fw-bold' : 'muted' }}" style="font-size: .7rem; text-transform: uppercase;">{{ ucfirst($step) }}</small>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Items --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Items ({{ $order->items->count() }})</h5>
            </div>
            <div class="card-body p-0">
                @foreach ($order->items as $item)
                    <div class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 56px; height: 56px; border-radius: 8px; overflow: hidden; background: #f5f5f5; flex-shrink: 0;">
                                <img src="{{ Str::startsWith($item->product?->image ?? '', 'http') ? $item->product->image : asset($item->product?->image ?? '') }}"
                                     alt="" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ $item->product?->name ?? 'Deleted Product' }}</h6>
                                <small class="text-muted">UGX {{ number_format($item->price_at_sale) }} × {{ $item->quantity }}</small>
                            </div>
                        </div>
                        <div class="fw-bold fs-5" style="color: var(--sh-orange);">UGX {{ number_format($item->price_at_sale * $item->quantity) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-person me-2"></i>Customer Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Name</small>
                        <span class="fw-medium">{{ $order->shipping_name ?? $order->user?->name ?? 'Guest' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Email</small>
                        <span>{{ $order->user?->email ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Phone</small>
                        <span>{{ $order->payment_phone }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Delivery Location</small>
                        <span class="d-block">{{ $order->shipping_area }}, {{ $order->shipping_district }}, {{ $order->shipping_region }}</span>
                        <small class="text-muted d-block mt-1">{{ $order->shipping_landmark }}</small>
                        @if ($order->shipping_address)
                            <small class="text-muted d-block mt-1">{{ $order->shipping_address }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Update Status --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-arrow-repeat me-2"></i>Update Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <select name="status" class="form-select form-select-lg">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sh-orange w-100">Update Status</button>
                </form>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-bold">UGX {{ number_format($order->total_amount) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Delivery</span>
                    <span class="text-success fw-medium">Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between py-2">
                    <span class="fw-bold fs-5">Total</span>
                    <span class="fw-bold fs-5" style="color: var(--sh-orange);">UGX {{ number_format($order->total_amount) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Payment</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Method</span>
                    <span class="fw-medium">{{ $order->payment_method }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Phone</span>
                    <span>{{ $order->payment_phone }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Transaction</span>
                    <span class="font-monospace small">{{ $order->transaction_id ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Status</span>
                    <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }} fs-6">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection