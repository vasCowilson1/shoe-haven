@php
    $cartCount = \App\Http\Controllers\CartController::itemCount();
    $isHome = request()->routeIs('home');
    $isShop = request()->routeIs('shop.*');
    $isWishlist = request()->routeIs('wishlist.*');
    $isCart = request()->routeIs('cart.*') || request()->routeIs('checkout.*');
    $isAccount = request()->routeIs('profile.*') || request()->routeIs('orders.*') || request()->routeIs('login') || request()->routeIs('register');
@endphp

<nav class="sh-bottom-nav d-md-none" aria-label="Mobile Navigation">
    <div class="sh-bottom-nav-container">
        {{-- Home --}}
        <a href="{{ route('home') }}" class="sh-bottom-nav-item {{ $isHome ? 'active' : '' }}">
            <i class="bi {{ $isHome ? 'bi-house-door-fill' : 'bi-house-door' }}"></i>
            <span>Home</span>
        </a>

        {{-- Shop --}}
        <a href="{{ route('shop.index') }}" class="sh-bottom-nav-item {{ $isShop ? 'active' : '' }}">
            <i class="bi {{ $isShop ? 'bi-grid-fill' : 'bi-grid' }}"></i>
            <span>Shop</span>
        </a>

        {{-- Wishlist --}}
        <a href="{{ route('wishlist.index') }}" class="sh-bottom-nav-item {{ $isWishlist ? 'active' : '' }}">
            <i class="bi {{ $isWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
            <span>Wishlist</span>
        </a>

        {{-- Cart --}}
        <a href="{{ route('cart.index') }}" class="sh-bottom-nav-item {{ $isCart ? 'active' : '' }}">
            <div class="position-relative d-inline-flex">
                <i class="bi {{ $isCart ? 'bi-bag-fill' : 'bi-bag' }}"></i>
                @if ($cartCount > 0)
                    <span class="sh-bottom-nav-badge">{{ $cartCount }}</span>
                @endif
            </div>
            <span>Cart</span>
        </a>

        {{-- Account --}}
        @auth
            <a href="{{ route('profile.edit') }}" class="sh-bottom-nav-item {{ $isAccount ? 'active' : '' }}">
                <i class="bi {{ $isAccount ? 'bi-person-fill' : 'bi-person' }}"></i>
                <span>Account</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="sh-bottom-nav-item {{ $isAccount ? 'active' : '' }}">
                <i class="bi {{ $isAccount ? 'bi-person-fill' : 'bi-person' }}"></i>
                <span>Account</span>
            </a>
        @endauth
    </div>
</nav>
