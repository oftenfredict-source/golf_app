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
        Schema::table('equipment_rentals', function (Blueprint $table) {
            // Add member_id column
            if (!Schema::hasColumn('equipment_rentals', 'member_id')) {
                $table->foreignId('member_id')->nullable()->after('equipment_id')->constrained()->onDelete('set null');
            }
        });
        
        // Update payment_method enum to include 'balance' (for SQLite, this is handled at application level)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE equipment_rentals MODIFY COLUMN payment_method ENUM('upi', 'cash', 'card', 'mobile', 'balance') DEFAULT 'balance'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_rentals', function (Blueprint $table) {
            if (Schema::hasColumn('equipment_rentals', 'member_id')) {
                $table->dropForeign(['member_id']);
                $table->dropColumn('member_id');
            }
        });
        
        // Revert payment_method enum (for SQLite, this is handled at application level)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE equipment_rentals MODIFY COLUMN payment_method ENUM('upi', 'cash', 'card', 'mobile') DEFAULT 'upi'");
        }
    }
};


