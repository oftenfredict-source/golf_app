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
        // For MySQL/PostgreSQL, update the enum to include mobile_money
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE topups MODIFY COLUMN payment_method ENUM('cash', 'card', 'mobile', 'mobile_money', 'bank_transfer') DEFAULT 'cash'");
        }
        // For SQLite, enum modification is not directly supported.
        // The application logic will handle both 'mobile' and 'mobile_money' values.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE topups MODIFY COLUMN payment_method ENUM('cash', 'card', 'mobile', 'bank_transfer') DEFAULT 'cash'");
        }
    }
};


