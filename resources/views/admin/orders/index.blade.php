@extends('admin.layouts.admin')

@section('title', 'Orders — Admin')
@section('page-title', 'Orders')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><h2 class="h5 fw-bold mb-1">Order Management</h2><p class="text-muted small mb-0">Track payments, fulfilment and delivery status.</p></div>
    <form action="{{ route('admin.orders.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <input type="search" name="search" class="form-control" placeholder="Order #, name or email..." value="{{ request('search') }}">
        <button class="btn btn-dark"><i class="bi bi-search"></i></button>
        @if(request('search'))<a href="{{ route('admin.orders.index',['status'=>request('status')]) }}" class="btn btn-outline-secondary">Clear</a>@endif
    </form>
</div>

<div class="d-flex gap-2 overflow-auto pb-2 mb-3">
@foreach([''=>'All','pending'=>'Pending','confirmed'=>'Confirmed','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $value=>$label)
<a href="{{ route('admin.orders.index', array_filter(['status'=>$value,'search'=>request('search')])) }}" class="btn btn-sm text-nowrap {{ request('status','')===$value ? 'btn-dark':'btn-outline-secondary' }}">{{ $label }}</a>
@endforeach
</div>

<div class="card admin-card">
<div class="table-responsive">
<table class="table admin-table table-hover align-middle mb-0">
<thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th></th></tr></thead>
<tbody>
@forelse($orders as $order)
@php $statusColors=['pending'=>'warning','confirmed'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; $color=$statusColors[$order->status]??'secondary'; @endphp
<tr>
<td class="fw-bold">#{{ $order->id }}</td>
<td><div class="fw-semibold">{{ $order->user?->name ?? 'Guest' }}</div><small class="text-muted">{{ $order->user?->email ?? $order->shipping_phone ?? '—' }}</small></td>
<td>{{ $order->items->count() }}</td>
<td class="fw-bold">UGX {{ number_format($order->total_amount) }}</td>
<td><span class="badge bg-{{ $order->payment_status==='paid'?'success':'warning' }}">{{ ucfirst($order->payment_status) }}</span><small class="d-block text-muted mt-1">{{ $order->payment_method }}</small></td>
<td><span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span></td>
<td><small class="text-muted">{{ $order->created_at->format('M d, Y') }}<br>{{ $order->created_at->format('H:i') }}</small></td>
<td class="text-end"><a href="{{ route('admin.orders.show',$order) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-eye me-1"></i>View</a></td>
</tr>
@empty<tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2"></i><p class="mb-0 mt-2">No orders found.</p></td></tr>@endforelse
</tbody>
</table>
</div>
</div>
<div class="d-flex justify-content-end mt-3">{{ $orders->links() }}</div>
@endsection
