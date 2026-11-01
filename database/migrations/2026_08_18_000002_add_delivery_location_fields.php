<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('delivery_region', 64)->nullable()->after('phone');
            $table->string('delivery_district', 128)->nullable()->after('delivery_region');
            $table->string('delivery_area', 128)->nullable()->after('delivery_district');
            $table->string('delivery_landmark', 255)->nullable()->after('delivery_area');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_region', 64)->nullable()->after('shipping_city');
            $table->string('shipping_district', 128)->nullable()->after('shipping_region');
            $table->string('shipping_area', 128)->nullable()->after('shipping_district');
            $table->string('shipping_landmark', 255)->nullable()->after('shipping_area');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_region', 'shipping_district', 'shipping_area', 'shipping_landmark']);
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['delivery_region', 'delivery_district', 'delivery_area', 'delivery_landmark']);
        });
    }
};
