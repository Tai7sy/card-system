<?php
use Illuminate\Support\Facades\Schema; use Illuminate\Database\Schema\Blueprint; use Illuminate\Database\Migrations\Migration; class AddManualProducts extends Migration { public function up() { if (!Schema::hasColumn('products', 'fields')) { Schema::table('products', function (Blueprint $sp3c6e67) { $sp3c6e67->text('fields')->nullable()->after('instructions'); $sp3c6e67->tinyInteger('delivery')->default(\App\Product::DELIVERY_AUTO)->after('fee_type'); }); } if (!Schema::hasColumn('orders', 'contact_ext')) { DB::unprepared('
ALTER TABLE `orders`
CHANGE COLUMN `email` `contact`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `customer`,
CHANGE COLUMN `email_sent` `send_status`  tinyint(4) NOT NULL DEFAULT 0 AFTER `contact`;
            '); Schema::table('orders', function (Blueprint $sp3c6e67) { $sp3c6e67->text('contact_ext')->nullable()->after('contact'); }); } } public function down() { } }