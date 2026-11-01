<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pesapal_tracking_id', 128)->nullable()->index()->after('transaction_id');
            $table->string('payment_phone', 32)->nullable()->change();
        });

        // Update payment_method check constraint to allow Pesapal
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_payment_method_check');
            DB::statement("
                ALTER TABLE orders ADD CONSTRAINT orders_payment_method_check
                CHECK (payment_method IN ('Pesapal', 'pesapal', 'MTN Mobile Money', 'Airtel Money', 'COD', 'card', 'bank_transfer', 'Visa', 'Mastercard', 'mobile_money'))
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_payment_method_check');
            DB::statement("
                ALTER TABLE orders ADD CONSTRAINT orders_payment_method_check
                CHECK (payment_method IN ('MTN Mobile Money', 'Airtel Money', 'COD', 'card', 'bank_transfer'))
            ");
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['pesapal_tracking_id']);
            $table->dropColumn('pesapal_tracking_id');
            $table->string('payment_phone', 32)->nullable(false)->change();
        });
    }
};
