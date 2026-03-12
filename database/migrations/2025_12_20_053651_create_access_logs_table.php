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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_id')->constrained('entry_gates')->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->string('card_number');
            $table->string('member_name');
            $table->enum('access_type', ['entry', 'exit'])->default('entry');
            $table->enum('status', ['success', 'denied', 'pending'])->default('pending');
            $table->string('denial_reason')->nullable();
            $table->decimal('member_balance', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['gate_id', 'created_at']);
            $table->index(['member_id', 'created_at']);
            $table->index(['card_number', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
