<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->whereIn('id', Wishlist::where('user_id', Auth::id())->pluck('product_id'))
            ->get();

        return view('wishlist.index', compact('products'));
    }

    public function toggle(Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ]);
        }

        return redirect()->back();
    }
}
