<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use App\Models\CartItem;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SyncSessionCartToDatabase
{
    /**
     * If an authenticated user has items in the session cart (e.g. from before login),
     * merge them into the database cart and clear the session copy.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && Session::has('cart')) {
            $sessionCart = Session::get('cart', []);

            if (! empty($sessionCart)) {
                $dbCart = Cart::forUser(Auth::id());

                foreach ($sessionCart as $productId => $item) {
                    $quantity = $item['quantity'] ?? 1;
                    $size = $item['size'] ?? null;

                    $existing = CartItem::where('cart_id', $dbCart->id)
                        ->where('product_id', $productId)
                        ->where('size', $size)
                        ->first();

                    if ($existing) {
                        $existing->update(['quantity' => $existing->quantity + $quantity]);
                    } else {
                        CartItem::create([
                            'cart_id' => $dbCart->id,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                            'size' => $size,
                        ]);
                    }
                }

                Session::forget('cart');
            }
        }

        return $response;
    }
}
