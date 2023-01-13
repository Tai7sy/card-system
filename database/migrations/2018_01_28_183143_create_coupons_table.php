<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('category_id')->default(-1);
            $table->integer('product_id')->default(-1);
            $table->integer('type')->default(\App\Coupon::TYPE_REPEAT);
            $table->integer('status')->default(\App\Coupon::STATUS_NORMAL);
            $table->string('coupon', 100)->index();
            $table->integer('discount_type');
            $table->integer('discount_val');
            $table->integer('count_used')->default(0); // type 为 TYPE_REPEAT 时, 表示已用次数
            $table->integer('count_all')->default(1);  // type 为 TYPE_REPEAT 时, 表示可用总次数
            $table->string('remark')->nullable();
            $table->dateTime('expire_at')->nullable();
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
        Schema::dropIfExists('coupons');
    }
}
