<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\OrderController as Order;
use App\Http\Controllers\api\AuthController as Auth;

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

Route::post("login", [Auth::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post("logout", [Auth::class, 'logout'])->name('logout');

    Route::post('/order/new', [Order::class, 'create']);
    Route::post('/order/delete', [Order::class, 'delete']);
    Route::get('/order/list/{orderId}', [Order::class, 'list']);
    Route::get('/order/discounts/{orderId}', [Order::class, 'orderDiscountDetail']);
});
