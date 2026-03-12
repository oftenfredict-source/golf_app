<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ball Management
        Schema::create('ball_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('ball_type')->default('standard');
            $table->integer('total_quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->integer('in_use')->default(0);
            $table->integer('damaged')->default(0);
            $table->decimal('cost_per_ball', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('ball_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['issued', 'returned', 'purchased', 'damaged', 'disposed']);
            $table->integer('quantity');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->foreignId('session_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });

        // Equipment
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('rental_hourly_rate', 12, 2)->default(0);
            $table->decimal('rental_daily_rate', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->integer('total_quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->integer('rented_quantity')->default(0);
            $table->integer('maintenance_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->boolean('is_rentable')->default(true);
            $table->boolean('is_sellable')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('equipment_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_upi')->nullable();
            $table->integer('quantity')->default(1);
            $table->enum('rental_type', ['hourly', 'daily'])->default('daily');
            $table->datetime('start_time');
            $table->datetime('expected_return');
            $table->datetime('actual_return')->nullable();
            $table->decimal('deposit_paid', 12, 2)->default(0);
            $table->decimal('rental_amount', 12, 2)->default(0);
            $table->decimal('late_fee', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['upi', 'cash', 'card', 'mobile'])->default('upi');
            $table->enum('status', ['active', 'returned', 'overdue', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('equipment_sales', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_upi')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['upi', 'cash', 'card', 'mobile'])->default('upi');
            $table->boolean('sms_sent')->default(false);
            $table->enum('status', ['completed', 'refunded', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('equipment_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // Food & Beverage
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('menu_categories');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('prep_time_minutes')->default(10);
            $table->boolean('is_available')->default(true);
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_upi')->nullable();
            $table->string('table_number')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['upi', 'cash', 'card', 'mobile'])->default('upi');
            $table->enum('status', ['pending', 'preparing', 'ready', 'served', 'completed', 'cancelled'])->default('pending');
            $table->boolean('sms_sent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->text('special_instructions')->nullable();
            $table->enum('status', ['pending', 'preparing', 'ready', 'served'])->default('pending');
            $table->timestamps();
        });

        // Counters
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->enum('type', ['food', 'beverage', 'equipment', 'general'])->default('general');
            $table->boolean('is_active')->default(true);
            $table->foreignId('assigned_user_id')->nullable();
            $table->timestamps();
        });

        // UPI/Members
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('upi_id')->unique()->nullable();
            $table->string('card_number')->unique()->nullable();
            $table->enum('membership_type', ['standard', 'premium', 'vip', 'corporate', 'guest'])->default('standard');
            $table->decimal('balance', 12, 2)->default(0);
            $table->date('valid_until')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'expired'])->default('active');
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Transactions
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('member_id')->nullable()->constrained();
            $table->string('customer_name');
            $table->enum('type', ['topup', 'payment', 'refund', 'transfer']);
            $table->enum('category', ['driving_range', 'equipment_rental', 'equipment_sale', 'food_beverage', 'membership', 'other']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2)->default(0);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->enum('payment_method', ['upi', 'cash', 'card', 'mobile', 'balance'])->default('upi');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');
            $table->boolean('sms_sent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Top-ups
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained();
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->enum('payment_method', ['cash', 'card', 'mobile', 'bank_transfer'])->default('cash');
            $table->string('reference_number')->nullable();
            $table->boolean('sms_sent')->default(false);
            $table->foreignId('processed_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topups');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('members');
        Schema::dropIfExists('counters');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_categories');
        Schema::dropIfExists('equipment_sale_items');
        Schema::dropIfExists('equipment_sales');
        Schema::dropIfExists('equipment_rentals');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('ball_transactions');
        Schema::dropIfExists('ball_inventory');
    }
};



