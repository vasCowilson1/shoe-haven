<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AboutController extends Controller
{
    public function index()
    {
        $productCount = Product::count();
        $categoryCount = Category::count();
        $userCount = User::count();
        $orderCount = Order::count();

        return view('about.index', compact(
            'productCount', 'categoryCount', 'userCount', 'orderCount'
        ));
    }
}
