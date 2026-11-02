@php
    $cartCount = \App\Http\Controllers\CartController::itemCount();
@endphp

<nav class="navbar navbar-expand-lg sh-navbar py-3 sticky-top">
    <div class="container">
        <a class="navbar-brand fs-4" href="{{ route('home') }}">Shoe<span>Haven</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" style="border-color: rgba(255,255,255,0.3);">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto gap-lg-4">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                @if (isset($categories))
                    @foreach ($categories as $cat)
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop.index') && request('category') === $cat->slug ? 'active' : '' }}" href="{{ route('shop.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                    @endforeach
                @endif
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop.index') && !request()->filled('category') && !request()->filled('brand') && !request()->filled('search') ? 'active' : '' }}" href="{{ route('shop.index') }}">Shop All</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                <a href="{{ route('cart.index') }}" class="sh-cart-icon">
                    <i class="bi bi-cart3"></i>
                    @if ($cartCount > 0)
                        <span class="sh-cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>
                @auth
                    <div class="dropdown">
                        <button class="btn btn-sh-orange btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                            <li><a class="dropdown-item text-white" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item text-white" href="{{ route('orders.index') }}"><i class="bi bi-bag me-2"></i>My Orders</a></li>
                            <li><a class="dropdown-item text-white" href="{{ route('wishlist.index') }}"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
                            @if (auth()->user()->isAdmin())
                                <li><a class="dropdown-item text-white" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-shaded me-2"></i>Admin Panel</a></li>
                            @endif
                            <li><hr class="dropdown-divider border-secondary"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-white"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sh-outline-white btn-sm">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-sh-orange btn-sm">Sign Up</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
