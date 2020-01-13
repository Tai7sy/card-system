<?php

use Faker\Generator as Faker;

$factory->define(App\Order::class, function (Faker $faker) {
    $order_no = date('YmdHis') . mt_rand(10000, 99999);
    while (\App\Order::whereOrderNo($order_no)->exists()) {
        $order_no = date('YmdHis') . mt_rand(10000, 99999);
    }

    $email = random_int(0,1) ? $faker->email : 'user01@qq.com';

    $price = 1000;
    $discount = random_int(0,1) * 100;
    $paid = $price - $discount;
    return [
        'user_id' => 2,
        'order_no' => $order_no,
        'product_id' => 1,
        'count' => 1
    ];
});