<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayWaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_ways', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->unique();
            $table->integer('sort')->default(1000);
            $table->string('img')->nullable();
            $table->tinyInteger('type')->default(\App\PayWay::TYPE_SHOP);
            $table->text('channels')->comment('子渠道信息');
            $table->text('comment')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        if (Schema::hasColumn('pays', 'img')) {
            // 商店的支付方式
            \App\Pay::each(function (\App\Pay $pay) {
                while (\App\PayWay::where('name', $pay->name)->exists()) {
                    $pay->name .= '_' . str_random(2);
                }
                \App\PayWay::create([
                    'name' => $pay->name,
                    'img' => $pay->img,
                    'type' => \App\PayWay::TYPE_SHOP,
                    'sort' => $pay->sort,
                    'enabled' => $pay->enabled,
                    'channels' => [[$pay->id, 1]]
                ]);

                if($pay->enabled > 0){
                    $pay->enabled = true;
                    $pay->saveOrFail();
                }
            });

            Schema::table('pays', function (Blueprint $table) {
                $table->dropColumn(['img', 'sort']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_ways');
    }
}
