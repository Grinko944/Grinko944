<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get("/products", [\App\Http\Controllers\ApiController::class, "index"]);
Route::get("/products/search/{title}", [\App\Http\Controllers\ApiController::class, "search"]);
Route::get("/products/show/{id}", [\App\Http\Controllers\ApiController::class, "show"]);

Route::patch("/product/update/{id}", [\App\Http\Controllers\ApiController::class, "update"]);

Route::delete("/product/delete/{id}", [\App\Http\Controllers\ApiController::class, "destroy"]);

// auth
Route::get("/auth", [\App\Http\Controllers\ApiController::class, "auth"]);

// user
Route::get("/users/get/{id}", [\App\Http\Controllers\ApiController::class, "getUser"])->name("api.userGet");

Route::get("/user/edit/{id}", [\App\Http\Controllers\ApiController::class, "userUpdate"]);

// basket
Route::get("/basket/{id}", [\App\Http\Controllers\ApiController::class, "basket"]);
Route::get("/basket/add/product", [\App\Http\Controllers\ApiController::class, "addToCart"]);
Route::get("/basket/delete/product", [\App\Http\Controllers\ApiController::class, "deleteFromBasket"]);
Route::get("/basket/delete/all/{id}", [\App\Http\Controllers\ApiController::class, "deleteAllFromBasket"]);
Route::get("/basket/update/product/{id}", [\App\Http\Controllers\ApiController::class, "updateProductFromBasket"]);
Route::get("/basket/orders/add/", [\App\Http\Controllers\ApiController::class, "fromBasketToOrders"]);

// orders
Route::get("/orders/get", [\App\Http\Controllers\ApiController::class, "getOrders"]);
