<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driving_range_config', function (Blueprint $table) {
            $table->id();
            $table->integer('total_bays')->default(20);
            $table->integer('balls_per_bucket')->default(50);
            $table->integer('range_distance')->default(250);
            $table->boolean('has_roof')->default(true);
            $table->boolean('has_lighting')->default(true);
            $table->boolean('has_tracking')->default(false);
            $table->decimal('hourly_rate', 12, 2)->default(5000);
            $table->decimal('bucket_price', 12, 2)->default(2000);
            $table->decimal('unlimited_price', 12, 2)->default(8000);
            $table->decimal('member_discount', 5, 2)->default(10);
            $table->decimal('premium_rate', 12, 2)->default(7500);
            $table->decimal('regular_rate', 12, 2)->default(5000);
            $table->timestamps();
        });

        Schema::create('driving_range_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_upi')->nullable();
            $table->integer('bay_number');
            $table->enum('session_type', ['hourly', 'bucket', 'unlimited'])->default('hourly');
            $table->integer('buckets_count')->default(1);
            $table->integer('balls_used')->default(0);
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('payment_method', ['upi', 'cash', 'card', 'mobile'])->default('upi');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driving_range_sessions');
        Schema::dropIfExists('driving_range_config');
    }
};



