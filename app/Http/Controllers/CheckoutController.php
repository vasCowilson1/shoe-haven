<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\InventoryHistory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\PesapalService;
use App\Support\DeliveryLocations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        protected ?PesapalService $pesapalService = null
    ) {
        $this->pesapalService ??= app(PesapalService::class);
    }

    public function index()
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $total = $products->sum(fn ($p) => $p->price * $cart[$p->id]['quantity']);

        $profile = Auth::user()->profile;
        $deliveryLocations = DeliveryLocations::data();

        return view('checkout.index', compact('products', 'cart', 'total', 'profile', 'deliveryLocations'));
    }

    public function store(Request $request)
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:32',
            'region' => 'required|string|max:64',
            'district' => 'required|string|max:128',
            'area' => 'required|string|max:128',
            'landmark' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'save_location' => 'nullable|boolean',
            'payment_method' => 'nullable|string',
        ]);

        if (! DeliveryLocations::isValid($data['region'], $data['district'], $data['area'])) {
            return back()->withErrors(['area' => 'Please select a valid delivery location.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $productIds = array_keys($cart);
            $products = Product::with(['stock', 'sizeStock'])->whereIn('id', $productIds)->lockForUpdate()->get();

            if ($products->count() !== count($productIds)) {
                throw new \Exception('One or more products in your cart are no longer available.');
            }

            $total = $products->sum(fn ($p) => $p->price * (int) ($cart[$p->id]['quantity'] ?? 0));

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'payment_method' => 'Pesapal',
                'payment_phone' => $data['phone'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
                'payment_initiated_at' => now(),
                'shipping_name' => $data['name'],
                'shipping_address' => $data['address'] ?? null,
                'shipping_city' => $data['area'],
                'shipping_region' => $data['region'],
                'shipping_district' => $data['district'],
                'shipping_area' => $data['area'],
                'shipping_landmark' => $data['landmark'],
                'shipping_phone' => $data['phone'],
            ]);

            foreach ($products as $product) {
                $qty = $cart[$product->id]['quantity'];
                $size = $cart[$product->id]['size'] ?? null;

                $stock = $product->stock()->lockForUpdate()->first();
                if (! $stock) {
                    throw new \Exception("No stock record for {$product->name}.");
                }

                if ($size) {
                    $sizeStock = DB::table('product_size_stock')
                        ->where('product_id', $product->id)
                        ->where('size', (string) $size)
                        ->lockForUpdate()
                        ->first();
                    if (! $sizeStock || $sizeStock->quantity < $qty) {
                        throw new \Exception("Insufficient stock for {$product->name} (Size {$size}).");
                    }
                }

                if ($stock->quantity < $qty) {
                    throw new \Exception("Insufficient stock for {$product->name}.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'size' => $size,
                    'quantity' => $qty,
                    'price_at_sale' => $product->price,
                ]);

                $previous = $stock->quantity;

                if ($size) {
                    DB::table('product_size_stock')
                        ->where('product_id', $product->id)
                        ->where('size', (string) $size)
                        ->decrement('quantity', $qty);
                }

                $stock->decrement('quantity', $qty);

                InventoryHistory::create([
                    'product_id' => $product->id,
                    'previous_quantity' => $previous,
                    'new_quantity' => $previous - $qty,
                    'change_amount' => -$qty,
                    'change_type' => 'sale',
                    'notes' => 'Order #'.$order->id,
                    'changed_by' => Auth::id(),
                ]);
            }

            if ($request->boolean('save_location')) {
                $profile = Auth::user()->profile ?? Auth::user()->profile()->create(['user_id' => Auth::id()]);
                $profile->update([
                    'full_name' => $data['name'],
                    'phone' => $data['phone'],
                    'delivery_region' => $data['region'],
                    'delivery_district' => $data['district'],
                    'delivery_area' => $data['area'],
                    'delivery_landmark' => $data['landmark'],
                ]);
            }

            $pesapalResult = $this->pesapalService->submitOrder($order, [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => Auth::user()->email,
                'region' => $data['region'],
                'district' => $data['district'],
                'area' => $data['area'],
                'landmark' => $data['landmark'],
                'address' => $data['address'] ?? null,
            ]);

            $order->update([
                'pesapal_tracking_id' => $pesapalResult['order_tracking_id'] ?? null,
            ]);

            Session::forget('cart');
            if (Auth::check()) {
                Cart::forUser(Auth::id())->items()->delete();
            }
            DB::commit();

            return redirect()->away($pesapalResult['redirect_url']);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function confirmation(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('orders.confirmation', compact('order'));
    }

    /**
     * Get the current cart as [productId => ['quantity' => n, 'size' => '...']].
     */
    protected function getCart(): array
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
}
