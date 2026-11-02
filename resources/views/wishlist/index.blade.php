@extends('layouts.public')

@section('title', 'My Wishlist — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">My Wishlist</h2>
    </div>

    @if ($products->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3" style="font-size: 4rem; opacity: 0.2;">
                <i class="bi bi-heartbreak"></i>
            </div>
            <h5 class="fw-bold">Your wishlist is empty</h5>
            <p class="text-muted mb-4">Save items you love by tapping the heart icon on any product.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-sh-orange btn-lg">Browse Products</a>
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
