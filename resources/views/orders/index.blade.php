@extends('layouts.public')

@section('title', 'My Orders — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <h2 class="fw-bold mb-0">My Orders</h2>
    </div>

    @if ($orders->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3" style="font-size: 4rem; opacity: 0.2;">
                <i class="bi bi-bag-x"></i>
            </div>
            <h5 class="fw-bold">No orders yet</h5>
            <p class="text-muted mb-4">When you place an order, it will appear here.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-sh-orange btn-lg">Start Shopping</a>
        </div>
    @else
        <div class="row g-4">
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">
                    <div class="card bg-dark border-0 mb-3 sh-order-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold">#{{ $order->id }}</span>
                                        <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <small class="text-white-50">{{ $order->created_at->format('M d, Y \a\t h:i A') }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: var(--sh-orange);">UGX {{ number_format($order->total_amount) }}</div>
                                    <small class="text-white-50">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</small>
                                </div>
                                <div>
                                    <span class="btn btn-sm btn-outline-light">View Details <i class="bi bi-chevron-right"></i></span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3 overflow-auto">
                                @foreach ($order->items->take(4) as $item)
                                    <div style="width: 48px; height: 48px; flex-shrink: 0;">
                                        <img src="{{ Str::startsWith($item->product?->image ?? '', 'http') ? $item->product->image : asset($item->product?->image ?? '') }}"
                                             alt="" class="w-100 h-100 rounded-2"
                                             style="object-fit: cover;">
                                    </div>
                                @endforeach
                                @if ($order->items->count() > 4)
                                    <div class="d-flex align-items-center justify-content-center text-white-50 small fw-bold"
                                         style="width: 48px; height: 48px; flex-shrink: 0; background: rgba(255,255,255,0.05); border-radius: 6px;">
                                        +{{ $order->items->count() - 4 }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
