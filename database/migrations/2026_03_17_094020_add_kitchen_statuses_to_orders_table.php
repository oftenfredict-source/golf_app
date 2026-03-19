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
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('orders', function (Blueprint $table) {
                // SQLite handles this as text
            });
        } else {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('saved', 'pending', 'preparing', 'ready', 'served', 'complete', 'cancelled') DEFAULT 'saved'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // No-op
        } else {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('saved', 'complete', 'cancelled') DEFAULT 'saved'");
        }
    }
};
