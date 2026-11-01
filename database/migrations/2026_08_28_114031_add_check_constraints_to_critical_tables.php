<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // orders.status — only these statuses are valid in the application
        DB::statement("
            ALTER TABLE orders ADD CONSTRAINT orders_status_check
            CHECK (status IN ('pending','confirmed','processing','shipped','delivered','cancelled','returned'))
        ");

        // orders.payment_status
        DB::statement("
            ALTER TABLE orders ADD CONSTRAINT orders_payment_status_check
            CHECK (payment_status IN ('pending','paid','failed','refunded','partially_refunded'))
        ");

        // orders.total_amount must be positive
        DB::statement('
            ALTER TABLE orders ADD CONSTRAINT orders_total_amount_check
            CHECK (total_amount > 0)
        ');

        // orders.payment_method — stored values from CheckoutController
        DB::statement("
            ALTER TABLE orders ADD CONSTRAINT orders_payment_method_check
            CHECK (payment_method IN ('MTN Mobile Money','Airtel Money','COD','card','bank_transfer'))
        ");

        // order_items.quantity must be at least 1
        DB::statement('
            ALTER TABLE order_items ADD CONSTRAINT order_items_quantity_check
            CHECK (quantity >= 1)
        ');

        // order_items.price_at_sale must be positive
        DB::statement('
            ALTER TABLE order_items ADD CONSTRAINT order_items_price_check
            CHECK (price_at_sale > 0)
        ');

        // products.price must be positive
        DB::statement('
            ALTER TABLE products ADD CONSTRAINT products_price_check
            CHECK (price > 0)
        ');

        // product_stock.quantity cannot go negative
        DB::statement('
            ALTER TABLE product_stock ADD CONSTRAINT product_stock_quantity_check
            CHECK (quantity >= 0)
        ');

        // product_size_stock.quantity cannot go negative
        DB::statement('
            ALTER TABLE product_size_stock ADD CONSTRAINT product_size_stock_quantity_check
            CHECK (quantity >= 0)
        ');
    }

    public function down(): void
    {
        $constraints = [
            'orders' => ['orders_status_check', 'orders_payment_status_check', 'orders_total_amount_check', 'orders_payment_method_check'],
            'order_items' => ['order_items_quantity_check', 'order_items_price_check'],
            'products' => ['products_price_check'],
            'product_stock' => ['product_stock_quantity_check'],
            'product_size_stock' => ['product_size_stock_quantity_check'],
        ];

        foreach ($constraints as $table => $names) {
            foreach ($names as $name) {
                DB::statement("ALTER TABLE {$table} DROP CONSTRAINT IF EXISTS {$name}");
            }
        }
    }
};
