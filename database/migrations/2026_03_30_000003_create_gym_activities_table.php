<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tags activités normalisés — partagés entre toutes les salles
        Schema::create('gym_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();           // "Musculation"
            $table->string('slug')->unique();           // "muscu"
            $table->string('icon', 10)->nullable();     // emoji : "🏋️"
            $table->timestamps();
        });

        // Pivot many-to-many gyms ↔ gym_activities
        Schema::create('gym_activity', function (Blueprint $table) {
            $table->foreignUuid('gym_id')->constrained('gyms')->onDelete('cascade');
            $table->foreignUuid('gym_activity_id')->constrained('gym_activities')->onDelete('cascade');
            $table->primary(['gym_id', 'gym_activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_activity');
        Schema::dropIfExists('gym_activities');
    }
};
