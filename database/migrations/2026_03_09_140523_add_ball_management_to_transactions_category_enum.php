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
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('category', [
                'driving_range', 
                'ball_management',
                'equipment_rental', 
                'equipment_sale', 
                'food_beverage', 
                'membership', 
                'other'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('category', [
                'driving_range', 
                'equipment_rental', 
                'equipment_sale', 
                'food_beverage', 
                'membership', 
                'other'
            ])->change();
        });
    }
};
