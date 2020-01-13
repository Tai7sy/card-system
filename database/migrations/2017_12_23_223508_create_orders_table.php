<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->string('order_no', 128)->index();       // 订单序列号 带有时间的很长的那种
            $table->integer('product_id');
            $table->string('product_name')->nullable();             // 商品名称备份, 防止商户修改商品
            $table->integer('count');                               // 购买数量
            $table->string('ip')->nullable();                       // 购买者IP
            $table->string('customer', 32)->nullable();     // 购买者ID, 下单时随机生成, 用于浏览器缓存查订单
            $table->string('contact')->nullable();                  // 购买者邮箱 or 联系方式
            $table->text('contact_ext')->nullable();                // 购买者附加信息
            $table->tinyInteger('send_status')->default(App\Order::SEND_STATUS_UN);  // 邮件/短信 发送状态

            $table->text('remark')->nullable();

            $table->integer('cost')->default(0);   //商户成本
            $table->integer('price')->default(0);  //商品价格
            $table->integer('discount')->default(0);  //折扣金额
            $table->integer('paid')->default(0);   //支付
            $table->integer('fee')->default(0);    //手续费
            $table->integer('system_fee')->default(0);    //系统通道手续费
            $table->integer('income')->default(0); //获得 (支付-手续费)

            $table->integer('pay_id'); //支付通道
            $table->string('pay_trade_no')->nullable(); //支付通道订单号
            $table->integer('status')->default(\App\Order::STATUS_UNPAY);
            $table->string('frozen_reason')->nullable();

            $table->string('api_out_no', 128)->nullable();
            $table->text('api_info')->nullable();

            $table->dateTime('paid_at')->nullable(); //支付时间
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
        Schema::dropIfExists('orders');
    }
}
