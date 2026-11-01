<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('brand', 100);
            $table->decimal('price', 12, 2);
            $table->decimal('original_price', 12, 2)->nullable();
            $table->string('image', 500);
            $table->boolean('is_new')->default(true);
            $table->unsignedTinyInteger('discount')->nullable();
            $table->timestamps();
            $table->index(['brand', 'name']);
        });

        Schema::create('product_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();
        });

        Schema::create('product_size_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size', 8);
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'size']);
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_url', 500);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('product_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('headline', 500);
            $table->text('subtitle');
            $table->string('button_text', 100);
            $table->string('button_url', 255);
            $table->string('secondary_button_text', 100);
            $table->string('secondary_button_url', 255);
            $table->string('image', 500)->nullable();
            $table->json('stats')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method', 32);
            $table->string('payment_phone', 32);
            $table->string('status', 32)->default('pending')->index();
            $table->string('payment_status', 32)->default('pending')->index();
            $table->string('transaction_id', 128)->nullable()->unique();
            $table->timestamp('payment_initiated_at')->nullable();
            $table->string('shipping_name')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city', 128)->nullable();
            $table->string('shipping_phone', 32)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price_at_sale', 12, 2);
            $table->timestamps();
        });

        Schema::create('inventory_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            $table->integer('change_amount');
            $table->string('change_type', 32);
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['product_id', 'created_at']);
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            $table->json('images')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'status']);
        });

        Schema::create('wishlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('full_name')->nullable();
            $table->string('phone', 32)->nullable();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20)->default('user');
            $table->timestamps();
            $table->unique(['user_id', 'role']);
        });

        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('points')->default(0);
            $table->string('tier', 50)->default('bronze');
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->index(['is_read', 'created_at']);
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'newsletter_subscribers', 'contact_messages', 'loyalty_points', 'user_roles', 'profiles',
            'wishlist', 'comments', 'inventory_history', 'order_items', 'orders', 'hero_sections',
            'product_descriptions', 'product_images', 'product_size_stock', 'product_stock', 'products', 'categories',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
