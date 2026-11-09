<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $cleaned = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $pattern = '%' . strtolower($cleaned) . '%';
            $query->where(function ($q) use ($search, $pattern) {
                if (is_numeric($search)) {
                    $q->where('id', (int) $search);
                }
                $q->orWhereRaw('LOWER(transaction_id) LIKE ?', [$pattern])
                  ->orWhere('payment_phone', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%")
                  ->orWhereRaw('LOWER(shipping_name) LIKE ?', [$pattern])
                  ->orWhereHas('user', function ($u) use ($pattern) {
                      $u->whereRaw('LOWER(name) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$pattern]);
                  });
            });
        }

        $orders = $query->latest('id')->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $data['status']]);

        return redirect()->route('admin.orders.index')
            ->with('status', "Order #{$order->id} status updated to {$data['status']}.");
    }
}
