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
        // For MySQL/PostgreSQL, update the enum to include balance
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE equipment_sales MODIFY COLUMN payment_method ENUM('upi', 'cash', 'card', 'mobile', 'balance') DEFAULT 'upi'");
        }
        // For SQLite, enum modification is not directly supported.
        // The application logic will handle the 'balance' value.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE equipment_sales MODIFY COLUMN payment_method ENUM('upi', 'cash', 'card', 'mobile') DEFAULT 'upi'");
        }
    }
};


