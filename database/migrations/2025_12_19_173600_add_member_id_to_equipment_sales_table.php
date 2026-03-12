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
        Schema::table('equipment_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment_sales', 'member_id')) {
                $table->foreignId('member_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_sales', function (Blueprint $table) {
            if (Schema::hasColumn('equipment_sales', 'member_id')) {
                $table->dropForeign(['member_id']);
                $table->dropColumn('member_id');
            }
        });
    }
};


