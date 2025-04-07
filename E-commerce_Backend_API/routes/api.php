<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\WishList\WishListController;

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
});