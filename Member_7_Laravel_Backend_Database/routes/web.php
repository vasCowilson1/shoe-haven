<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HeroController as AdminHeroController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PreferencesController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PesapalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

/* Admin */

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/{product}/stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
    Route::get('/hero', [AdminHeroController::class, 'edit'])->name('hero.edit');
    Route::post('/hero', [AdminHeroController::class, 'update'])->name('hero.update');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/preferences', [PreferencesController::class, 'update'])->name('preferences.update');
    Route::post('/preferences/reset', [PreferencesController::class, 'reset'])->name('preferences.reset');
    Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/read', [ContactMessageController::class, 'markRead'])->name('messages.mark-read');
    Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
    Route::post('/newsletter/{subscriber}/toggle', [AdminNewsletterController::class, 'toggle'])->name('newsletter.toggle');
});

/*
|--------------------------------------------------------------------------
| Shop
|--------------------------------------------------------------------------
*/

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [ShopController::class, 'show'])->name('shop.show');

/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}/add', [CartController::class, 'add'])->name('cart.add')
    ->middleware('throttle:30,1');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')
        ->middleware('throttle:10,1');
});

/*
|--------------------------------------------------------------------------
| Orders
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->name('orders.')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/orders/{order}/confirmation', [CheckoutController::class, 'confirmation'])->name('confirmation');
});

/*
|--------------------------------------------------------------------------
| Pesapal Payment Gateway
|--------------------------------------------------------------------------
*/

Route::get('/pesapal/callback', [PesapalController::class, 'callback'])->name('pesapal.callback');
Route::match(['get', 'post'], '/pesapal/ipn', [PesapalController::class, 'ipn'])->name('pesapal.ipn');

Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}/pay', [PesapalController::class, 'retry'])->name('orders.pay');
});

/*
|--------------------------------------------------------------------------
| Wishlist
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle')
        ->middleware('throttle:30,1');
});

/*
|--------------------------------------------------------------------------
| Reviews
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/reviews/{product}', [ReviewController::class, 'store'])->name('reviews.store')
        ->middleware('throttle:10,1');
});

/*
|--------------------------------------------------------------------------
| Recently Viewed
|--------------------------------------------------------------------------
*/

Route::get('/recently-viewed', [ShopController::class, 'recentlyViewed'])->name('shop.recently-viewed');

/*
|--------------------------------------------------------------------------
| About, Contact, Help, Privacy, Terms
|--------------------------------------------------------------------------
*/

Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')
    ->middleware('throttle:5,1');
Route::get('/help', function () {
    return view('help.index');
})->name('help.index');
Route::get('/privacy', function () {
    return view('privacy.index');
})->name('privacy.index');
Route::get('/terms', function () {
    return view('terms.index');
})->name('terms.index');

/*
|--------------------------------------------------------------------------
| Newsletter
|--------------------------------------------------------------------------
*/

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe')
    ->middleware('throttle:5,1');

/*
|--------------------------------------------------------------------------
| Google Authentication
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

Route::get('/sitemap.xml', function () {
    $products = Product::with('category')->get();
    $categories = Category::all();

    return response()->view('sitemap', compact('products', 'categories'))->header('Content-Type', 'application/xml');
});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/details', [ProfileController::class, 'updateDetails'])->name('profile.update-details');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
