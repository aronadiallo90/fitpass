<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('gym_id')->constrained('gyms')->onDelete('cascade');
            $table->string('name');                         // "Yoga du matin"
            $table->text('description')->nullable();
            // Horaires par jour : {"lundi": ["07:00", "18:00"], "mercredi": ["07:00"]}
            $table->json('schedule')->nullable();
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedSmallInteger('max_spots')->nullable(); // null = illimité
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('gym_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_programs');
    }
};
