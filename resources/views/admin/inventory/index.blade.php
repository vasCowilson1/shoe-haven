@extends('admin.layouts.admin')

@section('title', 'Inventory — Admin')
@section('page-title', 'Inventory')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><h2 class="h5 fw-bold mb-1">Inventory Control</h2><p class="text-muted small mb-0">Monitor stock levels and keep your catalog available.</p></div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-sh-orange"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card admin-card p-3"><small class="text-muted">Total products</small><div class="fs-4 fw-bold">{{ $products->count() }}</div></div></div>
    <div class="col-md-4"><div class="card admin-card p-3"><small class="text-muted">Low stock (≤ {{ $lowStockThreshold }})</small><div class="fs-4 fw-bold text-warning">{{ $lowStock->count() }}</div></div></div>
    <div class="col-md-4"><div class="card admin-card p-3"><small class="text-muted">Out of stock</small><div class="fs-4 fw-bold text-danger">{{ $outOfStock->count() }}</div></div></div>
</div>

@if($outOfStock->isNotEmpty())
<div class="alert alert-danger border-0 shadow-sm"><strong><i class="bi bi-x-circle me-2"></i>Out of stock:</strong> {{ $outOfStock->pluck('name')->join(', ') }}</div>
@endif

<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#stockTab"><i class="bi bi-box-seam me-1"></i>Stock Levels</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#historyTab"><i class="bi bi-clock-history me-1"></i>Adjustment History</button></li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="stockTab">
<div class="card admin-card">
<div class="table-responsive">
<table class="table admin-table table-hover align-middle mb-0">
<thead><tr><th>Product</th><th>Category</th><th>Current stock</th><th>Status</th><th class="text-end">Adjust quantity</th></tr></thead>
<tbody>
@forelse($products as $product)
@php $qty=$product->stock?->quantity ?? 0; @endphp
<tr>
<td><div class="d-flex align-items-center gap-3"><img class="admin-thumb" src="{{ Str::startsWith($product->image,'http') ? $product->image : asset($product->image) }}" alt=""><div><div class="fw-semibold">{{ $product->name }}</div><small class="text-muted">{{ $product->brand }}</small></div></div></td>
<td>{{ $product->category->name ?? '—' }}</td>
<td class="fw-bold">{{ $qty }}</td>
<td><span class="badge bg-{{ $qty===0?'danger':($qty<=$lowStockThreshold?'warning text-dark':'success') }}">{{ $qty===0?'Out of stock':($qty<=$lowStockThreshold?'Low stock':'In stock') }}</span></td>
<td class="text-end">
<form action="{{ route('admin.inventory.update-stock',$product) }}" method="POST" class="d-inline-flex gap-2">
@csrf
<input type="number" name="quantity" value="{{ $qty }}" min="0" class="form-control form-control-sm" style="width:90px" required>
<button class="btn btn-sm btn-outline-dark">Save</button>
<a href="{{ route('admin.products.edit',$product) }}" class="btn btn-sm btn-outline-secondary" title="Edit product"><i class="bi bi-pencil"></i></a>
</form>
</td>
</tr>
@empty
<tr><td colspan="5" class="text-center text-muted py-5">No products found.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>

<div class="tab-pane fade" id="historyTab">
<div class="card admin-card">
<div class="table-responsive">
<table class="table admin-table table-hover align-middle mb-0">
<thead><tr><th>Product</th><th>Previous</th><th>New</th><th>Change</th><th>Type</th><th>Date</th></tr></thead>
<tbody>
@forelse($history as $entry)
<tr>
<td class="fw-semibold">{{ $entry->product?->name ?? 'Deleted product' }}</td>
<td>{{ $entry->previous_quantity }}</td><td>{{ $entry->new_quantity }}</td>
<td class="fw-bold {{ $entry->change_amount > 0 ? 'text-success' : ($entry->change_amount < 0 ? 'text-danger' : 'text-muted') }}">{{ $entry->change_amount > 0 ? '+' : '' }}{{ $entry->change_amount }}</td>
<td><span class="badge bg-light text-dark text-capitalize">{{ $entry->change_type }}</span></td>
<td><small class="text-muted">{{ $entry->created_at->format('M d, Y H:i') }}</small></td>
</tr>
@empty<tr><td colspan="6" class="text-center text-muted py-5">No stock adjustments recorded.</td></tr>@endforelse
</tbody>
</table>
</div>
</div>
</div>
</div>
@endsection
