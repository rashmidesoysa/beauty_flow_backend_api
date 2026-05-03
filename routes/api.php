<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\SupplierController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('admin')->group(function () {
    // Public routes
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/check-auth', [AdminAuthController::class, 'checkAuth']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/profile', [AdminAuthController::class, 'profile']);
    });
});

//Supplier routes
Route::post('/supplier/create', [SupplierController::class, 'create']);

Route::put('/supplier/update', [SupplierController::class, 'update']);


//customer auth routes
Route::post('/customer/register', [CustomerAuthController::class, 'register']);

Route::post('/customer/login', [CustomerAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/customer/profile', [CustomerAuthController::class, 'profile']);

    Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);

});
