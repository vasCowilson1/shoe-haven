@extends('admin.layouts.admin')

@section('title', 'Messages — Admin')
@section('page-title', 'Messages')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h2 class="h5 fw-bold mb-1">Customer Messages</h2>
        <p class="text-muted small mb-0">Review contact enquiries and follow up with customers.</p>
    </div>
    <form action="{{ route('admin.messages.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
        <input type="search" name="search" class="form-control" placeholder="Name, email or subject..." value="{{ request('search') }}">
        <button class="btn btn-dark"><i class="bi bi-search"></i></button>
        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary">All</a>
        <a href="{{ route('admin.messages.index', ['unread' => 1]) }}" class="btn btn-outline-warning">Unread</a>
    </form>
</div>

<div class="card admin-card">
    <div class="table-responsive">
        <table class="table admin-table table-hover align-middle mb-0">
            <thead><tr><th>Sender</th><th>Subject</th><th>Message</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
            @forelse($messages as $message)
                <tr class="{{ !$message->is_read ? 'table-warning' : '' }}">
                    <td><div class="fw-semibold">{{ $message->name }}</div><small class="text-muted">{{ $message->email }}</small></td>
                    <td>{{ $message->subject ?: 'No subject' }}</td>
                    <td><span class="d-block text-truncate" style="max-width:340px">{{ $message->message }}</span></td>
                    <td>{!! $message->is_read ? '<span class="badge bg-secondary">Read</span>' : '<span class="badge bg-warning text-dark">Unread</span>' !!}</td>
                    <td><small class="text-muted">{{ $message->created_at->format('M d, Y H:i') }}</small></td>
                    <td class="text-end"><a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-eye"></i></a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No messages found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="d-flex justify-content-end mt-3">{{ $messages->links() }}</div>
@endsection
