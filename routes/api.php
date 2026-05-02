<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\SupplierController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello, World!']);
});

//Admin auth routes
Route::post('/admin/register', [AdminAuthController::class, 'register']);

Route::post('/admin/login', [AdminAuthController::class, 'login']);

//Supplier routes
Route::post('/supplier/create', [SupplierController::class, 'create']);

Route::put('/supplier/update', [SupplierController::class, 'update']);


//customer auth routes
Route::post('/customer/register', [CustomerAuthController::class, 'register']);

Route::post('/customer/login', [CustomerAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/customer/profile', [CustomerAuthController::class, 'profile']);

    Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);

    Route::get('/admin/profile', [AdminAuthController::class, 'profile']);

    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

});
