<?php

use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    private function increaseId()
    {
        // set increase to 1000
        \App\Order::insert([
            'id' => 1000,
            'user_id' => 0,
            'order_no' => '123456',
            'product_id' => \App\Product::first()->id,
            'count' => 0,
            'contact' => '',
            'pay_id' => 0,
            'status' => \App\Order::STATUS_UNPAY
        ]);
        try {
            \App\Order::where('id', 1000)->delete();
        } catch (Exception $e) {
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        self::increaseId();
    }
}
