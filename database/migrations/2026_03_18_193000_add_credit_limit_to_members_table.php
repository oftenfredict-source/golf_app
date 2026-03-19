<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->decimal('credit_limit', 15, 2)->default(0)->after('balance');
        });

        // Set default credit limits based on membership type
        DB::table('members')->where('membership_type', 'standard')->update(['credit_limit' => 10000]);
        DB::table('members')->where('membership_type', 'vip')->update(['credit_limit' => 100000]);
        DB::table('members')->whereIn('membership_type', ['premier', 'gold'])->update(['credit_limit' => 200000]);
        DB::table('members')->where('membership_type', 'corporate')->update(['credit_limit' => 500000]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('credit_limit');
        });
    }
};
