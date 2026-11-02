@extends('layouts.public')

@section('title', 'Your Cart — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="container pb-5" style="padding-top: 0.2mm;">
    <h2 class="fw-bold mb-4">Your Cart</h2>

    @if ($products->isEmpty())
        <div class="text-center py-5">
            <div class="mx-auto mb-4 d-flex align-items-center justify-content-center"
                 style="width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.05);">
                <i class="bi bi-cart-x" style="font-size: 2.8rem; color: rgba(255,255,255,0.3);"></i>
            </div>
            <h4 class="fw-bold mb-2">Your cart is empty</h4>
            <p class="text-muted mb-4 mx-auto" style="max-width: 360px;">Looks like you haven't added anything yet. Browse our collection and find your perfect pair.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-sh-orange btn-lg">Start Shopping</a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-sm-8">
                @foreach ($products as $product)
                    <div class="d-flex align-items-center justify-content-between border-bottom py-3 sh-cart-row">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset($product->image) }}" alt="{{ $product->name }}">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                                <small class="text-muted">UGX {{ number_format($product->price) }} each</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-4">
                            <form action="{{ route('cart.update', $product) }}" method="POST" class="d-flex align-items-center gap-2" id="cart-form-{{ $product->id }}">
                                @csrf
                                @method('PATCH')
                                <div class="sh-qty-control">
                                    <button type="button" onclick="var q=this.parentNode.querySelector('input'); var v=parseInt(q.value)-1; if(v>=1) { q.value=v; this.parentNode.parentNode.submit(); }">-</button>
                                    <input type="number" name="quantity" min="1" value="{{ $product->cart_quantity }}" readonly>
                                    <button type="button" onclick="var q=this.parentNode.querySelector('input'); var v=parseInt(q.value)+1; q.value=v; this.parentNode.parentNode.submit();">+</button>
                                </div>
                            </form>

                            <div class="fw-bold" style="min-width: 110px; text-align: right;">
                                UGX {{ number_format($product->line_total) }}
                            </div>

                            <form action="{{ route('cart.remove', $product) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-sm-4">
                <div class="sh-cart-sidebar">
                @if (!auth()->check())
                    <div class="sh-summary p-4 text-center">
                        <i class="bi bi-person-circle" style="font-size: 3rem; color: rgba(255,255,255,0.3);"></i>
                        <p class="text-white mt-3 mb-3">Please log in to complete your checkout.</p>
                        <a href="{{ route('login') }}" class="btn btn-sh-orange w-100">Login</a>
                    </div>
                @else
                    <div class="sh-summary p-4">
                        <h5 class="mb-4 text-white">Order Summary</h5>
                        @foreach ($products as $item)
                            @php $qty = $item->cart_quantity; $lineTotal = $item->price * $qty; @endphp
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : asset($item->image) }}"
                                         alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                                    <div>
                                        <span class="small fw-medium text-white">{{ $item->name }}</span>
                                        <small class="d-block text-white-50">Qty: {{ $qty }}</small>
                                    </div>
                                </div>
                                <span class="fw-medium text-white small">UGX {{ number_format($lineTotal) }}</span>
                            </div>
                        @endforeach
                        <hr style="border-color: rgba(255,255,255,0.15);">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white-50">Subtotal</span>
                            <span class="text-white">UGX {{ number_format($total) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white-50">Delivery</span>
                            <span class="text-success">{{ $total >= 200000 ? 'Free' : 'UGX 20,000' }}</span>
                        </div>
                        <hr style="border-color: rgba(255,255,255,0.15);">
                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span class="text-white">Total</span>
                            <span style="color: var(--sh-orange);">UGX {{ number_format($total >= 200000 ? $total : $total + 20000) }}</span>
                        </div>

                        <h5 class="fw-bold mb-4 text-white">Delivery Details</h5>
                        <form action="{{ route('checkout.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-white-50">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white-50">Phone Number (MTN / Airtel)</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+256 700 000 000" value="{{ old('phone') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white-50">Delivery Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Street, city, landmark" required>{{ old('address') }}</textarea>
                            </div>
                            <hr style="border-color: rgba(255,255,255,0.15);">
                            <h6 class="fw-bold mb-3 text-white">Payment Method</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="sidebar-mtn" value="mtn" checked>
                                <label class="form-check-label text-white" for="sidebar-mtn">
                                    <i class="bi bi-phone"></i> MTN Mobile Money
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="sidebar-airtel" value="airtel">
                                <label class="form-check-label text-white" for="sidebar-airtel">
                                    <i class="bi bi-phone"></i> Airtel Money
                                </label>
                            </div>
                            <button type="submit" class="btn btn-sh-orange w-100 mt-3 btn-lg">Place Order</button>
                        </form>
                    </div>
                @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
