<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Renommage pour rendre le stockage photos agnostique (local ou Cloudinary)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_photos', function (Blueprint $table) {
            $table->renameColumn('cloudinary_url', 'photo_url');
            $table->renameColumn('cloudinary_public_id', 'photo_storage_key');
        });

        Schema::table('gym_photos', function (Blueprint $table) {
            $table->string('photo_storage_key')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('gym_photos', function (Blueprint $table) {
            $table->string('photo_storage_key')->nullable(false)->change();
            $table->renameColumn('photo_storage_key', 'cloudinary_public_id');
            $table->renameColumn('photo_url', 'cloudinary_url');
        });
    }
};
