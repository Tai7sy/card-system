<?php

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

Route::get('/', 'HomeController@index');
Route::get('/login', 'HomeController@login')->name('login');
Route::get('/admin', 'HomeController@admin');
Route::get('/test', 'HomeController@test');
Route::get('/install', 'HomeController@install');

Route::post('/buy', 'Shop\PayController@buy');

Route::get('/pay/{order_no}', 'Shop\PayController@pay');


Route::get('/pay/return/{driver}/{out_trade_no}', 'Shop\PayController@payReturn');
Route::get('/pay/return/{driver}', 'Shop\PayController@payReturn');
Route::any('/pay/notify/{driver}', 'Shop\PayController@payNotify');

Route::get('/qrcode/pay/{order_no}/{pay}/{qr}', 'Shop\PayController@qrcode');

Route::get('/pay/result/{order_no}', 'Shop\PayController@result');



