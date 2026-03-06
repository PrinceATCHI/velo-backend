<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BikeConfiguratorController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\CouponController;
use App\Http\Controllers\Api\PaymentProofController;
use App\Http\Controllers\Api\Admin\PaymentProofController as AdminPaymentProofController;
use App\Http\Controllers\Api\Admin\PaymentSettingsController;
use App\Http\Controllers\TermsController;


// Routes publiques
Route::prefix('v1')->group(function () {

    // Authentification
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Catégories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);

    // Produits
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/products/{id}/related', [ProductController::class, 'related']);

    // Avis
    Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);

    // Configurateur
    Route::get('/configurator/components/{type?}', [BikeConfiguratorController::class, 'getComponents']);
    Route::get('/configurator/components/{id}/compatible', [BikeConfiguratorController::class, 'getCompatibleComponents']);
    Route::post('/configurator/check-compatibility', [BikeConfiguratorController::class, 'checkCompatibility']);
    Route::post('/configurator/calculate-price', [BikeConfiguratorController::class, 'calculatePrice']);

    Route::get('/payment-settings', function () {
        return response()->json(App\Models\PaymentSettings::getActive());
    });
});

// Routes protégées
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/sync', [WishlistController::class, 'sync']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);

    // Profil
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);

    // Adresses
    Route::get('/user/addresses', [UserController::class, 'addresses']);
    Route::post('/user/addresses', [UserController::class, 'storeAddress']);
    Route::put('/user/addresses/{id}', [UserController::class, 'updateAddress']);
    Route::delete('/user/addresses/{id}', [UserController::class, 'destroyAddress']);

    // Panier
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{itemId}', [CartController::class, 'update']);
    Route::delete('/cart/{itemId}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Commandes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Avis
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // Configurateur
    Route::post('/configurator/save', [BikeConfiguratorController::class, 'saveConfiguration']);
    Route::get('/configurator/my-configurations', [BikeConfiguratorController::class, 'myConfigurations']);
    Route::get('/configurator/configurations/{id}', [BikeConfiguratorController::class, 'showConfiguration']);
    Route::delete('/configurator/configurations/{id}', [BikeConfiguratorController::class, 'deleteConfiguration']);

    // Preuves de paiement
    Route::post('/payment-proofs', [PaymentProofController::class, 'store']);
    Route::get('/payment-proofs/{orderId}', [PaymentProofController::class, 'show']);
});

// Routes ADMIN
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Upload d'images
    Route::post('/upload-image', [ImageUploadController::class, 'upload']);
    Route::delete('/delete-image', [ImageUploadController::class, 'delete']);

    // Gestion des produits
    Route::apiResource('products', AdminProductController::class);

    // Gestion des commandes
    Route::apiResource('orders', AdminOrderController::class);

    // Gestion des clients
    Route::apiResource('customers', CustomerController::class);

    // Gestion des coupons
    Route::apiResource('coupons', CouponController::class);

    // Preuves de paiement
    Route::get('/payment-proofs', [AdminPaymentProofController::class, 'index']);
    Route::get('/payment-proofs/{id}', [AdminPaymentProofController::class, 'show']);
    Route::post('/payment-proofs/{id}/verify', [AdminPaymentProofController::class, 'verify']);
    Route::post('/payment-proofs/{id}/reject', [AdminPaymentProofController::class, 'reject']);

    // Payment Settings
    Route::get('/payment-settings', [PaymentSettingsController::class, 'index']);
    Route::put('/payment-settings', [PaymentSettingsController::class, 'update']);

    // CGU
    Route::post('/auth/accept-terms', [TermsController::class, 'accept']);
    Route::delete('/auth/refuse-terms', [TermsController::class, 'refuse']);
    Route::get('/auth/terms-status', [TermsController::class, 'status']);
});