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
    Route::post('profile/identity/edit', 'Api\Setting\SettingsController@editIdentity')->middleware('jwt.verify');
    Route::post('profile/password/edit', 'Api\Setting\SettingsController@changePassword')->middleware('jwt.verify');
});


/**
 * Menu
 * Routes
 */
// Route::get('data', 'Api\RestaurantMenu\RestaurantMenuController@test')->middleware('jwt.verify');
Route::get('menu/list', 'Api\RestaurantMenu\RestaurantMenuController@showMenu')->middleware('jwt.verify');
Route::post('menu/get', 'Api\RestaurantMenu\RestaurantMenuController@getMenu')->middleware('jwt.verify');
Route::post('menu/create', 'Api\RestaurantMenu\RestaurantMenuController@createMenu')->middleware('jwt.verify');
Route::post('menu/delete', 'Api\RestaurantMenu\RestaurantMenuController@deleteMenu')->middleware('jwt.verify');
Route::post('menu/edit', 'Api\RestaurantMenu\RestaurantMenuController@editMenu')->middleware('jwt.verify');
Route::post('menu/stock/edit', 'Api\RestaurantMenu\RestaurantMenuController@editStockQty')->middleware('jwt.verify');

/** 
 * Order 
 * Routes
 */
Route::post('order/create', 'Api\Order\OrderController@createOrder')->middleware('jwt.verify');
Route::post('order/edit', 'Api\Order\OrderController@editOrder')->middleware('jwt.verify');
Route::get('order/list', 'Api\Order\OrderController@getOrderList')->middleware('jwt.verify');
Route::get('order/cancelled/list', 'Api\Order\OrderController@getCancelledOrderList')->middleware('jwt.verify');
Route::get('order/successed/list', 'Api\Order\OrderController@getSuccessOrderList')->middleware('jwt.verify');
Route::get('order/order-number', 'Api\Order\OrderController@getOrderNumber')->middleware('jwt.verify');
Route::post('order/cancel/{id}', 'Api\Order\OrderController@cancelOrder')->middleware('jwt.verify');
Route::post('order/pay/cash/{id}', 'Api\Order\OrderController@payCash')->middleware('jwt.verify');

/** 
 * Order 
 * Details
 * Routes
 */
Route::post('order/details/create', 'Api\Order\OrderDetailsController@createOrderDetails')->middleware('jwt.verify');
Route::post('order/details/list/{id}', 'Api\Order\OrderDetailsController@getOrderDetailsList')->middleware('jwt.verify');
Route::get('order/cancelled/details/list/{id}', 'Api\Order\OrderDetailsController@getCancelledOrderDetailsList')->middleware('jwt.verify');
Route::get('order/successed/details/list/{id}', 'Api\Order\OrderDetailsController@getSuccessOrderDetailsList')->middleware('jwt.verify');
Route::post('order/details/delete/{code}', 'Api\Order\OrderDetailsController@deleteMenuFromOrder')->middleware('jwt.verify');


/**
 * Admin
 * Users Management
 * Routes
 */   
Route::post('siAdmino/users/create', 'Api\User\UserController@createUser')->middleware('jwt.verify');
// Cashier
Route::get('siAdmino/users/cashier/list', 'Api\User\UserController@getCashierUsers')->middleware('jwt.verify');
Route::post('siAdmino/users/any-role/edit/{id}', 'Api\User\UserController@editUser')->middleware('jwt.verify');
Route::post('siAdmino/users/any-role/delete/{id}', 'Api\User\UserController@deleteUser')->middleware('jwt.verify');
Route::get('siAdmino/users/admin/list', 'Api\User\UserController@getAdminUsers')->middleware('jwt.verify');