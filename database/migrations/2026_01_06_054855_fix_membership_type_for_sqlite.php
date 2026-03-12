<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix SQLite CHECK constraint issue for membership_type enum.
     * SQLite doesn't support ALTER TABLE to modify CHECK constraints,
     * so we need to change the column type to string to remove the constraint.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // For SQLite, we need to recreate the table to remove the CHECK constraint
            // SQLite doesn't support ALTER TABLE to modify CHECK constraints
            // Disable foreign key checks temporarily
            DB::statement('PRAGMA foreign_keys=OFF');
            
            // Get the current table structure
            $columns = DB::select("PRAGMA table_info(members)");
            
            // Create new table with membership_type as TEXT (no CHECK constraint)
            DB::statement("
                CREATE TABLE members_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    member_id TEXT NOT NULL UNIQUE,
                    name TEXT NOT NULL,
                    email TEXT,
                    phone TEXT NOT NULL,
                    upi_id TEXT UNIQUE,
                    card_number TEXT UNIQUE,
                    membership_type TEXT DEFAULT 'standard',
                    card_color TEXT,
                    balance DECIMAL(12,2) DEFAULT 0,
                    ball_limit INTEGER,
                    show_balance BOOLEAN DEFAULT 1,
                    valid_until DATE,
                    status TEXT DEFAULT 'active',
                    photo TEXT,
                    notes TEXT,
                    ulid TEXT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ");
            
            // Copy all data from old table to new table
            DB::statement("
                INSERT INTO members_new 
                (id, member_id, name, email, phone, upi_id, card_number, membership_type, 
                 card_color, balance, ball_limit, show_balance, valid_until, status, 
                 photo, notes, ulid, created_at, updated_at)
                SELECT 
                    id, member_id, name, email, phone, upi_id, card_number, membership_type,
                    card_color, balance, ball_limit, show_balance, valid_until, status,
                    photo, notes, ulid, created_at, updated_at
                FROM members
            ");
            
            // Drop old table
            DB::statement("DROP TABLE members");
            
            // Rename new table
            DB::statement("ALTER TABLE members_new RENAME TO members");
            
            // Recreate unique indexes
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS members_member_id_unique ON members(member_id)");
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS members_upi_id_unique ON members(upi_id)");
            DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS members_card_number_unique ON members(card_number)");
            
            // Re-enable foreign keys
            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            // For MySQL/PostgreSQL, update enum if not already done
            try {
                DB::statement("ALTER TABLE members MODIFY COLUMN membership_type ENUM('standard', 'vip', 'premier', 'corporate', 'guest') DEFAULT 'standard'");
            } catch (\Exception $e) {
                // Ignore if already updated or not supported
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Revert to enum (will need to recreate table again)
            // This is complex, so we'll just note that rollback may not work perfectly
            // In practice, you'd need to recreate the enum constraint
        } else {
            try {
                DB::statement("ALTER TABLE members MODIFY COLUMN membership_type ENUM('standard', 'premium', 'vip', 'corporate', 'guest') DEFAULT 'standard'");
            } catch (\Exception $e) {
                // Ignore
            }
        }
    }
};
