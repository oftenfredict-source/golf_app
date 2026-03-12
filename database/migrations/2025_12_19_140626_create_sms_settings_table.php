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
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('sender_name')->default('GolfClub');
            $table->string('api_url')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
        
        // Insert default settings
        DB::table('sms_settings')->insert([
            'username' => 'emcatechn',
            'password' => 'Emca@#12',
            'sender_name' => 'OfisiLink',
            'api_url' => 'https://messaging-service.co.tz/link/sms/v1/text/single',
            'enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};
