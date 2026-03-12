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
        if (!Schema::hasColumn('entry_gates', 'requires_card')) {
            Schema::table('entry_gates', function (Blueprint $table) {
                $table->boolean('requires_card')->default(false)->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entry_gates', function (Blueprint $table) {
            $table->dropColumn('requires_card');
        });
    }
};
