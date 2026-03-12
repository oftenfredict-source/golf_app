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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'table_id')) {
                $table->foreignId('table_id')->nullable()->constrained('tables')->after('table_number');
            }
            if (!Schema::hasColumn('orders', 'is_vip')) {
                $table->boolean('is_vip')->default(false)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'counter_id')) {
                $table->foreignId('counter_id')->nullable()->constrained('counters')->after('is_vip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['table_id']);
            $table->dropColumn(['table_id', 'is_vip', 'counter_id']);
        });
    }
};
