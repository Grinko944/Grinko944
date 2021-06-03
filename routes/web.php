<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/migrate", function() {
  echo Hash::make("291106Anton");
  echo "\n";
  echo Crypt::encrypt("291106Anton");
  Artisan::call("migrate:fresh");
});

Route::get("/", function () {
    if (auth()->check())
        return redirect(\route("admin.home"));
    return redirect(\route("login"));
});

// auth
Auth::routes();

// product
Route::get('/admin/home', [\App\Http\Controllers\AdminController::class, "home"])->name("admin.home");
Route::get('/admin/product/show/{id}', [\App\Http\Controllers\AdminController::class, "showProduct"])->name("admin.showProduct");
Route::get('/admin/product/edit/{id}', [\App\Http\Controllers\AdminController::class, "edit"])->name("admin.edit");
Route::get('/admin/product/create/', [\App\Http\Controllers\AdminController::class, "create"])->name("admin.create");

Route::post('/admin/product/store/', [\App\Http\Controllers\AdminController::class, "store"])->name("admin.store");

Route::patch("admin/product/edit/patch/{id}", [\App\Http\Controllers\AdminController::class, "update"])->name("admin.update");

Route::delete("/admin/product/delete/{id}", [\App\Http\Controllers\AdminController::class, "destroy"])->name("admin.destroy");

// order
Route::get("admin/order/show/{id}", [\App\Http\Controllers\AdminController::class, "orderShow"])->name("admin.showOrder");
Route::get("admin/order/edit/{id}", [\App\Http\Controllers\AdminController::class, "orderEdit"])->name("admin.orderEdit");
Route::get("admin/order/create/", [\App\Http\Controllers\AdminController::class, "orderCreate"])->name("admin.orderCreate");
Route::post("admin/order/create/store", [\App\Http\Controllers\AdminController::class, "orderStore"])->name("admin.orderStore");

Route::patch("admin/order/edit/patch/{id}", [\App\Http\Controllers\AdminController::class, "orderUpdate"])->name("admin.orderUpdate");
Route::delete("admin/order/destroy/{id}", [\App\Http\Controllers\AdminController::class, "destroyOrder"])->name("admin.orderDestroy");

// user
Route::get("admin/user/create/", [\App\Http\Controllers\AdminController::class, "userAdd"])->name("admin.userCreate");
Route::get("admin/user/edit/{id}", [\App\Http\Controllers\AdminController::class, "userEdit"])->name("admin.userEdit");

Route::post("admin/user/store/", [\App\Http\Controllers\AdminController::class, "userStore"])->name("admin.userStore");

Route::delete("admin/user/delete/{id}", [\App\Http\Controllers\AdminController::class, "destroyUser"])->name("admin.destroyUser");
Route::patch("admin/user/update/{id}", [\App\Http\Controllers\AdminController::class, "userUpdate"])->name("admin.userUpdate");

// chat
Route::get("chat/{userId}/{orderId}", function ($userId, $orderId) {
    $user = \App\Models\User::find($userId);
    return view("single_chat", compact("user"));
})->name("single_chat");
