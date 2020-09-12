<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('product_id')->index();
            $table->text('card');
            $table->integer('type');
            $table->integer('status')->default(\App\Card::STATUS_NORMAL);
            $table->integer('count_sold')->default(0); // type 为 TYPE_REPEAT 时, 表示已卖出次数
            $table->integer('count_all')->default(1);  // type 为 TYPE_REPEAT 时, 表示可卖总次数
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
        try{
            DB::unprepared('DROP PROCEDURE `add_cards`;');
        }catch (\Exception $e){}
    }
}
