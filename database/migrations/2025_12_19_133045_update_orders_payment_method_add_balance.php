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
        // For SQLite, we don't need to modify the enum as it's stored as text
        // For MySQL/PostgreSQL, we need to add 'balance' to the enum
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('upi', 'cash', 'card', 'mobile', 'balance') DEFAULT 'upi'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For MySQL/PostgreSQL, revert to original enum
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('upi', 'cash', 'card', 'mobile') DEFAULT 'upi'");
        }
    }
};
