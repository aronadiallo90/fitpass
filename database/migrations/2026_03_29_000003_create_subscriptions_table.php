<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('restrict');
            $table->foreignUuid('plan_id')->constrained('subscription_plans')->onDelete('restrict');
            $table->string('reference')->unique(); // FIT-2026-00001
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->integer('amount_fcfa');
            $table->integer('checkins_remaining')->nullable(); // null = illimité
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
