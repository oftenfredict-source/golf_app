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
        // Update counters table to include new counter types
        Schema::table('counters', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                try {
                    DB::statement("ALTER TABLE counters MODIFY COLUMN type ENUM('food', 'beverage', 'equipment', 'general', 'chakula', 'kahawa', 'jikoni') DEFAULT 'general'");
                } catch (\Exception $e) {
                    // If enum update fails, continue
                }
            }
        });

        // Create default counters if they don't exist
        // For SQLite, use 'food' type since enum modification isn't supported
        $counterType = DB::getDriverName() === 'sqlite' ? 'food' : 'chakula';
        
        $defaultCounters = [
            ['name' => 'CHAKULA', 'type' => $counterType, 'location' => 'Main Restaurant'],
            ['name' => 'KAHAWA', 'type' => DB::getDriverName() === 'sqlite' ? 'beverage' : 'kahawa', 'location' => 'Coffee Shop'],
            ['name' => 'JIKONI', 'type' => DB::getDriverName() === 'sqlite' ? 'food' : 'jikoni', 'location' => 'Kitchen'],
        ];

        foreach ($defaultCounters as $counter) {
            if (!DB::table('counters')->where('name', $counter['name'])->exists()) {
                DB::table('counters')->insert(array_merge($counter, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // Note: Payment method updates will be handled at application level
        // We'll standardize to: CASH, MOBILE_MONEY (LIPA NAMBA), BANK, BALANCE
        // The database will store these as: cash, mobile_money, bank, balance
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove default counters
        DB::table('counters')->whereIn('name', ['CHAKULA', 'KAHAWA', 'JIKONI'])->delete();

        // Revert counter types if not SQLite
        if (DB::getDriverName() !== 'sqlite') {
            try {
                DB::statement("ALTER TABLE counters MODIFY COLUMN type ENUM('food', 'beverage', 'equipment', 'general') DEFAULT 'general'");
            } catch (\Exception $e) {
                // If enum update fails, continue
            }
        }
    }
};
