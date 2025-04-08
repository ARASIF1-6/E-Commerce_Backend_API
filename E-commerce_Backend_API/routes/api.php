<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\WishList\WishListController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Payment\PaymentController;

Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function ($router) {

    Route::post('register', [AuthController::class, 'registration']);
    Route::post('login', [AuthController::class, 'login']);

});

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::put('update_profile', [AuthController::class, 'update']);
    Route::delete('delete', [AuthController::class, 'destroy']);
    Route::post('wishlist/register', [WishListController::class, 'store']);
    Route::get('wishlist/show', [WishListController::class, 'index']);
    Route::delete('wishlist/delete', [WishListController::class, 'destroy']);
    Route::post('cart/register', [CartController::class, 'store']);
    Route::put('cart/update', [CartController::class, 'update']);
    Route::get('cart/show', [CartController::class, 'index']);
    Route::delete('cart/delete', [CartController::class, 'destroy']);
    Route::post('payment/register', [PaymentController::class, 'store']);
    Route::get('payment/show', [PaymentController::class, 'index']);
});