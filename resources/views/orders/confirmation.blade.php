@extends('layouts.public')

@section('title', 'Order Confirmed! — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="container py-5">
    <div class="text-center py-5">
        <div class="mb-4 d-inline-flex align-items-center justify-content-center"
             style="width: 100px; height: 100px; border-radius: 50%; background: rgba(46, 125, 50, 0.15);">
            <i class="bi bi-check-lg" style="font-size: 3rem; color: #4caf50;"></i>
        </div>
        <h1 class="fw-bold mb-2">Order Confirmed!</h1>
        <p class="text-muted mb-1 fs-5">Thank you for your purchase.</p>
        <p class="text-white-50 mb-4">Your order <span class="fw-bold text-white">#{{ $order->id }}</span> has been placed successfully.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-sh-orange btn-lg">
                <i class="bi bi-eye"></i> View Order
            </a>
            <a href="{{ route('shop.index') }}" class="btn btn-outline-light btn-lg">
                Continue Shopping
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-geo-alt" style="color: var(--sh-orange);"></i>
                <h6 class="fw-bold mb-0">Delivery Location</h6>
            </div>
            <div class="fw-semibold">{{ $order->shipping_area }}, {{ $order->shipping_district }}, {{ $order->shipping_region }}</div>
            <div class="text-muted small">{{ $order->shipping_landmark }}</div>
            @if ($order->shipping_address)
                <div class="text-muted small mt-1">{{ $order->shipping_address }}</div>
            @endif
        </div>
    </div>

    <div class="row g-4 mt-3">
        <div class="col-md-4">
            <div class="text-center p-4 rounded-4" style="background: rgba(255,255,255,0.03);">
                <i class="bi bi-clock-history fs-2" style="color: var(--sh-orange);"></i>
                <h6 class="fw-bold mt-3">Order Processing</h6>
                <small class="text-white-50">Your order is being prepared. You'll receive a confirmation call shortly.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-4 rounded-4" style="background: rgba(255,255,255,0.03);">
                <i class="bi bi-truck fs-2" style="color: var(--sh-orange);"></i>
                <h6 class="fw-bold mt-3">Delivery</h6>
                <small class="text-white-50">Expect delivery within 2–5 business days across Uganda.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-4 rounded-4" style="background: rgba(255,255,255,0.03);">
                <i class="bi bi-headset fs-2" style="color: var(--sh-orange);"></i>
                <h6 class="fw-bold mt-3">Need Help?</h6>
                <small class="text-white-50">Contact our support team at support@shoehaven.com or call +256 700 000 000.</small>
            </div>
        </div>
    </div>
</div>
@endsection
