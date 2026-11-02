@extends('admin.layouts.admin')

@section('title', 'Products — ' . config('app.name', 'Shoe Haven'))
@section('page-title', 'Products')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="h5 fw-bold mb-1">Product Catalog</h2>
        <p class="text-muted small mb-0">Create, edit and manage every shoe in your store.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-sh-orange"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
</div>

<div class="card admin-card">
    <div class="card-header">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" class="form-control" placeholder="Search product name or brand..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <span class="text-muted small">{{ $products->total() }} products</span>
            </div>
            @if(request('search'))
                <div class="col-auto"><a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a></div>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table admin-table table-hover align-middle mb-0">
            <thead>
            <tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                @php $qty=$product->stock?->quantity ?? 0; @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img class="admin-thumb" src="{{ Str::startsWith($product->image,'http') ? $product->image : asset($product->image) }}" alt="{{ $product->name }}" onerror="this.src='{{ asset('images/placeholder.svg') }}'">
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate" style="max-width:260px">{{ $product->name }}</div>
                                <small class="text-muted">{{ $product->brand }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $product->category->name ?? '—' }}</td>
                    <td><div class="fw-semibold">UGX {{ number_format($product->price) }}</div>@if($product->original_price)<small class="text-muted text-decoration-line-through">UGX {{ number_format($product->original_price) }}</small>@endif</td>
                    <td><span class="badge bg-{{ $qty===0?'danger':($qty<=5?'warning text-dark':'success') }}">{{ $qty }} units</span></td>
                    <td>
                        @if($product->is_new)<span class="badge bg-success">New</span>@endif
                        @if($product->discount)<span class="badge bg-danger">-{{ $product->discount }}%</span>@endif
                        @if(!$product->is_new && !$product->discount)<span class="text-muted small">Active</span>@endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('admin.products.edit',$product) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.products.destroy',$product) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5"><i class="bi bi-box-seam fs-2 text-muted"></i><p class="text-muted mt-2 mb-0">No products found.</p></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }}</small>
    {{ $products->links() }}
</div>
@endsection
