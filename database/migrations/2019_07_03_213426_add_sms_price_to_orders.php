<?php
use Illuminate\Support\Facades\Schema; use Illuminate\Database\Schema\Blueprint; use Illuminate\Database\Migrations\Migration; class AddSmsPriceToOrders extends Migration { public function up() { if (!Schema::hasColumn('orders', 'sms_price')) { Schema::table('orders', function (Blueprint $speb36c4) { $speb36c4->integer('sms_price')->default(0)->after('discount'); }); } } public function down() { } }