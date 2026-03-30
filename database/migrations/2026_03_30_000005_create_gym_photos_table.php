<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('gym_id')->constrained('gyms')->onDelete('cascade');
            $table->string('cloudinary_url');           // URL optimisée Cloudinary
            $table->string('cloudinary_public_id');     // Pour suppression via API
            $table->unsignedTinyInteger('display_order')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->timestamps();

            $table->index('gym_id');
            $table->index(['gym_id', 'is_cover']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_photos');
    }
};
