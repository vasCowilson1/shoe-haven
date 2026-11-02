<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalRevenue = (float) Order::whereNotIn('status', ['cancelled'])->sum('total_amount');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalProducts = Product::count();

        $totalUsers = User::whereDoesntHave('roles', fn ($q) => $q->where('role', 'admin'))->count();

        $lowStock = Product::whereHas('stock', fn ($q) => $q->where('quantity', '>', 0)->where('quantity', '<=', 5))
            ->orWhereDoesntHave('stock')
            ->count();

        $unreadMessages = ContactMessage::where('is_read', false)->count();
        $activeSubscribers = NewsletterSubscriber::where('is_active', true)->count();

        $statuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as aggregate'))
            ->whereIn('status', $statuses)
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $ordersByStatus = collect($statuses)->mapWithKeys(
            fn ($status) => [$status => (int) ($statusCounts[$status] ?? 0)]
        )->all();

        $recentOrders = Order::with('user')->latest('id')->take(8)->get();
        $recentProducts = Product::with(['category', 'stock'])->latest('id')->take(8)->get();

        return view('admin.dashboard', [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_products' => $totalProducts,
            'total_users' => $totalUsers,
            'low_stock' => $lowStock,
            'unread_messages' => $unreadMessages,
            'active_subscribers' => $activeSubscribers,
            'orders_by_status' => $ordersByStatus,
            'recent_orders' => $recentOrders,
            'recent_products' => $recentProducts,
        ]);
    }
}
