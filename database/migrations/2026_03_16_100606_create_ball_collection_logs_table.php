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
        Schema::create('ball_collection_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collector_id')->constrained('ball_collectors');
            $table->integer('quantity_collected')->default(0);
            $table->string('status')->default('pending'); // pending, verified
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamp('collected_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ball_collection_logs');
    }
};
