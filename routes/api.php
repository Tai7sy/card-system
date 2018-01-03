<?php

use Illuminate\Http\Request;

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

Route::post('admin/login', 'Admin\LoginController@login')->middleware('api');


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'api']], function () {
    Route::post('dashboard', 'Admin\DashboardController@index');
    Route::post('auth/change', 'Admin\LoginController@change');

    Route::post('group/get', 'Admin\GroupController@get');
    Route::post('group/edit', 'Admin\GroupController@edit');
    Route::post('group/enabled', 'Admin\GroupController@enabled');
    Route::post('group/delete', 'Admin\GroupController@delete');


    Route::post('good/get', 'Admin\GoodController@get');
    Route::post('good/edit', 'Admin\GoodController@edit');
    Route::post('good/enabled', 'Admin\GoodController@enabled');
    Route::post('good/delete', 'Admin\GoodController@delete');


    Route::post('card/get', 'Admin\CardController@get');
    Route::post('card/edit', 'Admin\CardController@edit');
    Route::post('card/enabled', 'Admin\CardController@enabled');
    Route::post('card/delete', 'Admin\CardController@delete');

    Route::post('order/get', 'Admin\OrderController@get');
    Route::post('order/delete', 'Admin\OrderController@delete');


    Route::post('pay/get', 'Admin\PayController@get');
    Route::post('pay/edit', 'Admin\PayController@edit');
    Route::post('pay/enabled', 'Admin\PayController@enabled');
    Route::post('pay/delete', 'Admin\PayController@delete');

});


Route::group(['prefix' => 'shop', 'middleware' => ['api']], function () {
    Route::post('group', 'Shop\GroupController@get');
    Route::post('good', 'Shop\GoodController@getByGroup');
    Route::post('good/info', 'Shop\GoodController@getInfo');
    Route::post('pay', 'Shop\PayController@get');

    Route::post('record/get', 'Shop\OrderController@get');
});


Route::post('qrcode/query/{driver}', 'Shop\PayController@qrQuery')->middleware('api');