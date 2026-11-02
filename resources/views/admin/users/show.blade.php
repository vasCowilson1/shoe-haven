@extends('admin.layouts.admin')

@section('title', "{$user->name} — Admin")
@section('page-title', $user->name)

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                     style="width: 80px; height: 80px; font-size: 1.8rem; {{ $user->isAdmin() ? 'background: var(--sh-orange);' : 'background: #6c757d;' }}">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <small class="text-muted">{{ $user->email }}</small>
                <div class="mt-2">
                    @if ($user->isAdmin())
                        <span class="badge bg-dark fs-6"><i class="bi bi-shield-fill-check me-1"></i>Admin</span>
                    @else
                        <span class="badge bg-secondary fs-6">Customer</span>
                    @endif
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Joined</span>
                    <span class="fw-medium">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Orders</span>
                    <span class="fw-medium">{{ $user->orders->count() }}</span>
                </div>
                @if ($user->profile && $user->profile->phone)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phone</span>
                        <span class="fw-medium">{{ $user->profile->phone }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-bag me-2"></i>Orders ({{ $user->orders->count() }})</h5>
            </div>
            <div class="card-body p-0">
                @if ($user->orders->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size: 2.5rem; opacity: 0.3;"></i>
                        <p class="mt-2 mb-0">No orders placed yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->orders as $order)
                                    <tr>
                                        <td class="fw-bold">#{{ $order->id }}</td>
                                        <td>{{ $order->items->count() }}</td>
                                        <td class="fw-medium">UGX {{ number_format($order->total_amount) }}</td>
                                        <td><small class="text-muted">{{ ucfirst($order->payment_method) }}</small></td>
                                        <td>
                                            @php
                                                $statusColors = ['pending' => 'warning', 'confirmed' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
                                                $color = $statusColors[$order->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                        <td><small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small></td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-dark">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection