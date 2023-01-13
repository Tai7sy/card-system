<?php

use Illuminate\Database\Seeder;

class CardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_id = \App\User::first()->id;
        \App\Card::insert([
            [
                'user_id' => $user_id,
                'product_id' => 1,
                'card' => '11111',
                'type' => \App\Card::TYPE_ONETIME,
                'status' => \App\Card::STATUS_NORMAL,
                'count_sold' => 0,
                'count_all' => 1
            ],
            [
                'user_id' => $user_id,
                'product_id' => 1,
                'card' => '11112',
                'type' => \App\Card::TYPE_ONETIME,
                'status' => \App\Card::STATUS_SOLD,
                'count_sold' => 1,
                'count_all' => 1
            ],
            [
                'user_id' => $user_id,
                'product_id' => 1,
                'card' => '11113',
                'type' => \App\Card::TYPE_ONETIME,
                'status' => \App\Card::STATUS_NORMAL,
                'count_sold' => 0,
                'count_all' => 1
            ],

            [
                'user_id' => $user_id,
                'product_id' => 2,
                'card' => '123456',
                'type' => \App\Card::TYPE_REPEAT,
                'status' => \App\Card::STATUS_SOLD,
                'count_sold' => 2,
                'count_all' => 100
            ]
        ]);

    }
}
