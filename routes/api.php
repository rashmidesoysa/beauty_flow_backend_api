<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
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

        //Supplier routes
        Route::post('/supplier/create', [SupplierController::class, 'create']);

        Route::put('/supplier/update', [SupplierController::class, 'update']);

        //Category routes
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/category/create', [CategoryController::class, 'store']);
        Route::get('/category/{id}', [CategoryController::class, 'show']);
        Route::post('/category/update/{id}', [CategoryController::class, 'update']);
        Route::delete('/category/delete/{id}', [CategoryController::class, 'destroy']);

        // SubCategory routes
        Route::get('/sub-categories', [SubCategoryController::class, 'index']);
        Route::post('/sub-category/create', [SubCategoryController::class, 'store']);
        Route::get('/sub-category/{id}', [SubCategoryController::class, 'show']);
        Route::post('/sub-category/update/{id}', [SubCategoryController::class, 'update']);
        Route::delete('/sub-category/delete/{id}', [SubCategoryController::class, 'destroy']);

        //Brand routes
        Route::get('/brands', [BrandController::class, 'index']);
        Route::post('/brand/create', [BrandController::class, 'store']);
        Route::get('/brand/{id}', [BrandController::class, 'show']);
        Route::post('/brand/update/{id}', [BrandController::class, 'update']);
        Route::delete('/brand/delete/{id}', [BrandController::class, 'destroy']);
    });
});




//customer auth routes
Route::post('/customer/register', [CustomerAuthController::class, 'register']);

Route::post('/customer/login', [CustomerAuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/customer/profile', [CustomerAuthController::class, 'profile']);

    Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);
});
