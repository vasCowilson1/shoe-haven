<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Shoe Haven') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root { --admin-sidebar: 264px; --admin-sidebar-mini: 78px; }
        body { background:#f6f7fb; overflow-x:hidden; }
        .admin-shell { min-height:100vh; display:flex; }

        .sh-sidebar {
            width:var(--admin-sidebar); flex:0 0 var(--admin-sidebar); background:var(--sh-black);
            height:100vh; position:fixed; top:0; left:0; z-index:1040; overflow:hidden;
            transition:width .25s ease, transform .25s ease;
        }
        .sh-sidebar-inner { height:100%; display:flex; flex-direction:column; min-width:0; }

        .sh-brand { min-height:76px; flex:0 0 auto; display:flex; align-items:center; gap:.8rem; padding:1.05rem 1.3rem; border-bottom:1px solid rgba(255,255,255,.07); }
        .sh-brand-mark { width:40px; height:40px; flex:0 0 auto; border-radius:11px; background:var(--sh-orange); color:#fff; display:grid; place-items:center; font-weight:900; font-size:1.05rem; }
        .sh-brand-title { font-size:1.25rem; font-weight:800; line-height:1.2; color:#fff; white-space:nowrap; }
        .sh-brand-sub { font-size:.85rem; color:rgba(255,255,255,.55); line-height:1.3; white-space:nowrap; }

        .sh-nav { flex:1 1 auto; min-height:0; overflow-y:auto; overflow-x:hidden; padding:.55rem .75rem 1.15rem; display:flex; flex-direction:column; flex-wrap:nowrap; scrollbar-width:thin; scrollbar-color:rgba(255,255,255,.28) transparent; }
        .sh-nav::-webkit-scrollbar { width:5px; }
        .sh-nav::-webkit-scrollbar-thumb { background:rgba(255,255,255,.22); border-radius:99px; }
        .sh-nav::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,.35); }
        .sh-nav::-webkit-scrollbar-track { background:transparent; }

        .sh-nav-group { margin-top:1.35rem; width:100%; display:flex; flex-direction:column; flex-wrap:nowrap; }
        .sh-nav-group:first-child { margin-top:.35rem; }
        .sh-nav-label { color:rgba(255,255,255,.42); font-size:.6875rem; text-transform:uppercase; letter-spacing:.14em; font-weight:700; padding:0 .7rem .5rem; white-space:nowrap; display:flex; align-items:center; gap:.6rem; }
        .sh-nav-label::after { content:''; flex:1; height:1px; background:rgba(255,255,255,.09); }

        .sh-sidebar .nav-link {
            height:48px; min-height:48px; padding:.55rem .8rem; margin:.16rem 0; border-radius:10px;
            color:rgba(255,255,255,.72); font-weight:600; font-size:.9375rem;
            display:flex; align-items:center; gap:.875rem; white-space:nowrap;
            transition:background .18s ease, color .18s ease, box-shadow .18s ease;
        }
        .sh-sidebar .nav-link i { width:22px; min-width:22px; flex:0 0 auto; text-align:center; font-size:1.05rem; }
        .sh-sidebar .nav-link:hover { color:#fff; background:rgba(255,255,255,.08); }
        .sh-sidebar .nav-link:focus-visible { outline:2px solid var(--sh-orange); outline-offset:-2px; }
        .sh-sidebar .nav-link.active {
            color:#fff; background:linear-gradient(90deg, rgba(255,102,0,.22), rgba(255,102,0,.10));
            box-shadow:inset 3px 0 0 var(--sh-orange);
        }

        .sh-sidebar-footer { flex:0 0 auto; margin-top:auto; padding:.7rem .75rem; border-top:1px solid rgba(255,255,255,.08); background:rgba(0,0,0,.18); }
        .sh-sidebar-footer .nav-link { height:46px; min-height:46px; }

        .admin-main { min-width:0; flex:1; margin-left:var(--admin-sidebar); transition:margin-left .25s ease; }
        .admin-topbar { height:76px; position:sticky; top:0; z-index:1030; background:#fff; border-bottom:1px solid #e9eaf0; }
        .admin-content { padding:1.25rem; max-width:1700px; margin:0 auto; }
        .admin-page-heading h1 { font-size:1.35rem; font-weight:750; margin:0; }
        .admin-page-heading .breadcrumb { margin:0; font-size:.78rem; white-space:nowrap; }
        .sh-breadcrumbs { margin-top:.15rem; }
        .sh-breadcrumbs ol { display:flex; align-items:center; flex-wrap:nowrap; gap:.4rem; list-style:none; padding:0; margin:0; font-size:.75rem; color:#8a909c; }
        .sh-breadcrumbs a { color:#6f7683; text-decoration:none; }
        .sh-breadcrumbs a:hover { color:var(--sh-orange); }
        .sh-breadcrumbs-sep { color:#b6bbc4; }

        .admin-user { background:#f6f7fb; border-radius:10px; padding:.35rem .65rem; }
        .admin-avatar { width:34px; height:34px; border-radius:50%; display:grid; place-items:center; background:var(--sh-orange); color:#fff; font-weight:700; }
        .sh-collapse-btn { width:38px; height:38px; display:grid; place-items:center; border:1px solid #e1e3e8; background:#fff; border-radius:9px; }
        .sh-stat-card { border:0; border-radius:14px; transition:transform .15s, box-shadow .15s; }
        .sh-stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(20,20,26,.08)!important; }
        .sh-stat-icon { width:46px; height:46px; border-radius:12px; display:grid; place-items:center; font-size:1.2rem; }
        .admin-card { border:0; border-radius:14px; box-shadow:0 2px 12px rgba(20,20,26,.045); }
        .admin-card .card-header { background:#fff; border-bottom:1px solid #eef0f4; padding:1rem 1.1rem; }
        .admin-table th { color:#737887; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; font-weight:700; white-space:nowrap; }
        .admin-table td { padding:.8rem .75rem; }
        .admin-thumb { width:46px; height:46px; object-fit:cover; border-radius:9px; background:#f3f4f6; }
        .sh-sidebar-overlay { display:none; }
        .admin-search { min-width:240px; }

        @media (min-width:992px) {
            body.sidebar-collapsed .sh-sidebar { width:var(--admin-sidebar-mini); flex-basis:var(--admin-sidebar-mini); }
            body.sidebar-collapsed .admin-main { margin-left:var(--admin-sidebar-mini); }
            body.sidebar-collapsed .sh-brand { justify-content:center; padding-left:.5rem; padding-right:.5rem; }
            body.sidebar-collapsed .sh-brand-title, body.sidebar-collapsed .sh-brand-sub,
            body.sidebar-collapsed .sh-nav-label, body.sidebar-collapsed .sh-nav .nav-link span,
            body.sidebar-collapsed .sh-sidebar-footer .nav-link span { display:none; }
            body.sidebar-collapsed .sh-nav { padding:.55rem .5rem 1.15rem; }
            body.sidebar-collapsed .sh-nav-group { margin-top:.55rem; }
            body.sidebar-collapsed .sh-nav .nav-link { justify-content:center; padding-left:.4rem; padding-right:.4rem; }
            body.sidebar-collapsed .sh-sidebar-footer .nav-link { justify-content:center; }
        }

        @media (max-width:991.98px) {
            .sh-sidebar { position:fixed; left:0; top:0; transform:translateX(-100%); box-shadow:10px 0 30px rgba(0,0,0,.18); }
            .sh-sidebar.open { transform:translateX(0); }
            .admin-main { margin-left:0; }
            .sh-sidebar-overlay.show { display:block; position:fixed; inset:0; z-index:1039; background:rgba(0,0,0,.45); }
            .admin-content { padding:.9rem; }
            .admin-search { min-width:0; width:100%; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-shell">
    <div class="sh-sidebar" id="adminSidebar">
        <div class="sh-sidebar-inner">
            <a href="{{ route('admin.dashboard') }}" class="sh-brand text-decoration-none" aria-label="ShoeHaven Administration dashboard">
                <div class="sh-brand-mark">SH</div>
                <div>
                    <div class="sh-brand-title">Shoe<span style="color:var(--sh-orange)">Haven</span></div>
                    <div class="sh-brand-sub">Administration</div>
                </div>
            </a>

            <nav class="sh-nav nav flex-column" aria-label="Admin navigation">
                @foreach (config('admin-nav') as $group)
                    <div class="sh-nav-group">
                        <div class="sh-nav-label">{{ $group['label'] }}</div>
                        @foreach ($group['items'] as $item)
                            @php
                                $isActive = request()->routeIs($item['pattern']);
                            @endphp
                            <a class="nav-link {{ $isActive ? 'active' : '' }}"
                               href="{{ route($item['route']) }}"
                               data-bs-title="{{ $item['label'] }}"
                               aria-label="{{ $item['label'] }}"
                               @if ($isActive) aria-current="page" @endif>
                                <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <div class="sh-sidebar-footer">
                <a class="nav-link" href="{{ route('home') }}" data-bs-title="View Store" aria-label="View Store"><i class="bi bi-shop"></i><span>View Store</span></a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="nav-link w-100 border-0 bg-transparent text-start" data-bs-title="Sign Out" aria-label="Sign Out"><i class="bi bi-box-arrow-right"></i><span>Sign Out</span></button>
                </form>
            </div>
        </div>
    </div>

    <div class="sh-sidebar-overlay" id="sidebarOverlay"></div>

    <main class="admin-main">
        <header class="admin-topbar d-flex align-items-center justify-content-between px-3 px-lg-4">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <button class="sh-collapse-btn" id="sidebarToggle" type="button" aria-label="Toggle sidebar" aria-expanded="false" aria-controls="adminSidebar">
                    <i class="bi bi-layout-sidebar-inset"></i>
                </button>
                @php
                    $adminRoute = request()->route()?->getName() ?? '';
                    $breadcrumbMap = [
                        'admin.dashboard' => [],
                        'admin.products.index' => [['label' => 'Catalog'], ['label' => 'Products']],
                        'admin.products.create' => [['label' => 'Catalog'], ['label' => 'Products', 'url' => route('admin.products.index')], ['label' => 'Add Product']],
                        'admin.products.edit' => [['label' => 'Catalog'], ['label' => 'Products', 'url' => route('admin.products.index')], ['label' => 'Edit Product']],
                        'admin.categories.index' => [['label' => 'Catalog'], ['label' => 'Categories']],
                        'admin.inventory.index' => [['label' => 'Catalog'], ['label' => 'Inventory']],
                        'admin.hero.edit' => [['label' => 'Content'], ['label' => 'Homepage / Hero']],
                        'admin.orders.index' => [['label' => 'Sales & Customers'], ['label' => 'Orders']],
                        'admin.orders.show' => [['label' => 'Sales & Customers'], ['label' => 'Orders', 'url' => route('admin.orders.index')], ['label' => 'Order Details']],
                        'admin.users.index' => [['label' => 'Sales & Customers'], ['label' => 'Customers']],
                        'admin.users.show' => [['label' => 'Sales & Customers'], ['label' => 'Customers', 'url' => route('admin.users.index')], ['label' => 'Customer Details']],
                        'admin.messages.index' => [['label' => 'Communication'], ['label' => 'Messages']],
                        'admin.messages.show' => [['label' => 'Communication'], ['label' => 'Messages', 'url' => route('admin.messages.index')], ['label' => 'Message Details']],
                        'admin.newsletter.index' => [['label' => 'Communication'], ['label' => 'Newsletter']],
                    ];
                    $breadcrumbs = $breadcrumbMap[$adminRoute] ?? [];
                @endphp
                <div class="admin-page-heading min-w-0">
                    <div class="sh-breadcrumbs">
                        <ol>
                            <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                            @foreach ($breadcrumbs as $crumb)
                                <li class="sh-breadcrumbs-sep" aria-hidden="true">/</li>
                                <li>
                                    @if(isset($crumb['url']))
                                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                                    @else
                                        <span>{{ $crumb['label'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </div>
                    <h1 class="text-truncate">@yield('page-title', 'Dashboard')</h1>
                </div>
            </div>
            <div class="dropdown">
                <button class="admin-user border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown" type="button">
                    <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                    <span class="d-none d-md-block text-start">
                        <strong class="d-block small">{{ auth()->user()->name }}</strong>
                        <small class="text-muted">Administrator</small>
                    </span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>My profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-shop me-2"></i>View store</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">@csrf
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <div class="admin-content">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>

<script>
(function () {
    const body = document.body;
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');
    const key = 'shoeHavenAdminSidebar';
    const tooltips = [];

    const isMobile = () => window.innerWidth < 992;

    if (!isMobile() && localStorage.getItem(key) === 'collapsed') body.classList.add('sidebar-collapsed');

    if (typeof bootstrap !== 'undefined' && sidebar) {
        sidebar.querySelectorAll('.nav-link[data-bs-title]').forEach(el => {
            const tip = new bootstrap.Tooltip(el, {
                placement: 'right',
                trigger: 'hover focus',
                title: el.getAttribute('data-bs-title'),
                customClass: 'sh-tooltip'
            });
            tip.disable();
            tooltips.push(tip);
        });
    }

    function syncTooltips() {
        const collapsed = body.classList.contains('sidebar-collapsed');
        tooltips.forEach(t => collapsed ? t.enable() : t.disable());
    }
    syncTooltips();

    function closeDrawer() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle?.addEventListener('click', function () {
        if (isMobile()) {
            const willOpen = !sidebar.classList.contains('open');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
            toggle.setAttribute('aria-expanded', String(willOpen));
            return;
        }
        body.classList.toggle('sidebar-collapsed');
        localStorage.setItem(key, body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded');
        syncTooltips();
    });

    overlay?.addEventListener('click', closeDrawer);
    sidebar?.querySelectorAll('.sh-nav .nav-link').forEach(link => {
        link.addEventListener('click', function () {
            if (isMobile()) closeDrawer();
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar?.classList.contains('open')) closeDrawer();
    });
})();
</script>
@stack('scripts')
</body>
</html>