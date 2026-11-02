@extends('admin.layouts.admin')

@section('title', 'Newsletter — Admin')
@section('page-title', 'Newsletter')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h2 class="h5 fw-bold mb-1">Newsletter Subscribers</h2>
        <p class="text-muted small mb-0">Manage the people subscribed to store updates and promotions.</p>
    </div>
    <form action="{{ route('admin.newsletter.index') }}" method="GET" class="d-flex gap-2">
        <input type="search" name="search" class="form-control" placeholder="Search email..." value="{{ request('search') }}">
        <button class="btn btn-dark"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card admin-card">
    <div class="table-responsive">
        <table class="table admin-table table-hover align-middle mb-0">
            <thead><tr><th>Email</th><th>Status</th><th>Subscribed</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($subscribers as $subscriber)
                <tr>
                    <td class="fw-semibold">{{ $subscriber->email }}</td>
                    <td><span class="badge bg-{{ $subscriber->is_active ? 'success' : 'secondary' }}">{{ $subscriber->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td><small class="text-muted">{{ $subscriber->created_at->format('M d, Y') }}</small></td>
                    <td class="text-end">
                        <form action="{{ route('admin.newsletter.toggle', $subscriber) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-dark">{{ $subscriber->is_active ? 'Deactivate' : 'Activate' }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-5">No subscribers found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="d-flex justify-content-end mt-3">{{ $subscribers->links() }}</div>
@endsection
