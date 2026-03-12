<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('module'); // payments, golf-services, services, etc.
            $table->string('action'); // created, updated, deleted, completed, etc.
            $table->string('entity_type')->nullable(); // Member, Order, Transaction, etc.
            $table->unsignedBigInteger('entity_id')->nullable(); // ID of the entity
            $table->string('description'); // Human-readable description
            $table->json('data')->nullable(); // Additional data (amounts, quantities, etc.)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['module', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
