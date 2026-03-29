<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_checkins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('restrict');
            $table->foreignUuid('gym_id')->constrained()->onDelete('restrict');
            $table->foreignUuid('subscription_id')->constrained()->onDelete('restrict');
            $table->enum('status', ['valid', 'invalid', 'expired'])->default('valid');
            $table->string('failure_reason')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('gym_id');
            $table->index(['user_id', 'gym_id']);
            $table->index('created_at');
            // Anti-doublon journalier géré en applicatif (CheckinService + transaction)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_checkins');
    }
};
