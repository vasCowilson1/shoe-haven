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
            $table->string('pesajet_reference', 128)->nullable()->index()->after('transaction_id');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_payment_method_check');
            DB::statement("
                ALTER TABLE orders ADD CONSTRAINT orders_payment_method_check
                CHECK (payment_method IN ('PesaJet', 'pesajet', 'Pesapal', 'pesapal', 'MTN Mobile Money', 'Airtel Money', 'COD', 'card', 'bank_transfer', 'Visa', 'Mastercard', 'mobile_money'))
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
                CHECK (payment_method IN ('Pesapal', 'pesapal', 'MTN Mobile Money', 'Airtel Money', 'COD', 'card', 'bank_transfer', 'Visa', 'Mastercard', 'mobile_money'))
            ");
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['pesajet_reference']);
            $table->dropColumn('pesajet_reference');
        });
    }
};

