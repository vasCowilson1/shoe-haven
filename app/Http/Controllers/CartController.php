<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Show the cart page with full product details.
     */
    public function index()
    {
        $cart = $this->getCart();

        $products = Product::with('category')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->map(function ($product) use ($cart) {
                $product->cart_quantity = (int) ($cart[$product->id]['quantity'] ?? 0);
                $product->cart_size = $cart[$product->id]['size'] ?? null;
                $product->line_total = $product->price * $product->cart_quantity;

                return $product;
            });

        $total = $products->sum('line_total');

        return view('cart.index', [
            'products' => $products,
            'total' => $total,
        ]);
    }

    /**
     * Add a product to the cart (or increase its quantity).
     */
    public function add(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity', 1);
        $size = $request->filled('size') ? (string) $request->input('size') : null;

        if ($quantity < 1 || $quantity > 99) {
            throw ValidationException::withMessages(['quantity' => 'Quantity must be between 1 and 99.']);
        }

        $product->load('stock', 'sizeStock');
        $available = $product->stock?->quantity ?? 0;

        if ($product->sizeStock->isNotEmpty()) {
            if ($size === null) {
                $defaultSizeStock = $product->sizeStock->where('quantity', '>', 0)->sortBy('size')->first();
                if ($defaultSizeStock) {
                    $size = (string) $defaultSizeStock->size;
                } else {
                    throw ValidationException::withMessages(['size' => 'This product is currently out of stock.']);
                }
            }

            $sizeStock = $product->sizeStock->firstWhere('size', $size);
            if (! $sizeStock) {
                throw ValidationException::withMessages(['size' => 'The selected size is not available.']);
            }

            $available = min($available, (int) $sizeStock->quantity);
        } elseif ($size !== null) {
            throw ValidationException::withMessages(['size' => 'This product does not use size selection.']);
        }

        $cart = $this->getCart();

        if (isset($cart[$product->id])) {
            $existingSize = $cart[$product->id]['size'] ?? null;
            if ((string) $existingSize !== (string) $size) {
                $newQuantity = $quantity;
            } else {
                $newQuantity = (int) $cart[$product->id]['quantity'] + $quantity;
            }
        } else {
            $newQuantity = $quantity;
        }

        if ($newQuantity > $available) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$available} unit(s) of {$product->name}".($size ? " in size {$size}" : '').' are currently available.',
            ]);
        }

        $this->setCartItem($product->id, $newQuantity, $size);

        $cart = $this->getCart();
        $msg = $size ? "{$product->name} (Size {$size}) added to cart" : "{$product->name} added to cart";

        return back()->with('cart_toast', [
            'message' => $msg,
            'count' => collect($cart)->sum('quantity'),
        ]);
    }

    /**
     * Update the quantity of a product already in the cart.
     */
    public function update(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity < 1 || $quantity > 99) {
            throw ValidationException::withMessages(['quantity' => 'Quantity must be between 1 and 99.']);
        }

        $cart = $this->getCart();
        if (! isset($cart[$product->id])) {
            return back()->with('cart_toast', [
                'message' => 'That product is no longer in your cart.',
                'count' => collect($cart)->sum('quantity'),
            ]);
        }

        $product->load('stock', 'sizeStock');
        $size = $cart[$product->id]['size'] ?? null;
        $available = $product->stock?->quantity ?? 0;

        if ($product->sizeStock->isNotEmpty()) {
            $sizeStock = $product->sizeStock->firstWhere('size', (string) $size);
            if (! $sizeStock) {
                $this->removeCartItem($product->id);
                throw ValidationException::withMessages(['size' => 'The selected size is no longer available.']);
            }
            $available = min($available, (int) $sizeStock->quantity);
        }

        if ($quantity > $available) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$available} unit(s) are currently available.",
            ]);
        }

        $this->setCartItem($product->id, $quantity, $size);

        return back()->with('cart_toast', [
            'message' => 'Cart updated',
            'count' => $this->getItemCount(),
        ]);
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(Product $product)
    {
        $this->removeCartItem($product->id);

        return back()->with('cart_toast', [
            'message' => "{$product->name} removed from cart",
            'count' => $this->getItemCount(),
        ]);
    }

    /**
     * Total number of items in the cart (for the navbar badge).
     */
    public static function itemCount(): int
    {
        if (Auth::check()) {
            $cart = Cart::forUser(Auth::id());

            return $cart->itemCount();
        }

        return collect(Session::get('cart', []))->sum('quantity');
    }

    // -------------------------------------------------
    // Private helpers — abstract the storage layer
    // -------------------------------------------------

    /**
     * Get the current cart as [productId => ['quantity' => n, 'size' => '...']].
     */
    private function getCart(): array
    {
        $sessionCart = Session::get('cart', []);
        if (! empty($sessionCart)) {
            return $sessionCart;
        }

        if (Auth::check()) {
            $cart = Cart::forUser(Auth::id());
            $dbFormat = $cart->toSessionFormat();
            if (! empty($dbFormat)) {
                return $dbFormat;
            }
        }

        return [];
    }

    /**
     * Set (or update) a single cart item.
     */
    private function setCartItem(int $productId, int $quantity, ?string $size): void
    {
        if (Auth::check()) {
            $cart = Cart::forUser(Auth::id());
            CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->where('size', '!=', $size)
                ->delete();

            CartItem::updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $productId, 'size' => $size],
                ['quantity' => $quantity]
            );

            return;
        }

        $sessionCart = Session::get('cart', []);
        $sessionCart[$productId] = ['quantity' => $quantity, 'size' => $size];
        Session::put('cart', $sessionCart);
    }

    /**
     * Remove a single product from the cart.
     */
    private function removeCartItem(int $productId): void
    {
        if (Auth::check()) {
            $cart = Cart::forUser(Auth::id());
            CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->delete();

            return;
        }

        $cart = Session::get('cart', []);
        unset($cart[$productId]);
        Session::put('cart', $cart);
    }

    /**
     * Get total item count for the current user/guest.
     */
    private function getItemCount(): int
    {
        return collect($this->getCart())->sum('quantity');
    }
}
