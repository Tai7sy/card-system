<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('products', 'count_all')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('count_all')->default(0)->after('count_sold');
            });

            App\Product::whereRaw('1')->update([
                'count_sold' => 0,
                'count_all' => 0,
            ]);

            \App\Card::selectRaw('`product_id`,SUM(`count_sold`) as `count_sold`,SUM(`count_all`) as `count_all`')
                ->groupBy('product_id')->orderByRaw('`product_id`')
                ->chunk(100, function ($card_groups) {
                    foreach ($card_groups as $card_group) {
                        \App\Product::where('id', $card_group->product_id)->update([
                            'count_sold' => $card_group->count_sold,
                            'count_all' => $card_group->count_all,
                        ]);
                    }
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
        if (Schema::hasColumn('products', 'count_all')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['count_all']);
            });
        }
    }
}
