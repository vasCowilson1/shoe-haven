@extends('layouts.public')

@section('title', 'About Us — ' . config('app.name', 'Shoe Haven'))

@section('content')
<section class="py-5" style="background: linear-gradient(135deg, #1a1a24 0%, #2a2a3a 100%);">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="sh-eyebrow">About Shoe Haven</div>
                <h1 class="fw-bold mb-4 text-white">Uganda's <span style="color: var(--sh-orange);">Premier</span> Shoe Destination</h1>
                <p class="lead mb-4 text-white-50">
                    At Shoe Haven, we believe every step counts. Founded in Kampala, we bring you premium
                    footwear — from trendy sneakers and elegant heels to durable boots and activewear —
                    all at prices that respect your wallet.
                </p>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.03);">
                            <div class="fs-3 fw-bold" style="color: var(--sh-orange);">{{ $userCount }}+</div>
                            <small class="text-white-50">Happy Customers</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.03);">
                            <div class="fs-3 fw-bold" style="color: var(--sh-orange);">{{ $productCount }}+</div>
                            <small class="text-white-50">Shoe Styles</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.03);">
                            <div class="fs-3 fw-bold" style="color: var(--sh-orange);">4.9</div>
                            <small class="text-white-50">Average Rating</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.03);">
                            <div class="fs-3 fw-bold" style="color: var(--sh-orange);">{{ $orderCount }}+</div>
                            <small class="text-white-50">Orders Fulfilled</small>
                        </div>
                    </div>
                </div>
                <a href="{{ route('shop.index') }}" class="btn btn-sh-orange btn-lg">Shop Now</a>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=700&q=80"
                     alt="Shoe collection" class="w-100 rounded-4 shadow">
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-4">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1552346154-1d3a0e1f4a3d?w=600&q=80"
                     alt="Our story" class="w-100 rounded-4 shadow">
            </div>
            <div class="col-lg-6">
                <div class="sh-eyebrow">Our Story</div>
                <h2 class="fw-bold mb-3">From a Small Dream to a National Brand</h2>
                <p class="text-muted mb-3">
                    Shoe Haven was born in 2020 in a small shop in Kampala. What started as a passion
                    project — sourcing quality footwear for friends and family — quickly grew into one of
                    Uganda's most trusted online shoe retailers.
                </p>
                <p class="text-muted mb-3">
                    We saw a gap: Ugandans wanted stylish, durable shoes but struggled with limited options
                    and high prices. So we built a platform that connects you directly to premium footwear
                    at factory-friendly prices.
                </p>
                <p class="text-muted">
                    Today we serve thousands of customers across all regions of Uganda, with mobile money
                    payment options and fast delivery to your doorstep.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <div class="sh-eyebrow">Our Values</div>
            <h2 class="sh-title">What We Stand For</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card bg-white border-0 h-100 p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border-radius: 14px; background: var(--sh-orange);">
                        <i class="bi bi-gem fs-4 text-white"></i>
                    </div>
                    <h5 class="fw-bold">Premium Quality</h5>
                    <p class="text-muted small mb-0">Every pair is sourced from trusted manufacturers and passes strict quality checks before reaching your doorstep.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-0 h-100 p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border-radius: 14px; background: var(--sh-orange);">
                        <i class="bi bi-phone fs-4 text-white"></i>
                    </div>
                    <h5 class="fw-bold">Easy Payments</h5>
                    <p class="text-muted small mb-0">Pay seamlessly with MTN Mobile Money or Airtel Money. No credit card needed — just your phone.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-0 h-100 p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border-radius: 14px; background: var(--sh-orange);">
                        <i class="bi bi-box-seam fs-4 text-white"></i>
                    </div>
                    <h5 class="fw-bold">Fast Delivery</h5>
                    <p class="text-muted small mb-0">Free delivery on orders over UGX 200,000. Get your shoes delivered within 2–5 business days anywhere in Uganda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <div class="sh-eyebrow">Meet the Team</div>
            <h2 class="sh-title">The People Behind Shoe Haven</h2>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 col-lg-3">
                <div class="text-center">
                    <img src="{{ asset('images/team/edrine.jpg') }}" alt="Eng Mwima Edrine"
                         style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; object-position: center 20%;">
                    <h6 class="fw-bold mb-1 mt-3">Eng Mwima Edrine</h6>
                    <small class="text-muted">Founder & CEO</small>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="text-center">
                    <div class="mx-auto mb-3" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--sh-orange), #e65c00); display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-fs-2 text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Dr. Ainembabazi Daphine</h6>
                    <small class="text-muted">Operations Manager</small>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="text-center">
                    <div class="mx-auto mb-3" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--sh-orange), #e65c00); display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-fill text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Eng Bongomin Ronald</h6>
                    <small class="text-muted">Head of Logistics</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <div class="sh-eyebrow">Milestones</div>
            <h2 class="sh-title">Our Journey</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="timeline">
                    <div class="d-flex gap-4 mb-4">
                        <div class="text-end" style="width: 100px; flex-shrink: 0;">
                            <span class="fw-bold" style="color: var(--sh-orange);">2020</span>
                        </div>
                        <div style="width: 2px; background: var(--sh-orange); flex-shrink: 0;"></div>
                        <div>
                            <h6 class="fw-bold mb-1">Humble Beginnings</h6>
                            <p class="text-muted small mb-0">Shoe Haven launched as a small shop in Kampala with just 50 pairs of shoes.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-4 mb-4">
                        <div class="text-end" style="width: 100px; flex-shrink: 0;">
                            <span class="fw-bold" style="color: var(--sh-orange);">2022</span>
                        </div>
                        <div style="width: 2px; background: var(--sh-orange); flex-shrink: 0;"></div>
                        <div>
                            <h6 class="fw-bold mb-1">Online Launch</h6>
                            <p class="text-muted small mb-0">Launched our e-commerce platform with mobile money payment integration.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-4 mb-4">
                        <div class="text-end" style="width: 100px; flex-shrink: 0;">
                            <span class="fw-bold" style="color: var(--sh-orange);">2024</span>
                        </div>
                        <div style="width: 2px; background: var(--sh-orange); flex-shrink: 0;"></div>
                        <div>
                            <h6 class="fw-bold mb-1">National Reach</h6>
                            <p class="text-muted small mb-0">Expanded delivery to all regions of Uganda and crossed 5,000 orders.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-4">
                        <div class="text-end" style="width: 100px; flex-shrink: 0;">
                            <span class="fw-bold" style="color: var(--sh-orange);">2026</span>
                        </div>
                        <div style="width: 2px; background: var(--sh-orange); flex-shrink: 0;"></div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ $productCount }}+ Styles</h6>
                            <p class="text-muted small mb-0">Today we offer {{ $productCount }}+ styles and serve {{ $userCount }}+ happy customers across Uganda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-4 text-center">
        <div class="sh-promo p-5">
            <h3 class="mb-3">Ready to Find Your Perfect Pair?</h3>
            <p class="mb-4 fs-5">Browse our collection of {{ $productCount }}+ styles and find shoes that match your vibe.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-light btn-lg fw-bold">Explore Collection</a>
        </div>
    </div>
</section>
@endsection