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
        // Check if columns already exist before adding them
        Schema::table('ball_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('ball_transactions', 'member_id')) {
                $table->foreignId('member_id')->nullable()->after('customer_phone')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('ball_transactions', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('ball_transactions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ball_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('ball_transactions', 'member_id')) {
                $table->dropForeign(['member_id']);
                $table->dropColumn('member_id');
            }
            if (Schema::hasColumn('ball_transactions', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('ball_transactions', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};
