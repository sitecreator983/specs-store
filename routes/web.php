<?php

use App\Http\Controllers\AccountOrderController;
use App\Http\Controllers\AdminBrandController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeProductController;
use App\Http\Controllers\OfferProductController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PcBuilderController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SavedPcBuildController;
use App\Http\Controllers\SearchProductController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('home');
})->name('login');

Route::get('/forgot-password', function () {
    return view('home');
})->name('password.request');

Route::get('/register', function () {
    return view('home');
})->name('register');

Route::get('/cart', function () {
    return view('home');
})->name('cart');

Route::get('/wishlist', function () {
    return view('home');
})->name('wishlist');

Route::get('/offers', function () {
    return view('home');
})->name('offers');

Route::get('/search', function () {
    return view('home');
})->name('search');

Route::get('/pc-builder', function () {
    return view('home');
})->name('pc-builder');

Route::get('/checkout', function () {
    return view('home');
})->name('checkout');

Route::get('/checkout/card', function () {
    return view('home');
})->name('checkout.card');

Route::get('/checkout/success/{trackingCode}', function () {
    return view('home');
})->name('checkout.success');

Route::get('/track-order', function () {
    return view('home');
})->name('orders.track');

Route::get('/account/orders', function () {
    return view('home');
})->middleware('auth')->name('account.orders');

Route::get('/admin/products', function () {
    abort_unless(auth()->user()?->is_admin, 403);

    return view('home');
})->middleware('auth')->name('admin.products');

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/categories/processors', function () {
    return view('home');
})->name('categories.processors');

Route::get('/categories/graphics-cards', function () {
    return view('home');
})->name('categories.graphics-cards');

Route::get('/categories/storage', function () {
    return view('home');
})->name('categories.storage');

Route::get('/categories/cases', function () {
    return view('home');
})->name('categories.cases');

Route::get('/categories/ram', function () {
    return view('home');
})->name('categories.ram');

Route::get('/categories/motherboards', function () {
    return view('home');
})->name('categories.motherboards');

Route::get('/categories/power-supplies', function () {
    return view('home');
})->name('categories.power-supplies');

Route::get('/categories/fans', function () {
    return view('home');
})->name('categories.fans');

Route::get('/categories/external-drives', function () {
    return view('home');
})->name('categories.external-drives');

Route::get('/categories/ssd-hdd', function () {
    return view('home');
})->name('categories.ssd-hdd');

Route::get('/categories/usb-sticks', function () {
    return view('home');
})->name('categories.usb-sticks');

Route::get('/categories/memory-cards', function () {
    return view('home');
})->name('categories.memory-cards');

Route::get('/categories/drive-enclosures', function () {
    return view('home');
})->name('categories.drive-enclosures');

Route::get('/categories/cd-dvd', function () {
    return view('home');
})->name('categories.cd-dvd');

Route::get('/categories/keyboards', function () {
    return view('home');
})->name('categories.keyboards');

Route::get('/categories/headsets', function () {
    return view('home');
})->name('categories.headsets');

Route::get('/categories/monitors', function () {
    return view('home');
})->name('categories.monitors');

Route::get('/categories/mice', function () {
    return view('home');
})->name('categories.mice');

Route::get('/categories/speakers', function () {
    return view('home');
})->name('categories.speakers');

Route::get('/categories/joysticks', function () {
    return view('home');
})->name('categories.joysticks');

Route::get('/api/categories/{category:slug}/products', CategoryProductController::class)->name('categories.products.index');
Route::get('/api/pc-builder/products', PcBuilderController::class)->name('pc-builder.products.index');
Route::get('/api/pc-builder/builds', [SavedPcBuildController::class, 'index'])->middleware('auth')->name('pc-builder.builds.index');
Route::post('/api/pc-builder/builds', [SavedPcBuildController::class, 'store'])->middleware('auth')->name('pc-builder.builds.store');
Route::put('/api/pc-builder/builds/{savedPcBuild}', [SavedPcBuildController::class, 'update'])->middleware('auth')->name('pc-builder.builds.update');
Route::delete('/api/pc-builder/builds/{savedPcBuild}', [SavedPcBuildController::class, 'destroy'])->middleware('auth')->name('pc-builder.builds.destroy');
Route::get('/api/offers', OfferProductController::class)->name('offers.products.index');
Route::get('/api/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/api/cart/validate-stock', [CartController::class, 'validateStock'])->name('cart.validate-stock');
Route::post('/api/cart', [CartController::class, 'store'])->name('cart.store');
Route::post('/api/cart/build', [CartController::class, 'storeBuild'])->name('cart.build.store');
Route::patch('/api/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/api/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/api/wishlist', [WishlistController::class, 'show'])->name('wishlist.show');
Route::post('/api/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
Route::delete('/api/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
Route::post('/api/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/api/checkout/card', [CheckoutController::class, 'storeCard'])->name('checkout.card.store');
Route::get('/api/account/orders', [AccountOrderController::class, 'index'])->middleware('auth')->name('account.orders.index');
Route::patch('/api/account/orders/{order}/cancel', [AccountOrderController::class, 'cancel'])->middleware('auth')->name('account.orders.cancel');
Route::get('/api/admin/products', [AdminProductController::class, 'index'])->middleware('auth')->name('admin.products.index');
Route::get('/api/admin/orders', [AdminOrderController::class, 'index'])->middleware('auth')->name('admin.orders.index');
Route::patch('/api/admin/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->middleware('auth')->name('admin.orders.cancel');
Route::post('/api/orders/track', [OrderTrackingController::class, 'show'])->name('orders.track.show');
Route::post('/api/admin/brands', [AdminBrandController::class, 'store'])->middleware('auth')->name('admin.brands.store');
Route::delete('/api/admin/brands/{brand}', [AdminBrandController::class, 'destroy'])->middleware('auth')->name('admin.brands.destroy');
Route::post('/api/admin/categories', [AdminCategoryController::class, 'store'])->middleware('auth')->name('admin.categories.store');
Route::delete('/api/admin/categories/{category}', [AdminCategoryController::class, 'destroy'])->middleware('auth')->name('admin.categories.destroy');
Route::post('/api/admin/products', [AdminProductController::class, 'store'])->middleware('auth')->name('admin.products.store');
Route::put('/api/admin/products/{product}', [AdminProductController::class, 'update'])->middleware('auth')->name('admin.products.update');
Route::delete('/api/admin/products/{product}', [AdminProductController::class, 'destroy'])->middleware('auth')->name('admin.products.destroy');
Route::get('/api/home-products', HomeProductController::class)->name('home-products.index');
Route::get('/api/search-products', SearchProductController::class)->name('search-products.index');
