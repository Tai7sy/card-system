<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInventoryToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('products', 'inventory')) {

            Schema::table('products', function (Blueprint $table) {
                $table->tinyInteger('inventory')->default(\App\User::INVENTORY_AUTO)->after('enabled'); // 商品库存状态 默认跟随店铺
                $table->tinyInteger('fee_type')->default(\App\User::FEE_TYPE_AUTO)->after('inventory');// 商品手续费状态 默认跟随店铺

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
        foreach ([
                     'inventory',
                     'fee_type'
                 ] as $column) {
            try {
                Schema::table('products', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            } catch (\Throwable $e) {
            }
        }

    }
}
