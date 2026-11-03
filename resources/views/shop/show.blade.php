@extends('layouts.public')

@section('title', $product->name . ' — ' . config('app.name', 'Shoe Haven'))

@push('styles')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "brand": { "@type": "Brand", "name": "{{ $product->brand }}" },
    "description": "{{ $product->description?->description ?? 'Premium quality footwear.' }}",
    "image": "{{ Str::startsWith($product->image, 'http') ? $product->image : asset($product->image) }}",
    "offers": {
        "@type": "Offer",
        "price": "{{ $product->price }}",
        "priceCurrency": "UGX",
        "availability": "https://schema.org/InStock",
        "url": "{{ url()->current() }}"
    }
}
</script>
<style>
    .sh-lightbox { position: fixed; inset: 0; z-index: 99999; background: rgba(0,0,0,0.92); display: none; align-items: center; justify-content: center; cursor: zoom-out; animation: sh-fade-in .2s ease; }
    .sh-lightbox.open { display: flex; }
    .sh-lightbox img { max-width: 90vw; max-height: 90vh; object-fit: contain; border-radius: 8px; }
    .sh-size-chart th, .sh-size-chart td { text-align: center; padding: .5rem .75rem; }
    .sh-size-chart tbody tr:nth-child(odd) { background: rgba(255,255,255,0.03); }
    .sh-star-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 2px; }
    .sh-star-input input { display: none; }
    .sh-star-input label { font-size: 1.4rem; color: #ddd; cursor: pointer; transition: color .15s; }
    .sh-star-input input:checked ~ label,
    .sh-star-input label:hover,
    .sh-star-input label:hover ~ label { color: #ffc107; }
</style>
@endpush

@section('content')
<div class="container py-3">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none">Shop</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.index', ['category' => $product->category?->slug]) }}" class="text-decoration-none">{{ $product->category?->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-6">
            <div class="position-relative" style="cursor: zoom-in;">
                @if ($product->discount)
                    <span class="sh-badge-sale" style="position: absolute; top: 12px; left: 12px; z-index: 2;">-{{ $product->discount }}%</span>
                @elseif ($product->is_new)
                    <span class="sh-badge-new" style="position: absolute; top: 12px; left: 12px; z-index: 2;">NEW</span>
                @endif
                <img src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset($product->image) }}"
                     alt="{{ $product->name }}" class="w-100 rounded-4" loading="lazy"
                     style="max-height: 500px; object-fit: cover;" id="main-product-img"
                     onclick="openLightbox(this.src)">
            </div>
            @if ($product->images->isNotEmpty())
                <div class="d-flex gap-2 mt-3 overflow-auto pb-2" style="scrollbar-width: thin;">
                    @foreach ($product->images as $img)
                        <img src="{{ $img->image_url }}"
                             alt="{{ $product->name }} {{ $loop->iteration }}"
                             class="gallery-thumb rounded-3 border cursor-pointer flex-shrink-0"
                             style="width: 72px; height: 72px; object-fit: cover; {{ $loop->first ? 'border-color: var(--sh-orange) !important;' : '' }}"
                             onclick="swapImage('{{ $img->image_url }}', this)"
                             onmouseover="this.style.borderColor='var(--sh-orange)'"
                             onmouseout="this.style.borderColor='{{ $loop->first ? 'var(--sh-orange)' : '#ddd' }}'"
                             loading="lazy">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-lg-6">
            <a href="{{ route('shop.index', ['brand' => $product->brand]) }}" class="text-decoration-none">
                <div class="small text-uppercase mb-2" style="color: var(--sh-orange);">{{ $product->brand }}</div>
            </a>
            <h1 class="fw-bold mb-3">{{ $product->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="fs-3 fw-bold" style="color: var(--sh-orange);">UGX {{ number_format($product->price) }}</span>
                @if ($product->original_price)
                    <span class="text-decoration-line-through text-muted fs-5">UGX {{ number_format($product->original_price) }}</span>
                @endif
            </div>

            <p class="mb-4">
                {{ $product->description?->description ?? 'Premium quality footwear crafted for comfort and style. Perfect for everyday wear.' }}
            </p>

            <div class="d-flex gap-2 mb-4">
                <div class="text-center p-3 rounded-3" style="background: rgba(255,255,255,0.05); min-width: 90px;">
                    <i class="bi bi-box-seam fs-5"></i>
                    <small class="d-block text-white-50">
                        @if ($product->stock && $product->stock->quantity > 0)
                            {{ $product->stock->quantity }} in stock
                        @else
                            Out of stock
                        @endif
                    </small>
                </div>
                <div class="text-center p-3 rounded-3" style="background: rgba(255,255,255,0.05); min-width: 90px;">
                    <i class="bi bi-truck fs-5"></i>
                    <small class="d-block text-white-50">Free delivery</small>
                </div>
                <div class="text-center p-3 rounded-3" style="background: rgba(255,255,255,0.05); min-width: 90px;">
                    <i class="bi bi-arrow-repeat fs-5"></i>
                    <small class="d-block text-white-50">14-day returns</small>
                </div>
            </div>

            @php $availableSizes = $product->sizeStock->where('quantity', '>', 0); @endphp
            <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-4 js-cart-add-form">
                @csrf
                @if ($product->sizeStock->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Select Size</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach ($product->sizeStock->sortBy('size') as $ss)
                                <label class="d-flex align-items-center justify-content-center rounded-3 border cursor-pointer"
                                       style="width: 48px; height: 40px; font-size: .85rem; font-weight: 600; transition: all .15s; {{ $ss->quantity === 0 ? 'opacity: .3; pointer-events: none;' : '' }}"
                                       onmouseover="this.style.borderColor='var(--sh-orange)'" onmouseout="this.style.borderColor='#ddd'">
                                    <input type="radio" name="size" value="{{ $ss->size }}" class="d-none"
                                           @if ($ss->quantity > 0) required @else disabled @endif
                                           onchange="document.querySelectorAll('.size-option').forEach(el => el.style.borderColor='#ddd'); this.closest('label').style.borderColor='var(--sh-orange)'; this.closest('label').style.background='rgba(255,102,0,0.08)'">
                                    <span>{{ $ss->size }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="d-flex gap-3 flex-wrap align-items-center">
                    <div class="input-group" style="max-width: 180px;">
                        <input type="number" name="quantity" value="1" min="1" class="form-control bg-dark text-white border-secondary">
                        <button type="submit" class="btn btn-sh-orange"
                                @disabled(($product->stock?->quantity ?? 0) < 1 || ($product->sizeStock->isNotEmpty() && $availableSizes->isEmpty()))>
                            Add to Cart
                        </button>
                    </div>
                </div>
            </form>

            <div class="d-flex gap-3 flex-wrap align-items-center">
                <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light px-3">
                        <i class="bi {{ $wishlisted ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                    </button>
                </form>

                <button type="button" class="btn btn-link text-white-50 text-decoration-none small" data-bs-toggle="modal" data-bs-target="#sizeGuideModal">
                    <i class="bi bi-rulers"></i> Size Guide
                </button>
            </div>
        </div>
    </div>

    {{-- Reviews Section --}}
    <section class="mt-5 pt-5 border-top border-secondary">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Reviews ({{ $reviews->count() }})</h3>
        </div>

        @auth
            <div class="card bg-dark border-0 mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Write a Review</h6>
                    <form action="{{ route('reviews.store', $product) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Rating</label>
                            <div class="sh-star-input">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                                    <label for="star{{ $i }}">&#9733;</label>
                                @endfor
                            </div>
                        </div>
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="3" placeholder="Share your thoughts about this product..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sh-orange btn-sm">Submit Review</button>
                    </form>
                </div>
            </div>
        @else
            <div class="text-center text-muted small mb-4">
                <a href="{{ route('login') }}" style="color: var(--sh-orange);">Log in</a> to leave a review.
            </div>
        @endauth

        @forelse ($reviews as $review)
            <div class="d-flex gap-3 mb-4">
                <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold text-white flex-shrink-0"
                     style="width: 40px; height: 40px; font-size: .85rem; background: #6c757d;">
                    {{ substr($review->user->name, 0, 1) }}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold small">{{ $review->user->name }}</span>
                        <span class="text-warning small">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                            @endfor
                        </span>
                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0 small">{{ $review->content }}</p>
                </div>
            </div>
        @empty
            <p class="text-muted small">No reviews yet. Be the first to review this product!</p>
        @endforelse
    </section>

    @if ($related->isNotEmpty())
        <section class="mt-5 pt-5 border-top border-secondary">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Related Products</h3>
                <a href="{{ route('shop.index', ['category' => $product->category?->slug]) }}" class="btn btn-sh-outline">View All</a>
            </div>
            <div class="row row-cols-2 row-cols-md-5 g-3">
                @foreach ($related as $rel)
                    <div class="col">
                        @include('partials.product-card', ['product' => $rel])
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>

<div class="modal fade" id="sizeGuideModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="bi bi-rulers me-2"></i>Size Guide</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-white-50 mb-3">Find your perfect fit. Measure your foot length in cm and match it to the EU size below.</p>
                <div class="table-responsive">
                    <table class="table table-dark table-sm sh-size-chart mb-0">
                        <thead>
                            <tr><th>EU</th><th>UK</th><th>US Men</th><th>US Women</th><th>Foot (cm)</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>35</td><td>2.5</td><td>4</td><td>5</td><td>22.1</td></tr>
                            <tr><td>36</td><td>3.5</td><td>5</td><td>6</td><td>22.9</td></tr>
                            <tr><td>37</td><td>4</td><td>5.5</td><td>6.5</td><td>23.5</td></tr>
                            <tr><td>38</td><td>5</td><td>6.5</td><td>7.5</td><td>24.1</td></tr>
                            <tr><td>39</td><td>6</td><td>7.5</td><td>8.5</td><td>24.8</td></tr>
                            <tr><td>40</td><td>6.5</td><td>8</td><td>9</td><td>25.4</td></tr>
                            <tr><td>41</td><td>7.5</td><td>9</td><td>10</td><td>26.0</td></tr>
                            <tr><td>42</td><td>8</td><td>9.5</td><td>10.5</td><td>26.7</td></tr>
                            <tr><td>43</td><td>9</td><td>10.5</td><td>11.5</td><td>27.3</td></tr>
                            <tr><td>44</td><td>9.5</td><td>11</td><td>12</td><td>27.9</td></tr>
                            <tr><td>45</td><td>10.5</td><td>12</td><td>13</td><td>28.6</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="small text-white-50 mt-3 mb-0"><i class="bi bi-info-circle"></i> Sizes may vary by brand.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function swapImage(url, el) {
        document.getElementById('main-product-img').src = url;
        document.querySelectorAll('.gallery-thumb').forEach(function(t) {
            t.style.borderColor = '#ddd';
        });
        el.style.borderColor = 'var(--sh-orange)';
    }
    function openLightbox(src) {
        var lb = document.getElementById('sh-lightbox');
        if (!lb) {
            lb = document.createElement('div');
            lb.id = 'sh-lightbox';
            lb.className = 'sh-lightbox';
            lb.onclick = function() { this.classList.remove('open'); };
            var img = document.createElement('img');
            img.alt = 'Product zoom';
            lb.appendChild(img);
            document.body.appendChild(lb);
        }
        lb.querySelector('img').src = src;
        lb.classList.add('open');
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var lb = document.getElementById('sh-lightbox');
            if (lb) lb.classList.remove('open');
        }
    });
</script>
@endpush