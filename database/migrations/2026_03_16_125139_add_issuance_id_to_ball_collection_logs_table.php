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
        Schema::table('ball_collection_logs', function (Blueprint $table) {
            $table->foreignId('ball_transaction_id')->nullable()->constrained('ball_transactions')->onDelete('cascade')->after('collector_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ball_collection_logs', function (Blueprint $table) {
            $table->dropForeign(['ball_transaction_id']);
            $table->dropColumn('ball_transaction_id');
        });
    }
};
