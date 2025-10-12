<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\CompanyController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Sale\SaleDetailController;
use App\Http\Controllers\Sale\SalePaymentController;
use App\Http\Controllers\Guia\GuiaRemisionController;
use App\Http\Controllers\Product\CategorieController;
use App\Http\Controllers\Sale\FacturacionEletronicaController;

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

    Route::get("sales/config",[SaleController::class,"config"]);
    Route::post("sales/index",[SaleController::class,"index"]);
    Route::get("sales/search_anticipo/{anticipo}",[SaleController::class,"search_anticipo"]);
    Route::resource("sales",SaleController::class);

    Route::resource("sale_details",SaleDetailController::class);
    Route::resource("sale_payments",SalePaymentController::class);

    Route::post("seend_sunat",[FacturacionEletronicaController::class,"sunat_seend"]);
    Route::post("sunat_nota_seend",[FacturacionEletronicaController::class,"sunat_nota_seend"]);

    Route::get("guia/config",[GuiaRemisionController::class,"config"]);
    Route::post("guia/index",[GuiaRemisionController::class,"index"]);
    Route::resource("guia",GuiaRemisionController::class);
});

Route::get("guia-remision-pdf/{id}",[GuiaRemisionController::class,"pdf"]);
Route::get("sales-pdf/{id}",[SaleController::class,"pdf"]);
Route::get("electronic-note-pdf/{n_operacion}",[FacturacionEletronicaController::class,"pdf"]);