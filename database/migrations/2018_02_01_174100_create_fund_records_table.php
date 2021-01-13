<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFundRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('type')->default(\App\FundRecord::TYPE_OUT);
            $table->integer('amount');
            $table->integer('balance')->default(0); //操作后余额
            $table->integer('order_id')->nullable(); //关联订单
            $table->string('withdraw_id')->nullable(); //关联订单
            $table->string('remark')->nullable();
            $table->timestamps();
        });

        DB::unprepared('ALTER TABLE `fund_records` CHANGE COLUMN `created_at` `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP;');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fund_records');
    }
}
