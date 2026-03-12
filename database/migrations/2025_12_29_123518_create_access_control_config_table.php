<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('access_control_config', function (Blueprint $table) {
            $table->id();
            $table->boolean('members_only')->default(true);
            $table->boolean('require_valid_card')->default(true);
            $table->boolean('check_balance')->default(false);
            $table->boolean('allow_guests')->default(true);
            $table->boolean('operating_hours_only')->default(true);
            $table->time('opening_time')->default('06:00');
            $table->time('closing_time')->default('22:00');
            $table->decimal('min_balance', 15, 2)->default(0);
            $table->decimal('guest_fee', 15, 2)->default(50000);
            $table->text('blocked_cards')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_control_config');
    }
};
