@extends('admin.layouts.admin')

@section('title', 'Customers — Admin')
@section('page-title', 'Customers')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><h2 class="h5 fw-bold mb-1">Customer Accounts</h2><p class="text-muted small mb-0">View customer profiles and their order history.</p></div>
    <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
        <input type="search" name="search" class="form-control" placeholder="Name or email..." value="{{ request('search') }}">
        <button class="btn btn-dark"><i class="bi bi-search"></i></button>
        @if(request('search'))<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Clear</a>@endif
    </form>
</div>

<div class="card admin-card">
<div class="card-header d-flex justify-content-between align-items-center"><strong>{{ $users->total() }} registered users</strong><span class="small text-muted">Newest first</span></div>
<div class="table-responsive">
<table class="table admin-table table-hover align-middle mb-0">
<thead><tr><th>User</th><th>Email</th><th>Role</th><th>Orders</th><th>Joined</th><th class="text-end">Action</th></tr></thead>
<tbody>
@forelse($users as $user)
<tr>
<td><div class="d-flex align-items-center gap-3"><div class="admin-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div><div><div class="fw-semibold">{{ $user->name }}</div>@if($user->profile?->phone)<small class="text-muted">{{ $user->profile->phone }}</small>@endif</div></div></td>
<td>{{ $user->email }}</td>
<td>@if($user->isAdmin())<span class="badge bg-dark"><i class="bi bi-shield-check me-1"></i>Admin</span>@else<span class="badge bg-light text-dark">Customer</span>@endif</td>
<td><span class="fw-semibold">{{ $user->orders_count }}</span></td>
<td><small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small></td>
<td class="text-end"><a href="{{ route('admin.users.show',$user) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-eye me-1"></i>View</a></td>
</tr>
@empty<tr><td colspan="6" class="text-center text-muted py-5">No users found.</td></tr>@endforelse
</tbody>
</table>
</div>
</div>
<div class="d-flex justify-content-end mt-3">{{ $users->links() }}</div>
@endsection
