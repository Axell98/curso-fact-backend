<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Client\CompanyController;
use App\Http\Controllers\Product\CategorieController;
use App\Http\Controllers\Product\ProductController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/me', [AuthController::class, 'me'])
    ->middleware(['auth:api'])//,'permission:publish articles'
    ->name('me');
});

Route::group([
    'middeleware' => 'auth:api'
], function ($router) {
    Route::resource("roles",RoleController::class);

    Route::post("users/{id}",[UserController::class,"update"]);
    Route::resource("users",UserController::class);

    Route::post("categories/{id}",[CategorieController::class,"update"]);
    Route::resource("categories",CategorieController::class);

    Route::resource("company",CompanyController::class);

    Route::get("products/config",[ProductController::class,"config"]);
    Route::post("products/{id}",[ProductController::class,"update"]);
    Route::resource("products",ProductController::class);

    Route::resource("clients",ClientController::class);
});