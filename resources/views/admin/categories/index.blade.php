@extends('admin.layouts.admin')

@section('title', 'Categories — Admin')
@section('page-title', 'Categories')

@section('content')
<div class="row g-4">
    <div class="col-xl-4">
        <div class="card admin-card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Add Category</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Sneakers" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="slug" class="form-control" placeholder="sneakers">
                    </div>
                    <button class="btn btn-sh-orange w-100"><i class="bi bi-check-lg me-1"></i>Create Category</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">Product Categories</h5>
                    <small class="text-muted">{{ $categories->count() }} categories</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table admin-table table-hover align-middle mb-0">
                    <thead>
                        <tr><th>Name</th><th>Slug</th><th>Products</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td><span class="badge bg-light text-dark">{{ $category->products_count }}</span></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editCategory{{ $category->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" {{ $category->products_count ? 'disabled title=Category has products' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editCategory{{ $category->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Category</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label fw-semibold">Name</label>
                                            <input name="name" class="form-control mb-3" value="{{ $category->name }}" required>
                                            <label class="form-label fw-semibold">Slug</label>
                                            <input name="slug" class="form-control" value="{{ $category->slug }}">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-sh-orange">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-5">No categories have been created yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
