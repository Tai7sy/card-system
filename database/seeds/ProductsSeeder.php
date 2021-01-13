<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_id = \App\User::first()->id;

        $category = new \App\Category;
        $category->user_id = $user_id;
        $category->name = '测试分组';
        $category->enabled = true;
        $category->save();

        $category = new \App\Category;
        $category->user_id = $user_id;
        $category->name = '这里是一个啦啦啦啦啦啦超级无敌爆炸螺旋长的商品类别标题';
        $category->enabled = true;
        $category->save();

        $category = new \App\Category;
        $category->user_id = $user_id;
        $category->name = '密码123456';
        $category->enabled = true;
        $category->password = '123456';
        $category->password_open = true;
        $category->save();


        // 1
        $product = new \App\Product;
        $product->id = 1;
        $product->user_id = $user_id;
        $product->category_id = 1;
        $product->name = '测试商品';
        $product->description = '这里是测试商品的一段简短的描述';
        $product->price = 1;
        $product->enabled = true;
        $product->support_coupon = true;
        $product->count_sold = 1;
        $product->count_all = 3;
        $product->instructions = '充值网址: XXXXX';
        $product->save();

        // 2
        $product = new \App\Product;
        $product->id = 2;
        $product->user_id = $user_id;
        $product->category_id = 1;
        $product->name = '重复测试密码123456';
        $product->description = '<h2>商品描述</h2>所十二星座运势查询,提前预测2016年十二星座运势内容,让你能够占卜吉凶;2016年生肖运势测算,生肖开运,周易风水。';
        $product->instructions = '充值网址: XXXXX';
        $product->password = '123456';
        $product->password_open = true;
        $product->support_coupon = true;
        $product->price = 10;
        $product->price_whole = '[["2","8"],["10","5"]]';
        $product->enabled = true;
        $product->count_sold = 2;
        $product->count_all = 100;
        $product->count_warn = 10;
        $product->save();

        // 3
        $product = new \App\Product;
        $product->user_id = $user_id;
        $product->category_id = 2;
        $product->name = '测试商品_2';
        $product->description = '这里是测试商品的一段简短的描述, 可以插入多媒体文本';
        $product->price = 1;
        $product->enabled = true;
        $product->save();

        // 4
        $product = new \App\Product;
        $product->user_id = $user_id;
        $product->category_id = 3;
        $product->name = '测试商品_3';
        $product->description = '这里是测试商品的一段简短的描述, 可以插入多媒体文本';
        $product->price = 1;
        $product->enabled = true;
        $product->save();


    }
}
