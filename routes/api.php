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


// Route::group(['middleware' => 'auth:sanctum'], function() {
//     // For User Profile
//     Route::get('get-user', 'Api\Auth\AuthController@getUser');
//     Route::post('logout', 'Api\Auth\AuthController@logoutCurrentUser');

// });

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'Api\Auth\AuthController@login');
    Route::post('register', 'Api\Auth\AuthController@register');
    Route::get('email/verify/{id}', 'Api\Auth\VerificationApiController@verify')->name('verificationapi.verify');
    Route::post('email/resend', 'Api\Auth\VerificationApiController@resendVerificationmail')->name('verificationapi.resend');
    Route::post('password/forgot', 'Api\Auth\ForgotPasswordController@sendResetLinkEmail')->name('api.forgot-password');
    Route::post('password/reset', 'Api\Auth\ResetPasswordController@reset')->name('api.reset-password');

    Route::post('logout', 'Api\Auth\AuthController@logoutCurrentUser')->middleware('jwt.verify');
    Route::post('refresh', 'Api\Auth\AuthController@refresh');

    Route::get('me', 'Api\Auth\AuthController@me')->middleware('jwt.verify');
});


/**
 * Menu
 * Routes
 */

Route::get('data', 'Api\RestaurantMenu\RestaurantMenuController@test')->middleware('jwt.verify');
Route::get('menu/list', 'Api\RestaurantMenu\RestaurantMenuController@showMenu')->middleware('jwt.verify');
Route::post('menu/get', 'Api\RestaurantMenu\RestaurantMenuController@getMenu')->middleware('jwt.verify');
Route::post('menu/create', 'Api\RestaurantMenu\RestaurantMenuController@createMenu')->middleware('jwt.verify');
Route::post('menu/delete', 'Api\RestaurantMenu\RestaurantMenuController@deleteMenu')->middleware('jwt.verify');
Route::post('menu/edit', 'Api\RestaurantMenu\RestaurantMenuController@editMenu')->middleware('jwt.verify');
Route::post('menu/stock/edit', 'Api\RestaurantMenu\RestaurantMenuController@editStockQty')->middleware('jwt.verify');
Route::post('order/create', 'Api\Order\CreateMenuOrderController@createOrder')->middleware('jwt.verify');
Route::post('order/details/create', 'Api\Order\CreateMenuOrderController@createOrderDetails')->middleware('jwt.verify');
Route::post('order/create', 'Api\Order\CreateMenuOrderController@createOrder')->middleware('jwt.verify');
Route::get('order/list', 'Api\Order\EditMenuOrderController@getOrderList')->middleware('jwt.verify');
