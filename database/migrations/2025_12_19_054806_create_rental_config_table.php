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
        Schema::create('rental_config', function (Blueprint $table) {
            $table->id();
            $table->decimal('security_deposit', 12, 2)->default(50000);
            $table->integer('max_rental_hours')->default(4);
            $table->decimal('late_fee_per_hour', 12, 2)->default(5000);
            $table->boolean('require_deposit')->default(true);
            $table->boolean('allow_extensions')->default(true);
            $table->boolean('auto_charge_late')->default(true);
            $table->decimal('extension_fee_per_hour', 12, 2)->default(3000);
            $table->decimal('damage_fee_percentage', 5, 2)->default(10);
            $table->integer('grace_period_minutes')->default(15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_config');
    }
};
