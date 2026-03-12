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
        Schema::table('access_control_config', function (Blueprint $table) {
            $table->string('global_mode')->default('normal')->after('id'); // normal, open, locked, emergency
            $table->timestamp('global_mode_expires_at')->nullable()->after('global_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access_control_config', function (Blueprint $table) {
            $table->dropColumn(['global_mode', 'global_mode_expires_at']);
        });
    }
};
