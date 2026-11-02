@extends('layouts.public')

@section('title', config('app.name', 'Shoe Haven') . ' — Step Into Style')

@section('content')
{{-- ============ HERO CAROUSEL ============ --}}
<section class="sh-hero py-5">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-indicators">
            @foreach ($heroes as $i => $h)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach ($heroes as $i => $h)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                    <div class="container py-4">
                        <div class="row align-items-center g-5">
                            <div class="col-lg-6">
                                <div class="eyebrow mb-3">New Season Collection</div>
                                <h1 class="mb-4">{!! $h->headline !!}</h1>
                                <p class="lead mb-4">{{ $h->subtitle }}</p>
                                <div class="d-flex gap-3 flex-wrap">
                                    <a href="{{ $h->button_url }}" class="btn btn-sh-orange btn-lg">{{ $h->button_text }}</a>
                                    <a href="{{ $h->secondary_button_url }}" class="btn btn-sh-outline-white btn-lg">{{ $h->secondary_button_text }}</a>
                                </div>
                                <div class="d-flex gap-4 mt-5">
                                    @foreach ($h->stats ?? [] as $stat)
                                        <div>
                                            <div class="fs-4 fw-bold text-white">{!! $stat['value'] !!}{!! !empty($stat['icon']) ? '<i class="bi bi-star-fill" style="color: var(--sh-orange); font-size: .7em;"></i>' : '' !!}</div>
                                            <small class="text-white-50">{{ $stat['label'] }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="sh-hero-img-wrap p-4 position-relative text-center">
                                    <img src="{{ asset($h->image) }}"
                                         alt="Hero banner" class="img-fluid rounded-4" style="max-height: 420px; object-fit: cover;">
                                    <div class="sh-hero-badge position-absolute" style="bottom: 10px; left: 0;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="sh-add-btn" style="background: var(--sh-orange);"><i class="bi bi-lightning-fill"></i></div>
                                            <div>
                                                <div class="fw-bold" style="font-size:.85rem;">Free Express Shipping</div>
                                                <small class="text-muted">On orders over UGX 200,000</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

{{-- ============ SEARCH BAR ============ --}}
<section class="py-4" style="background: #1a1a24;">
    <div class="container">
        <form action="{{ route('shop.index') }}" method="GET" class="mx-auto" style="max-width: 600px;">
            <div class="input-group">
                <input type="text" name="search" class="form-control form-control-lg" placeholder="Search shoes, brands, categories..." value="{{ request('search') }}" style="border: none; border-radius: 8px 0 0 8px;">
                <button type="submit" class="btn btn-sh-orange btn-lg" style="border-radius: 0 8px 8px 0;">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ============ FEATURE STRIP ============ --}}
<section class="sh-strip py-3">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3 item d-flex flex-column align-items-center">
                <i class="bi bi-truck"></i>
                <strong>Free Shipping</strong>
                <small>On orders over UGX 200,000</small>
            </div>
            <div class="col-6 col-md-3 item d-flex flex-column align-items-center">
                <i class="bi bi-shield-check"></i>
                <strong>Secure Payment</strong>
                <small>MTN &amp; Airtel Money</small>
            </div>
            <div class="col-6 col-md-3 item d-flex flex-column align-items-center">
                <i class="bi bi-arrow-repeat"></i>
                <strong>Easy Returns</strong>
                <small>14-day return policy</small>
            </div>
            <div class="col-6 col-md-3 item d-flex flex-column align-items-center">
                <i class="bi bi-headset"></i>
                <strong>24/7 Support</strong>
                <small>We're here to help</small>
            </div>
        </div>
    </div>
</section>

{{-- ============ FEATURED PRODUCTS ============ --}}
<section class="py-5 bg-light" id="products">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
            <div>
                <div class="sh-eyebrow">Our Collection</div>
                <h2 class="sh-title mb-0">All Products</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="btn btn-sh-outline">View All →</a>
        </div>
        <div class="row row-cols-2 row-cols-md-5 g-3">
            @forelse ($featuredProducts as $product)
                <div class="col">
                    @include('partials.product-card', ['product' => $product])
                </div>
            @empty
                <p class="text-muted text-center">No products yet — run the seeder to add demo products.</p>
            @endforelse
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $featuredProducts->withQueryString()->links() }}
        </div>
    </div>
</section>

@if ($recentProducts->isNotEmpty())
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
            <div>
                <div class="sh-eyebrow">Your Footprints</div>
                <h2 class="sh-title mb-0">Recently Viewed</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="btn btn-sh-outline btn-sm">View All</a>
        </div>
        <div class="row row-cols-2 row-cols-md-5 g-3">
            @foreach ($recentProducts as $product)
                <div class="col">
                    @include('partials.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ PROMO BANNER ============ --}}
<section class="py-5">
    <div class="container py-3">
        <div class="sh-promo p-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="mb-2">End of Season Sale — Up to 40% Off</h3>
                    <p class="mb-0">Pay easily with MTN Mobile Money or Airtel Money at checkout. Limited stock, while it lasts!</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="{{ route('shop.index') }}" class="btn btn-light btn-lg fw-bold">Shop the Sale</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
