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
            $table->integer('target_quantity')->default(0)->after('collector_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ball_collection_logs', function (Blueprint $table) {
            $table->dropColumn('target_quantity');
        });
    }
};
