<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartCheckoutController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\OrderTrackingController;
use App\Http\Controllers\Api\V1\WarrantyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile App API v1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ==========================================
    // 1. Customer Authentication (Public)
    // ==========================================
    Route::post('/customer/register', [AuthController::class, 'register']);
    Route::post('/customer/login', [AuthController::class, 'login']);

    // ==========================================
    // 2. Catalog & Storefront (Public)
    // ==========================================
    Route::get('/home', [CatalogController::class, 'home']);
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/products', [CatalogController::class, 'products']);
    Route::get('/products/{slug_or_id}', [CatalogController::class, 'productDetail']);

    // ==========================================
    // 3. Cart, Coupons & Shipping (Public / Guest)
    // ==========================================
    Route::post('/cart/validate', [CartCheckoutController::class, 'validateCart']);
    Route::post('/coupon/apply', [CartCheckoutController::class, 'applyCoupon']);
    Route::get('/delivery-methods', [CartCheckoutController::class, 'deliveryMethods']);
    Route::get('/shipping-zones', [CartCheckoutController::class, 'deliveryMethods']); // Alias
    Route::post('/checkout/place-order', [CartCheckoutController::class, 'placeOrder']);

    // ==========================================
    // 4. Public Order Tracking & Barcode Scanner
    // ==========================================
    Route::get('/track-order', [OrderTrackingController::class, 'trackOrder']);
    Route::get('/warranty/verify', [WarrantyController::class, 'verifyWarranty']);

    // ==========================================
    // 5. Authenticated Customer Protected Routes
    // ==========================================
    Route::middleware('auth:sanctum')->group(function () {
        // Customer Profile
        Route::get('/customer/profile', [AuthController::class, 'profile']);
        Route::put('/customer/profile', [AuthController::class, 'updateProfile']);
        Route::post('/customer/fcm-token', [AuthController::class, 'updateFcmToken']);
        Route::post('/customer/logout', [AuthController::class, 'logout']);

        // Customer Orders
        Route::get('/customer/orders', [OrderTrackingController::class, 'orders']);
        Route::get('/customer/orders/{order_no}', [OrderTrackingController::class, 'orderDetail']);

        // Customer Warranties
        Route::get('/customer/warranties', [WarrantyController::class, 'customerWarranties']);
        Route::post('/customer/warranties/claim', [WarrantyController::class, 'claimWarranty']);
    });
});
