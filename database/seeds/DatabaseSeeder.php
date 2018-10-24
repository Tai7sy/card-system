<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SystemSeeder::class,
            UsersSeeder::class,
            ProductsSeeder::class,
            CardsSeeder::class,
            CouponsSeeder::class,
            OrdersSeeder::class,
        ]);
        //if (config('app.debug'))
        //    $this->call(MyPayTableSeeder::class);
        //else
            $this->call(PayTableSeeder::class);
    }
}
