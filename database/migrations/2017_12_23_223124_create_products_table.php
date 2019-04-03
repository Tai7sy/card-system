<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('category_id')->index();
            $table->string('name');
            $table->longText('description');
            $table->integer('sort')->default(1000);

            $table->integer('buy_min')->default(1); //限购
            $table->integer('buy_max')->default(10);

            $table->integer('count_sold')->default(0);
            $table->integer('count_all')->default(0); // 库存数量, 用后面的 add_count_to_products 计算一下
            $table->integer('count_warn')->default(0); //库存预警

            $table->boolean('support_coupon')->default(false);
            $table->string('password')->nullable();
            $table->boolean('password_open')->default(false);

            $table->integer('cost')->default(0);  //成本
            $table->integer('price'); //售价
            $table->text('price_whole')->nullable(); //批发价

            $table->text('instructions')->nullable(); // 使用说明

            $table->boolean('enabled');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
    }
}
