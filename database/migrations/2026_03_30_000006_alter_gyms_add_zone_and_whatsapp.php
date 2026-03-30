<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->enum('zone', [
                'Plateau',
                'Almadies',
                'Mermoz',
                'Parcelles',
                'Guédiawaye',
                'Thiès',
                'Autre',
            ])->nullable()->after('address');

            $table->string('phone_whatsapp', 20)->nullable()->after('phone');

            $table->index('zone');
        });
    }

    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->dropIndex(['zone']);
            $table->dropColumn(['zone', 'phone_whatsapp']);
        });
    }
};
