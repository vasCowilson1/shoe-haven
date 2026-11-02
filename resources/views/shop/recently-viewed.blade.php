@extends('layouts.public')

@section('title', 'Recently Viewed — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" class="d-flex align-items-center justify-content-center flex-shrink-0 text-decoration-none rounded-circle fw-bold"
           style="width: 40px; height: 40px; background: #fff3e6; color: var(--sh-orange); transition: background .15s;"
           onmouseover="this.style.background='#ffe0cc'" onmouseout="this.style.background='#fff3e6'">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">Recently Viewed</h2>
    </div>

    @if ($products->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3 d-flex align-items-center justify-content-center mx-auto"
                 style="width: 80px; height: 80px; border-radius: 50%; background: #fff3e6;">
                <i class="bi bi-clock-history" style="font-size: 2rem; color: var(--sh-orange);"></i>
            </div>
            <h5 class="fw-bold">No recently viewed items</h5>
            <p class="text-muted mb-4">Start browsing products and they'll appear here.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-sh-orange">Browse Products</a>
        </div>
    @else
        <div class="row row-cols-2 row-cols-md-5 g-3">
            @foreach ($products as $product)
                <div class="col">
                    @include('partials.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection