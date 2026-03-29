<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gyms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('owner_id')->constrained('users')->onDelete('restrict');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->json('activities')->nullable();     // ['muscu','cardio','yoga',...]
            $table->json('opening_hours')->nullable();  // {'lun':'7h-22h', ...}
            $table->string('phone', 20)->nullable();
            $table->string('photo_url')->nullable();
            $table->string('api_token', 64)->unique()->nullable(); // pour bornes autonomes
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gyms');
    }
};
