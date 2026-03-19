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
            // Drop by array notation (Laravel handles name)
            $table->dropForeign(['assigned_by']);
            
            // Make it nullable and set null on delete
            $table->unsignedBigInteger('assigned_by')->nullable()->change();
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ball_collection_logs', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
            $table->foreign('assigned_by')->references('id')->on('users');
        });
    }
};
