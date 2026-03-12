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
        Schema::table('members', function (Blueprint $table) {
            // Add new fields for advanced card features (only if they don't exist)
            if (!Schema::hasColumn('members', 'ball_limit')) {
                $table->integer('ball_limit')->nullable()->after('balance'); // Limit number of balls
            }
            if (!Schema::hasColumn('members', 'show_balance')) {
                $table->boolean('show_balance')->default(true)->after('ball_limit'); // Show balance on card
            }
            if (!Schema::hasColumn('members', 'card_color')) {
                $table->string('card_color')->nullable()->after('membership_type'); // Card color: silver, black, gold
            }
        });

        // Update membership types: standard -> standard (silver), vip -> vip (black), add premier (gold)
        // For SQLite, we'll handle enum changes at the application level, not database level
        // For MySQL/PostgreSQL, update enum values
        if (DB::getDriverName() !== 'sqlite') {
            try {
                // Update enum values to include 'premier' instead of 'premium'
                DB::statement("ALTER TABLE members MODIFY COLUMN membership_type ENUM('standard', 'vip', 'premier', 'corporate', 'guest') DEFAULT 'standard'");
            } catch (\Exception $e) {
                // If enum update fails, continue (might already be updated or not supported)
            }
        }

        // Update existing members to set card colors based on membership type
        // Handle premium -> premier conversion carefully for SQLite
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, we update card colors first, then handle premium->premier at app level
            DB::table('members')->where('membership_type', 'standard')->update(['card_color' => 'silver']);
            DB::table('members')->where('membership_type', 'vip')->update(['card_color' => 'black']);
            // For premium members, update card color but keep membership_type (will be handled in model/controller)
            DB::table('members')->where('membership_type', 'premium')->update(['card_color' => 'gold']);
            DB::table('members')->where('membership_type', 'corporate')->update(['card_color' => 'blue']);
            DB::table('members')->where('membership_type', 'guest')->update(['card_color' => 'gray']);
        } else {
            // For MySQL/PostgreSQL, update both membership_type and card_color
            DB::table('members')->where('membership_type', 'standard')->update(['card_color' => 'silver']);
            DB::table('members')->where('membership_type', 'vip')->update(['card_color' => 'black']);
            DB::table('members')->where('membership_type', 'premium')->update(['membership_type' => 'premier', 'card_color' => 'gold']);
            DB::table('members')->where('membership_type', 'corporate')->update(['card_color' => 'blue']);
            DB::table('members')->where('membership_type', 'guest')->update(['card_color' => 'gray']);
        }
        
        // Set default card colors for any null values
        DB::table('members')->whereNull('card_color')->update(['card_color' => 'silver']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['ball_limit', 'show_balance', 'card_color']);
        });

        // Revert membership types if not SQLite
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE members MODIFY COLUMN membership_type ENUM('standard', 'premium', 'vip', 'corporate', 'guest') DEFAULT 'standard'");
        }
    }
};
