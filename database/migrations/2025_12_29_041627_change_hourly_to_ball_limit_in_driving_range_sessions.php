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
        // Add ball_limit_price and balls_limit_per_session to config table
        Schema::table('driving_range_config', function (Blueprint $table) {
            if (!Schema::hasColumn('driving_range_config', 'ball_limit_price')) {
                $table->decimal('ball_limit_price', 12, 2)->default(5000)->after('hourly_rate');
            }
            if (!Schema::hasColumn('driving_range_config', 'balls_limit_per_session')) {
                $table->integer('balls_limit_per_session')->default(50)->after('ball_limit_price');
            }
        });

        // Copy hourly_rate to ball_limit_price in config if it doesn't exist
        DB::table('driving_range_config')
            ->whereNull('ball_limit_price')
            ->orWhere('ball_limit_price', 0)
            ->update([
                'ball_limit_price' => DB::raw('COALESCE(hourly_rate, 5000)'),
                'balls_limit_per_session' => DB::raw('COALESCE(balls_limit_per_session, 50)')
            ]);

        // Add balls_limit_allowed field to sessions table to track the ball limit for each session
        Schema::table('driving_range_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('driving_range_sessions', 'balls_limit_allowed')) {
                $table->integer('balls_limit_allowed')->nullable()->after('buckets_count');
            }
        });

        // For MySQL/PostgreSQL, update the enum to replace 'hourly' with 'ball_limit'
        if (DB::getDriverName() !== 'sqlite') {
            try {
                // First update existing data
                DB::table('driving_range_sessions')
                    ->where('session_type', 'hourly')
                    ->update(['session_type' => 'ball_limit']);
                
                // Then alter the enum
                DB::statement("ALTER TABLE driving_range_sessions MODIFY COLUMN session_type ENUM('ball_limit', 'bucket', 'unlimited') DEFAULT 'ball_limit'");
            } catch (\Exception $e) {
                // If enum update fails, continue (might already be updated or not supported)
            }
        } else {
            // For SQLite, just update the data - enum is handled at application level
            DB::table('driving_range_sessions')
                ->where('session_type', 'hourly')
                ->update(['session_type' => 'ball_limit']);
        }

        // Update existing 'hourly' sessions to 'ball_limit' and set balls_limit_allowed if not set
        $defaultBallsLimit = DB::table('driving_range_config')->value('balls_limit_per_session') ?? 50;
        DB::table('driving_range_sessions')
            ->whereNull('balls_limit_allowed')
            ->update(['balls_limit_allowed' => $defaultBallsLimit]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update existing 'ball_limit' sessions back to 'hourly'
        DB::table('driving_range_sessions')
            ->where('session_type', 'ball_limit')
            ->update(['session_type' => 'hourly']);

        // For MySQL/PostgreSQL, update the enum back to 'hourly'
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE driving_range_sessions MODIFY COLUMN session_type ENUM('hourly', 'bucket', 'unlimited') DEFAULT 'hourly'");
        }

        // Remove added columns
        Schema::table('driving_range_sessions', function (Blueprint $table) {
            $table->dropColumn('balls_limit_allowed');
        });

        Schema::table('driving_range_config', function (Blueprint $table) {
            $table->dropColumn(['ball_limit_price', 'balls_limit_per_session']);
        });
    }
};
