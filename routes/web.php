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

// 注意 这个首页需要判断是否在子域名中(店铺链接)
Route::get('/', 'HomeController@shop');

// dev
if (config('app.debug')) {
    Route::get('/install', 'DevController@install');
    Route::get('/test', 'DevController@test');
}

Route::get('/storage/{file_path}', 'Merchant\File@renderImage')
    ->where('file_path', '.+');;


// 传入单号, 进行支付 (重新支付)
Route::get('/pay/{order_no}', 'Shop\Pay@pay');


// 支付返回 out_trade_no可选, 设置了会检验是否已经发货(Alipay Aliwap)
Route::any('/pay/return/{pay_id}/{out_trade_no}', 'Shop\Pay@payReturn');
Route::any('/pay/return/{pay_id}', 'Shop\Pay@payReturn');
// 支付异步通知
Route::any('/pay/notify/{pay_id}', 'Shop\Pay@payNotify');

// 渲染二维码支付页面
Route::get('/qrcode/pay/{order_no}/{pay_file}', 'Shop\Pay@qrcode');

// 渲染支付结果页面
Route::get('/pay/result/{order_no}', 'Shop\Pay@result');

// 后台
Route::get('/admin', 'HomeController@admin');
Route::get('/admin/{all}', 'HomeController@admin')
    ->where('all', '.*');


// 店铺地址
Route::get('/c/{en_category_id}', 'HomeController@shop_category');
Route::get('/p/{en_product_id}', 'HomeController@shop_product');
Route::get('/s','HomeController@shop_default');