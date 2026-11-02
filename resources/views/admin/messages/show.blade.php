@extends('admin.layouts.admin')

@section('title', 'Message — Admin')
@section('page-title', 'Message Details')

@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <div class="card admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="mb-0 fw-bold">{{ $message->subject ?: 'Customer enquiry' }}</h5><small class="text-muted">Received {{ $message->created_at->format('M d, Y H:i') }}</small></div>
                <span class="badge bg-secondary">Read</span>
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space:pre-wrap">{{ $message->message }}</p>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card admin-card">
            <div class="card-header"><h5 class="mb-0 fw-bold">Sender</h5></div>
            <div class="card-body">
                <div class="fw-semibold">{{ $message->name }}</div>
                <a href="mailto:{{ $message->email }}" class="small">{{ $message->email }}</a>
                @if($message->user)<div class="small text-muted mt-2">Registered customer</div>@endif
                <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary btn-sm mt-4">Back to messages</a>
            </div>
        </div>
    </div>
</div>
@endsection
