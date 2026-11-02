<footer class="sh-footer pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h6 class="fs-5">Shoe<span style="color: var(--sh-orange);">Haven</span></h6>
                <p class="small">Your trusted shoe store for premium footwear — sneakers, boots, heels and more, delivered across Uganda.</p>
                <div class="mt-3">
                    <a href="https://facebook.com/shoehaven" target="_blank" rel="noopener" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="https://instagram.com/shoehaven" target="_blank" rel="noopener" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="https://twitter.com/shoehaven" target="_blank" rel="noopener" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://wa.me/256700000000" target="_blank" rel="noopener" class="social-icon"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Shop</h6>
                <ul class="list-unstyled small">
                    @if (isset($categories))
                        @foreach ($categories as $cat)
                            <li class="mb-2"><a href="{{ route('shop.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Company</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('about.index') }}">About Us</a></li>
                    <li class="mb-2"><a href="{{ route('contact.index') }}">Contact Us</a></li>
                    <li class="mb-2"><a href="{{ route('help.index') }}">Help Center</a></li>
                    <li class="mb-2"><a href="{{ route('shop.index') }}">Shop All</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Account</h6>
                <ul class="list-unstyled small">
                    @auth
                        <li class="mb-2"><a href="{{ route('orders.index') }}">My Orders</a></li>
                        <li class="mb-2"><a href="{{ route('wishlist.index') }}">Wishlist</a></li>
                        <li class="mb-2"><a href="{{ route('profile.edit') }}">Profile</a></li>
                    @else
                        <li class="mb-2"><a href="{{ route('login') }}">Login</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Legal</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('privacy.index') }}">Privacy Policy</a></li>
                    <li class="mb-2"><a href="{{ route('terms.index') }}">Terms of Service</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>Contact</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Kampala, Uganda</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2"></i>support@shoehaven.com</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2"></i>+256 700 000 000</li>
                </ul>
            </div>
        </div>
        <div class="sh-footer-bottom mt-4 pt-4 text-center small">
            &copy; {{ date('Y') }} Shoe Haven. All rights reserved.
        </div>
    </div>
</footer>
