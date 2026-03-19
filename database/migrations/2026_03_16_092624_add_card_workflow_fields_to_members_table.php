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
        Schema::table('members', function (Blueprint $table) {
            $table->string('card_status')->default('pending_design')->after('is_card_issued');
            $table->timestamp('card_design_at')->nullable()->after('card_status');
            $table->timestamp('card_ready_at')->nullable()->after('card_design_at');
            $table->timestamp('card_issued_at')->nullable()->after('card_ready_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['card_status', 'card_design_at', 'card_ready_at', 'card_issued_at']);
        });
    }
};
