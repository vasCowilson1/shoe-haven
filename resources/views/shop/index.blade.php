@extends('layouts.public')

@section('title', 'Shop — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="d-flex">
    {{-- Desktop Sidebar --}}
    <div class="sh-cat-sidebar-wrap">
        <div class="sh-cat-sidebar p-3">
            <h6 class="fw-bold mb-3 text-white">Categories</h6>
            <a href="{{ route('shop.index', request()->only('sort', 'brand')) }}"
               class="sh-filter-link {{ $activeCategory === '' ? 'active' : '' }}">
                All Products
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('shop.index', ['category' => $cat->slug] + request()->only('sort', 'brand')) }}"
                   class="sh-filter-link {{ $activeCategory === $cat->slug ? 'active' : '' }}">
                    {{ $cat->name }} <span class="opacity-75">({{ $cat->products_count }})</span>
                </a>
            @endforeach
            @if ($brands->isNotEmpty())
                <hr style="border-color: rgba(255,255,255,0.1);">
                <h6 class="fw-bold mb-3 text-white">Brands</h6>
                <a href="{{ route('shop.index', request()->only('sort', 'category')) }}"
                   class="sh-filter-link {{ $activeBrand === '' ? 'active' : '' }}">
                    All Brands
                </a>
                @foreach ($brands as $brand)
                    <a href="{{ route('shop.index', ['brand' => $brand] + request()->only('sort', 'category')) }}"
                       class="sh-filter-link {{ $activeBrand === $brand ? 'active' : '' }}">
                        {{ $brand }}
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <div class="flex-grow-1" style="min-height: 100vh; min-width: 0; max-width: 100%;">
        <div class="container py-3 py-md-4">
            {{-- Header --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <h2 class="fw-bold mb-0 fs-4">
                    @if (request('search'))
                        Search results for "{{ request('search') }}"
                    @elseif ($activeCategory)
                        {{ $categories->firstWhere('slug', $activeCategory)->name ?? 'Shop' }}
                    @else
                        All Products
                    @endif
                </h2>
                <span class="text-muted small">{{ $products->total() }} products found</span>
            </div>

            {{-- Shop Search Bar --}}
            <form action="{{ route('shop.index') }}" method="GET" class="mb-3">
                @if (request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                @if (request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
                @if (request('sort') && request('sort') !== 'latest') <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search shoes by name, brand, or style..." value="{{ request('search') }}" aria-label="Search shoes">
                    <button class="btn btn-sh-orange" type="submit">Search</button>
                    @if (request('search'))
                        <a href="{{ route('shop.index', request()->only('category', 'brand', 'sort')) }}" class="btn btn-outline-secondary" title="Clear search">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>

            @if (request('search') || request('category') || request('brand'))
                <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                    <span class="small text-muted">Active filters:</span>
                    @if (request('search'))
                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 py-1 px-2">
                            Search: "{{ request('search') }}"
                            <a href="{{ route('shop.index', request()->only('category', 'brand', 'sort')) }}" class="text-secondary text-decoration-none" title="Remove search filter">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('category'))
                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 py-1 px-2">
                            Category: {{ $categories->firstWhere('slug', request('category'))->name ?? request('category') }}
                            <a href="{{ route('shop.index', request()->only('search', 'brand', 'sort')) }}" class="text-secondary text-decoration-none" title="Remove category filter">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('brand'))
                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 py-1 px-2">
                            Brand: {{ request('brand') }}
                            <a href="{{ route('shop.index', request()->only('search', 'category', 'sort')) }}" class="text-secondary text-decoration-none" title="Remove brand filter">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    <a href="{{ route('shop.index') }}" class="small text-decoration-none ms-1" style="color: var(--sh-orange);">Clear all</a>
                </div>
            @endif

            {{-- Mobile Filter Bar --}}
            <div class="sh-mobile-filter-bar gap-2 mb-3">
                <div class="dropdown flex-fill">
                    <button class="btn btn-outline-dark btn-sm w-100 text-start" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-funnel me-1"></i> Category
                    </button>
                    <ul class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto;">
                        <li>
                            <a class="dropdown-item {{ $activeCategory === '' ? 'active' : '' }}"
                               href="{{ route('shop.index', request()->only('sort', 'search')) }}">
                                All Products
                            </a>
                        </li>
                        @foreach ($categories as $cat)
                            <li>
                                <a class="dropdown-item {{ $activeCategory === $cat->slug ? 'active' : '' }}"
                                   href="{{ route('shop.index', ['category' => $cat->slug] + request()->only('sort', 'search')) }}">
                                    {{ $cat->name }} <span class="text-muted">({{ $cat->products_count }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if ($brands->isNotEmpty())
                    <div class="dropdown flex-fill">
                        <button class="btn btn-outline-dark btn-sm w-100 text-start" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-tag me-1"></i> Brand
                        </button>
                        <ul class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto;">
                            <li>
                                <a class="dropdown-item {{ $activeBrand === '' ? 'active' : '' }}"
                                   href="{{ route('shop.index', request()->only('sort', 'category')) }}">
                                    All Brands
                                </a>
                            </li>
                            @foreach ($brands as $brand)
                                <li>
                                    <a class="dropdown-item {{ $activeBrand === $brand ? 'active' : '' }}"
                                       href="{{ route('shop.index', ['brand' => $brand] + request()->only('sort', 'category')) }}">
                                        {{ $brand }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <select class="sh-sort-select flex-fill" onchange="var s=this.value; var u=new URL(window.location.href); u.searchParams.set('sort', s); if(s==='latest') u.searchParams.delete('sort'); window.location.href=u.toString();">
                    <option value="latest" {{ $currentSort === 'latest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ $currentSort === 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="price_asc" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name_asc" {{ $currentSort === 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                    <option value="name_desc" {{ $currentSort === 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                </select>
            </div>

            {{-- Desktop Sort --}}
            <div class="d-flex justify-content-end mb-3 d-none d-lg-flex">
                <select class="sh-sort-select" onchange="var s=this.value; var u=new URL(window.location.href); u.searchParams.set('sort', s); if(s==='latest') u.searchParams.delete('sort'); window.location.href=u.toString();">
                    <option value="latest" {{ $currentSort === 'latest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ $currentSort === 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="price_asc" {{ $currentSort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ $currentSort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name_asc" {{ $currentSort === 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                    <option value="name_desc" {{ $currentSort === 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                </select>
            </div>

            {{-- Products Grid --}}
            <div class="row row-cols-2 row-cols-md-5 g-3">
                @forelse ($products as $product)
                    <div class="col">
                        @include('partials.product-card', ['product' => $product])
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="bi bi-search" style="font-size: 3rem; opacity: 0.3; color: var(--sh-orange);"></i>
                        <h5 class="fw-bold mt-3 mb-2 text-dark">No shoes found</h5>
                        <p class="mb-3">
                            @if (request('search'))
                                We couldn't find any products matching <strong>"{{ request('search') }}"</strong>.
                            @else
                                No products found in this selection.
                            @endif
                        </p>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="{{ route('shop.index') }}" class="btn btn-sh-orange btn-sm">
                                View All Shoes
                            </a>
                            @if (request('search') && (request('category') || request('brand')))
                                <a href="{{ route('shop.index', ['search' => request('search')]) }}" class="btn btn-outline-secondary btn-sm">
                                    Search across all categories
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
