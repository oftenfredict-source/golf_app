<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL/PostgreSQL, update the enum to include balance and mobile_money
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE driving_range_sessions MODIFY COLUMN payment_method ENUM('upi', 'cash', 'card', 'mobile', 'mobile_money', 'balance') DEFAULT 'upi'");
        }
        // For SQLite, enum modification is not directly supported.
        // The application logic will handle mapping 'balance' to 'upi' and 'mobile_money' to 'mobile'.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE driving_range_sessions MODIFY COLUMN payment_method ENUM('upi', 'cash', 'card', 'mobile') DEFAULT 'upi'");
        }
    }
};


