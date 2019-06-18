<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_themes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128)->unique();
            $table->string('description')->nullable();
            $table->text('options')->nullable();
            $table->text('config')->nullable();
            $table->boolean('enabled')->default(true);
        });

        \App\ShopTheme::freshList();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_themes');
    }
}
