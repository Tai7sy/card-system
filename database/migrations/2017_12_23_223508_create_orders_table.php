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
            $table->string('order_no');
            $table->integer('good_id');
            $table->integer('count');
            $table->text('email');
            $table->boolean('email_sent');
            $table->text('comment')->nullable();
            $table->integer('amount');
            $table->integer('pay_id');
            $table->string('pay_trade_no')->nullable();
            $table->boolean('paid');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
            $table->index('order_no');
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
