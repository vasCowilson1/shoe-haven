<div class="sh-product-card">
    <div class="sh-product-img">
        <a href="{{ route('shop.show', $product) }}">
            @if ($product->discount)
                <span class="sh-badge-sale">-{{ $product->discount }}%</span>
            @elseif ($product->is_new)
                <span class="sh-badge-new">NEW</span>
            @endif
            @if ($product->original_price)
                <span class="sh-badge-original">UGX {{ number_format($product->original_price) }}</span>
            @endif
            @php
                $mainImg = Str::startsWith($product->image, 'http') ? $product->image : asset($product->image);
                $galleryImgs = $product->relationLoaded('images') && $product->images->isNotEmpty()
                    ? $product->images->pluck('image_url')->prepend($mainImg)->unique()->values()
                    : collect([$mainImg]);
            @endphp
            <img src="{{ $mainImg }}" alt="{{ $product->name }}" loading="lazy"
                 data-gallery="{{ $galleryImgs->implode(',') }}">
        </a>
        @auth
            <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="sh-wishlist-form">
                @csrf
                @php $inWishlist = auth()->user()->wishlist()->where('product_id', $product->id)->exists(); @endphp
                <button type="submit" class="sh-wishlist-btn {{ $inWishlist ? 'active' : '' }}">
                    <i class="bi {{ $inWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                </button>
            </form>
        @endauth
    </div>
    <div class="body">
        <div class="sh-card-brand">{{ $product->brand }}</div>
        <a href="{{ route('shop.show', $product) }}" class="text-decoration-none">
            <div class="sh-card-name">{{ $product->name }}</div>
        </a>
        <div class="sh-card-price">
            <span class="price-now">UGX {{ number_format($product->price) }}</span>
            @if ($product->original_price)
                <span class="price-old">UGX {{ number_format($product->original_price) }}</span>
                <span class="sh-discount-tag">-{{ $product->discount ?? round((1 - $product->price / $product->original_price) * 100) }}%</span>
            @endif
        </div>
        @if ($product->relationLoaded('comments') && $product->comments->isNotEmpty())
            <div class="sh-card-rating">
                <span class="stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= round($product->comments->avg('rating')) ? '-fill' : '' }}"></i>
                    @endfor
                </span>
                <span class="count">({{ $product->comments->count() }})</span>
            </div>
        @endif
        <div class="sh-card-delivery">
            <i class="bi bi-truck"></i> Free Delivery
        </div>
        @php
            $hasSizes = $product->relationLoaded('sizeStock') && $product->sizeStock->isNotEmpty();
            $availableSizes = $hasSizes ? $product->sizeStock->sortBy('size')->where('quantity', '>', 0) : collect();
        @endphp
        <form action="{{ route('cart.add', $product) }}" method="POST" class="sh-card-cart-form js-cart-add-form">
            @csrf
            @if ($hasSizes)
                <select name="size" class="form-select form-select-sm mb-2" aria-label="Select size for {{ $product->name }}" required>
                    <option value="">Select size</option>
                    @foreach ($availableSizes as $ss)
                        <option value="{{ $ss->size }}">{{ $ss->size }}</option>
                    @endforeach
                </select>
            @endif
            <button type="submit" class="sh-card-cart-btn" @disabled($hasSizes && $availableSizes->isEmpty())>
                <i class="bi bi-cart"></i> Add to cart
            </button>
        </form>
    </div>
</div>
