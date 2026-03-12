<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Uid\Ulid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Models that use route binding and need ULID
        $tables = [
            'members',
            'driving_range_sessions',
            'equipment',
            'equipment_rentals',
            'equipment_sales',
            'orders',
            'transactions',
            'topups',
            'menu_categories',
            'counters',
            'entry_gates',
            'activity_logs',
        ];

        $isSqlite = DB::getDriverName() === 'sqlite';
        
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $isSqlite) {
                    if (!Schema::hasColumn($tableName, 'ulid')) {
                        // SQLite doesn't support 'after()', so we add the column without it
                        if ($isSqlite) {
                            $table->string('ulid', 26)->nullable()->unique();
                        } else {
                            $table->string('ulid', 26)->nullable()->unique()->after('id');
                        }
                    }
                });

                // Generate ULIDs for existing records
                DB::table($tableName)->whereNull('ulid')->chunkById(100, function ($records) use ($tableName) {
                    foreach ($records as $record) {
                        DB::table($tableName)
                            ->where('id', $record->id)
                            ->update(['ulid' => (string) new Ulid()]);
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'members',
            'driving_range_sessions',
            'equipment',
            'equipment_rentals',
            'equipment_sales',
            'orders',
            'transactions',
            'topups',
            'menu_categories',
            'counters',
            'entry_gates',
            'activity_logs',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'ulid')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('ulid');
                });
            }
        }
    }
};
