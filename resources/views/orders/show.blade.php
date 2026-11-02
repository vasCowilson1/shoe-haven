@extends('layouts.public')

@section('title', 'Order #' . $order->id . ' — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('orders.index') }}" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h2 class="fw-bold mb-0">Order #{{ $order->id }}</h2>
            <small class="text-white-50">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</small>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card bg-dark border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Order Status</h6>
                    <div class="d-flex justify-content-between position-relative">
                        <div class="text-center flex-fill">
                            <div class="mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle fw-bold small
                                {{ in_array($order->status, ['pending','confirmed','shipped','delivered']) ? 'bg-success text-white' : 'bg-secondary text-white-50' }}"
                                style="width: 32px; height: 32px;">1</div>
                            <small class="d-block text-white-50" style="font-size: .65rem;">Pending</small>
                        </div>
                        <div class="text-center flex-fill">
                            <div class="mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle fw-bold small
                                {{ in_array($order->status, ['confirmed','shipped','delivered']) ? 'bg-success text-white' : 'bg-secondary text-white-50' }}"
                                style="width: 32px; height: 32px;">2</div>
                            <small class="d-block text-white-50" style="font-size: .65rem;">Confirmed</small>
                        </div>
                        <div class="text-center flex-fill">
                            <div class="mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle fw-bold small
                                {{ in_array($order->status, ['shipped','delivered']) ? 'bg-success text-white' : 'bg-secondary text-white-50' }}"
                                style="width: 32px; height: 32px;">3</div>
                            <small class="d-block text-white-50" style="font-size: .65rem;">Shipped</small>
                        </div>
                        <div class="text-center flex-fill">
                            <div class="mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle fw-bold small
                                {{ $order->status === 'delivered' ? 'bg-success text-white' : 'bg-secondary text-white-50' }}"
                                style="width: 32px; height: 32px;">4</div>
                            <small class="d-block text-white-50" style="font-size: .65rem;">Delivered</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-dark border-0">
                <div class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center py-3">
                    <h5 class="fw-bold mb-0">Items</h5>
                    <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }} fs-6">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="card-body p-0">
                    @foreach ($order->items as $item)
                        <div class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom border-secondary' : '' }}">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ Str::startsWith($item->product?->image ?? '', 'http') ? $item->product->image : asset($item->product?->image ?? '') }}"
                                     alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $item->product?->name ?? 'Deleted Product' }}</h6>
                                    <small class="text-white-50">UGX {{ number_format($item->price_at_sale) }} × {{ $item->quantity }}</small>
                                </div>
                            </div>
                            <div class="fw-bold">UGX {{ number_format($item->price_at_sale * $item->quantity) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-dark border-0 mb-3">
                <div class="card-header bg-transparent border-secondary py-3">
                    <h5 class="fw-bold mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white-50">Subtotal</span>
                        <span>UGX {{ number_format($order->total_amount) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white-50">Delivery</span>
                        <span class="text-success">Free</span>
                    </div>
                    <hr class="text-secondary">
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span style="color: var(--sh-orange);">UGX {{ number_format($order->total_amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="card bg-dark border-0 mb-3">
                <div class="card-header bg-transparent border-secondary py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2"></i>Delivery Location</h5>
                </div>
                <div class="card-body small">
                    <div class="fw-semibold">{{ $order->shipping_area }}, {{ $order->shipping_district }}</div>
                    <div class="text-white-50">{{ $order->shipping_region }}</div>
                    <div class="mt-2"><span class="text-white-50">Landmark:</span> {{ $order->shipping_landmark }}</div>
                    @if ($order->shipping_address)
                        <div class="mt-1"><span class="text-white-50">Directions:</span> {{ $order->shipping_address }}</div>
                    @endif
                    <div class="mt-1"><span class="text-white-50">Phone:</span> {{ $order->shipping_phone }}</div>
                </div>
            </div>

            <div class="card bg-dark border-0">
                <div class="card-header bg-transparent border-secondary py-3">
                    <h5 class="fw-bold mb-0">Payment Info</h5>
                </div>
                <div class="card-body small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white-50">Method</span>
                        <span>{{ $order->payment_method }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white-50">Phone</span>
                        <span>{{ $order->payment_phone }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white-50">Transaction</span>
                        <span class="font-monospace">{{ $order->transaction_id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white-50">Payment Status</span>
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
