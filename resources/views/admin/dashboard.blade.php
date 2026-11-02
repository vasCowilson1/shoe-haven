@extends('admin.layouts.admin')

@section('title', 'Dashboard — ' . config('app.name', 'Shoe Haven'))
@section('page-title', 'Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <p class="text-muted mb-0">A quick overview of your store.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.create') }}" class="btn btn-sh-orange btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark btn-sm"><i class="bi bi-bag me-1"></i>View Orders</a>
    </div>
</div>

<div class="row g-3 mb-4">
    @php
        $kpis = [
            ['label'=>'Revenue', 'value'=>'UGX '.number_format($total_revenue), 'icon'=>'bi-cash-stack', 'bg'=>'#eaf7ef', 'fg'=>'#16803c', 'url'=>null],
            ['label'=>'Orders', 'value'=>$total_orders, 'icon'=>'bi-bag-check', 'bg'=>'#eef4ff', 'fg'=>'#2563eb', 'url'=>route('admin.orders.index')],
            ['label'=>'Pending', 'value'=>$pending_orders, 'icon'=>'bi-clock-history', 'bg'=>'#fff5e8', 'fg'=>'#d97706', 'url'=>route('admin.orders.index', ['status'=>'pending'])],
            ['label'=>'Products', 'value'=>$total_products, 'icon'=>'bi-box-seam', 'bg'=>'#f2efff', 'fg'=>'#6d4aff', 'url'=>route('admin.products.index')],
            ['label'=>'Customers', 'value'=>$total_users, 'icon'=>'bi-people', 'bg'=>'#fff0f3', 'fg'=>'#db2777', 'url'=>route('admin.users.index')],
            ['label'=>'Low stock', 'value'=>$low_stock, 'icon'=>'bi-exclamation-triangle', 'bg'=>'#fff1f0', 'fg'=>'#dc2626', 'url'=>route('admin.inventory.index')],
        ];
    @endphp
    @foreach($kpis as $kpi)
        <div class="col-6 col-md-4 col-xl-2">
            @if($kpi['url'])<a href="{{ $kpi['url'] }}" class="text-decoration-none">@endif
            <div class="card sh-stat-card h-100 p-3">
                <div class="sh-stat-icon mb-3" style="background:{{ $kpi['bg'] }};color:{{ $kpi['fg'] }}"><i class="bi {{ $kpi['icon'] }}"></i></div>
                <div class="fw-bold fs-5 text-dark text-truncate">{{ $kpi['value'] }}</div>
                <small class="text-muted">{{ $kpi['label'] }}</small>
            </div>
            @if($kpi['url'])</a>@endif
        </div>
    @endforeach
</div>

@if($low_stock > 0)
<div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between gap-3 mb-4">
    <div><i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ $low_stock }} product(s) need attention.</strong> Check inventory before accepting more orders.</div>
    <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-dark">Review Inventory</a>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <a href="{{ route('admin.messages.index', ['unread' => 1]) }}" class="card admin-card h-100 p-3 text-decoration-none text-dark">
            <div class="d-flex align-items-center justify-content-between"><div><small class="text-muted">Unread messages</small><div class="fs-4 fw-bold">{{ $unread_messages }}</div></div><i class="bi bi-chat-left-text fs-3 text-warning"></i></div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="{{ route('admin.newsletter.index') }}" class="card admin-card h-100 p-3 text-decoration-none text-dark">
            <div class="d-flex align-items-center justify-content-between"><div><small class="text-muted">Active subscribers</small><div class="fs-4 fw-bold">{{ $active_subscribers }}</div></div><i class="bi bi-envelope-paper fs-3 text-primary"></i></div>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="card admin-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="mb-0 fw-bold">Order Status</h5><small class="text-muted">Current order pipeline</small></div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-dark">All orders</a>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-4">
                    @foreach($orders_by_status as $status=>$count)
                        <div class="col">
                            <a href="{{ route('admin.orders.index', ['status'=>$status]) }}" class="text-decoration-none">
                                <div class="p-2 rounded-3 text-center bg-light">
                                    <div class="fw-bold">{{ $count }}</div>
                                    <small class="text-muted text-capitalize">{{ $status }}</small>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <canvas id="ordersChart" height="105"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card admin-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="mb-0 fw-bold">Recent Orders</h5><small class="text-muted">Latest activity</small></div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-sh-orange">View all</a>
            </div>
            <div class="card-body p-0">
                @forelse($recent_orders as $order)
                    <a href="{{ route('admin.orders.show',$order) }}" class="d-flex align-items-center justify-content-between gap-3 p-3 text-decoration-none text-dark {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="min-w-0">
                            <div class="fw-semibold">Order #{{ $order->id }}</div>
                            <small class="text-muted text-truncate d-block">{{ $order->user?->name ?? 'Guest' }} · {{ $order->created_at->format('M d, Y') }}</small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold">UGX {{ number_format($order->total_amount) }}</div>
                            <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($order->status) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-muted py-5">No orders yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card admin-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><h5 class="mb-0 fw-bold">Recently Added Products</h5><small class="text-muted">Your latest catalog updates</small></div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-dark">Manage products</a>
    </div>
    <div class="table-responsive">
        <table class="table admin-table table-hover align-middle mb-0">
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Flags</th><th></th></tr></thead>
            <tbody>
            @forelse($recent_products as $product)
                @php $qty=$product->stock?->quantity ?? 0; @endphp
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><img class="admin-thumb" src="{{ Str::startsWith($product->image,'http') ? $product->image : asset($product->image) }}" alt=""><div><div class="fw-semibold">{{ $product->name }}</div><small class="text-muted">{{ $product->brand }}</small></div></div></td>
                    <td>{{ $product->category->name ?? '—' }}</td>
                    <td class="fw-semibold">UGX {{ number_format($product->price) }}</td>
                    <td><span class="badge bg-{{ $qty===0?'danger':($qty<=5?'warning text-dark':'success') }}">{{ $qty }}</span></td>
                    <td>@if($product->is_new)<span class="badge bg-success">New</span>@endif @if($product->discount)<span class="badge bg-danger">-{{ $product->discount }}%</span>@endif</td>
                    <td class="text-end"><a href="{{ route('admin.products.edit',$product) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No products yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('ordersChart');
    if (!el) return;
    new Chart(el, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($orders_by_status)) !!},
            datasets: [{ data: {!! json_encode(array_values($orders_by_status)) !!}, backgroundColor: ['#f59e0b','#0d6efd','#6f42c1','#198754','#dc3545'], borderRadius: 6 }]
        },
        options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{precision:0}}} }
    });
});
</script>
@endpush
