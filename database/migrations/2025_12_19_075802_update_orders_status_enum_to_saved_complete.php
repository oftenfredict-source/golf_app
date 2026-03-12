<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For SQLite, we need to recreate the column
        // First, update existing statuses to match new enum
        DB::statement("UPDATE orders SET status = 'saved' WHERE status IN ('pending', 'preparing', 'ready', 'served')");
        DB::statement("UPDATE orders SET status = 'complete' WHERE status = 'completed'");
        
        // For SQLite, we'll use a text column instead of enum
        // This is more flexible and works across databases
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('orders', function (Blueprint $table) {
                // SQLite doesn't support modifying enum, so we'll keep it as text
                // The constraint is handled at application level
            });
        } else {
            // For MySQL/PostgreSQL, modify the enum
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('saved', 'complete', 'cancelled') DEFAULT 'saved'");
        }
    }

    public function down(): void
    {
        // Revert to old statuses
        DB::statement("UPDATE orders SET status = 'pending' WHERE status = 'saved'");
        DB::statement("UPDATE orders SET status = 'completed' WHERE status = 'complete'");
        
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'preparing', 'ready', 'served', 'completed', 'cancelled') DEFAULT 'pending'");
        }
    }
};
