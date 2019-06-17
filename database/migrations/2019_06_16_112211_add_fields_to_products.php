<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('products', 'fields')) {

            Schema::table('products', function (Blueprint $table) {
                $table->text('fields')->nullable()->after('instructions');
                $table->tinyInteger('delivery')->default(\App\Product::DELIVERY_AUTO)->after('fee_type');
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
        if (Schema::hasColumn('products', 'fields')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['fields', 'delivery']);
            });
        }
    }
}
