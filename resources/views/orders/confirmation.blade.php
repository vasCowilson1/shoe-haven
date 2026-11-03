@extends('layouts.public')

@section('title', 'Order Status — ' . config('app.name', 'Shoe Haven'))

@section('content')
@php
    $paymentState = $order->payment_status;
    $isPaid = $paymentState === 'paid';
    $isFailed = $paymentState === 'failed';
    $isRefunded = in_array($paymentState, ['refunded', 'partially_refunded'], true);
    $isTerminal = $isPaid || $isFailed || $isRefunded;
@endphp
<div class="container py-5" id="order-confirmation" data-payment-status-url="{{ route('orders.pay.status', $order) }}" data-payment-state="{{ $paymentState }}">
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    <div class="text-center py-5">
        <div class="mb-4 d-inline-flex align-items-center justify-content-center"
             id="payment-state-icon"
             style="width: 100px; height: 100px; border-radius: 50%; background: {{ $isPaid ? 'rgba(46, 125, 50, 0.15)' : ($isFailed ? 'rgba(220, 53, 69, 0.15)' : ($isRefunded ? 'rgba(13, 110, 253, 0.15)' : 'rgba(255, 152, 0, 0.15)')) }};">
            <i class="bi {{ $isPaid ? 'bi-check-lg' : ($isFailed ? 'bi-x-lg' : ($isRefunded ? 'bi-arrow-counterclockwise' : 'bi-phone')) }}"
               style="font-size: 3rem; color: {{ $isPaid ? '#4caf50' : ($isFailed ? '#dc3545' : ($isRefunded ? '#0d6efd' : '#ff9800')) }};"></i>
        </div>
        <h1 class="fw-bold mb-2" id="payment-state-title">
            {{ $isPaid
                ? 'Payment received — order confirmed!'
                : ($isFailed
                    ? 'Payment was not completed'
                    : ($isRefunded
                        ? ($paymentState === 'refunded' ? 'Payment refunded' : 'Payment partially refunded')
                        : 'Payment pending')) }}
        </h1>
        <p class="text-muted mb-1 fs-5" id="payment-state-message">
            {{ $isPaid
                ? 'Thank you. Your payment has been received.'
                : ($isFailed
                    ? 'Your order is saved, but payment did not complete. Retry payment to continue.'
                    : ($isRefunded
                        ? 'A refund has been recorded for this order. Contact support if you need help.'
                        : 'Approve the mobile-money prompt sent to ' . $order->payment_phone . ' to complete your order.')) }}
        </p>
        <p class="text-white-50 mb-4">Order <span class="fw-bold text-white">#{{ $order->id }}</span></p>
        <div id="payment-status-feedback" class="alert alert-warning mx-auto mb-4" role="status" aria-live="polite" style="max-width: 640px; {{ $isTerminal ? 'display: none;' : '' }}">
            Waiting for your payment confirmation. Keep this page open or check the status below.
        </div>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('orders.pay', $order) }}" id="retry-payment-button" class="btn btn-danger btn-lg" style="{{ $isFailed ? '' : 'display: none;' }}">
                <i class="bi bi-arrow-clockwise"></i> Retry Payment
            </a>
            <button type="button" id="check-payment-button" class="btn btn-outline-light btn-lg" style="{{ $isTerminal ? 'display: none;' : '' }}">
                <i class="bi bi-arrow-repeat"></i> Check Payment Status
            </button>
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
                <h6 class="fw-bold mt-3" id="order-progress-title">{{ $isPaid ? 'Order Processing' : ($isRefunded ? 'Payment Update' : 'Payment Required') }}</h6>
                <small class="text-white-50" id="order-progress-message">
                    {{ $isPaid
                        ? "Your order is being prepared. You'll receive a confirmation call shortly."
                        : ($isFailed
                            ? 'Retry payment to continue with your order.'
                            : ($isRefunded
                                ? 'Contact support if you have questions about this refund.'
                                : 'Your order will be prepared once payment is confirmed.')) }}
                </small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-4 rounded-4" style="background: rgba(255,255,255,0.03);">
                <i class="bi bi-truck fs-2" style="color: var(--sh-orange);"></i>
                <h6 class="fw-bold mt-3">Delivery</h6>
                <small class="text-white-50" id="delivery-state-message">
                    {{ $isPaid ? 'Expect delivery within 2–5 business days across Uganda.' : 'Delivery timing will be confirmed after payment is complete.' }}
                </small>
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

@push('scripts')
<script>
(() => {
    const page = document.getElementById('order-confirmation');
    if (!page) return;

    const title = document.getElementById('payment-state-title');
    const message = document.getElementById('payment-state-message');
    const feedback = document.getElementById('payment-status-feedback');
    const retryButton = document.getElementById('retry-payment-button');
    const checkButton = document.getElementById('check-payment-button');
    const progressTitle = document.getElementById('order-progress-title');
    const progressMessage = document.getElementById('order-progress-message');
    const icon = document.querySelector('#payment-state-icon i');
    const iconWrap = document.getElementById('payment-state-icon');
    let paymentState = page.dataset.paymentState;
    let polling = true;

    function renderState(state) {
        paymentState = state;
        page.dataset.paymentState = state;
        feedback.className = 'alert mx-auto mb-4';

        if (state === 'paid') {
            title.textContent = 'Payment received — order confirmed!';
            message.textContent = 'Thank you. Your payment has been received.';
            feedback.style.display = 'none';
            retryButton.style.display = 'none';
            checkButton.style.display = 'none';
            progressTitle.textContent = 'Order Processing';
            progressMessage.textContent = "Your order is being prepared. You'll receive a confirmation call shortly.";
            document.getElementById('delivery-state-message').textContent = 'Expect delivery within 2–5 business days across Uganda.';
            icon.className = 'bi bi-check-lg';
            icon.style.color = '#4caf50';
            iconWrap.style.background = 'rgba(46, 125, 50, 0.15)';
            polling = false;
        } else if (state === 'failed') {
            title.textContent = 'Payment was not completed';
            message.textContent = 'Your order is saved, but payment did not complete. Retry payment to continue.';
            feedback.style.display = 'none';
            retryButton.style.display = '';
            checkButton.style.display = 'none';
            progressTitle.textContent = 'Payment Required';
            progressMessage.textContent = 'Retry payment to continue with your order.';
            document.getElementById('delivery-state-message').textContent = 'Delivery timing will be confirmed after payment is complete.';
            icon.className = 'bi bi-x-lg';
            icon.style.color = '#dc3545';
            iconWrap.style.background = 'rgba(220, 53, 69, 0.15)';
            polling = false;
        } else if (state === 'refunded' || state === 'partially_refunded') {
            title.textContent = state === 'refunded' ? 'Payment refunded' : 'Payment partially refunded';
            message.textContent = 'A refund has been recorded for this order. Contact support if you need help.';
            feedback.style.display = 'none';
            retryButton.style.display = 'none';
            checkButton.style.display = 'none';
            progressTitle.textContent = 'Payment Update';
            progressMessage.textContent = 'Contact support if you have questions about this refund.';
            document.getElementById('delivery-state-message').textContent = 'Contact support for delivery information about this refunded order.';
            icon.className = 'bi bi-arrow-counterclockwise';
            icon.style.color = '#0d6efd';
            iconWrap.style.background = 'rgba(13, 110, 253, 0.15)';
            polling = false;
        } else {
            title.textContent = 'Payment pending';
            message.textContent = 'Approve the mobile-money prompt sent to complete your order.';
            feedback.className = 'alert alert-warning mx-auto mb-4';
            feedback.textContent = 'Waiting for your payment confirmation. Keep this page open or check the status below.';
            feedback.style.display = '';
            retryButton.style.display = 'none';
            checkButton.style.display = '';
            progressTitle.textContent = 'Payment Required';
            progressMessage.textContent = 'Your order will be prepared once payment is confirmed.';
            document.getElementById('delivery-state-message').textContent = 'Delivery timing will be confirmed after payment is complete.';
            icon.className = 'bi bi-phone';
            icon.style.color = '#ff9800';
            iconWrap.style.background = 'rgba(255, 152, 0, 0.15)';
        }
    }

    async function checkPaymentStatus() {
        if (!polling || paymentState !== 'pending') return;

        checkButton.disabled = true;
        try {
            const response = await fetch(page.dataset.paymentStatusUrl, {
                headers: { Accept: 'application/json' },
            });
            const result = await response.json();

            if (!response.ok || result.status_check_failed) {
                throw new Error(result.message || 'Status check failed');
            }

            renderState(result.payment_status);
        } catch {
            feedback.className = 'alert alert-danger mx-auto mb-4';
            feedback.textContent = 'We could not check the payment status just now. Confirm the prompt on your phone, then try checking again.';
            feedback.style.display = '';
        } finally {
            checkButton.disabled = false;
        }
    }

    checkButton.addEventListener('click', checkPaymentStatus);
    if (paymentState === 'pending') {
        window.setInterval(checkPaymentStatus, 7000);
    }
})();
</script>
@endpush
