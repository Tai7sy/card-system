<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllToFundRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('fund_records', 'all')) {

            Schema::table('fund_records', function (Blueprint $table) {
                $table->integer('all')->nullable()->after('amount');
                $table->integer('frozen')->nullable()->after('all');
                $table->integer('paid')->nullable()->after('frozen');
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
                     'all',
                     'frozen',
                     'paid'
                 ] as $column) {
            try {
                Schema::table('fund_records', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            } catch (\Throwable $e) {
            }
        }
    }
}
